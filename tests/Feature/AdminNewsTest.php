<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\District;
use App\Models\NewsArticle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminNewsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;
    protected $district;

    protected function setUp(): void
    {
        parent::setUp();

        // Get or create test user
        $this->user = User::firstOrCreate(
            ['email' => 'editor@cgnewsexpress.com'],
            ['name' => 'संपादक', 'password' => bcrypt('password123')]
        );

        $this->category = Category::firstOrCreate(
            ['slug' => 'state'],
            ['name' => 'राज्य', 'color' => '#E1261C']
        );

        $this->district = District::firstOrCreate(
            ['slug' => 'raipur'],
            ['name' => 'रायपुर']
        );
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_with_valid_credentials()
    {
        $response = $this->post('/admin/login', [
            'email' => 'editor@cgnewsexpress.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_authenticated_admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->user)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('डैशबोर्ड एवं समाचार फीड');
    }

    public function test_admin_can_view_create_news_page()
    {
        $response = $this->actingAs($this->user)->get('/admin/news/create');
        $response->assertStatus(200);
        $response->assertSee('नया समाचार लिखें एवं अपलोड करें');
    }

    public function test_admin_can_write_and_upload_news_to_feed()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('headline_photo.jpg', 100, 'image/jpeg');

        $postData = [
            'title' => 'रायपुर में नया आईटी पार्क स्थापित करने की घोषणा',
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'summary' => 'युवाओं के लिए 5000 नए रोजगार अवसरों का सृजन होगा।',
            'content' => 'मुख्यमंत्री ने आज रायपुर में विश्वस्तरीय आईटी पार्क स्थापित करने की विस्तृत रूपरेखा प्रस्तुत की...',
            'author_name' => 'विशेष संवाददाता',
            'document_no' => 'PR/2026/09/99',
            'is_featured' => '1',
            'is_breaking' => '1',
            'image_file' => $file,
            'tags' => 'आईटी, रायपुर, रोजगार, विकास',
        ];

        $response = $this->actingAs($this->user)->post('/admin/news', $postData);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('news_articles', [
            'title' => 'रायपुर में नया आईटी पार्क स्थापित करने की घोषणा',
            'category_id' => $this->category->id,
            'district_id' => $this->district->id,
            'is_breaking' => 1,
            'is_featured' => 1,
        ]);
    }

    public function test_admin_can_toggle_breaking_news()
    {
        $article = NewsArticle::create([
            'title' => 'परीक्षण ब्रेकिंग खबर',
            'slug' => 'test-breaking-news-' . uniqid(),
            'category_id' => $this->category->id,
            'content' => 'विस्तृत सामग्री यहाँ है...',
            'is_breaking' => false,
        ]);

        $response = $this->actingAs($this->user)->post("/admin/news/{$article->id}/toggle-breaking");
        $this->assertTrue($article->fresh()->is_breaking);
    }

    public function test_admin_can_toggle_featured_news()
    {
        $article = NewsArticle::create([
            'title' => 'परीक्षण फ़ीचर्ड खबर',
            'slug' => 'test-featured-news-' . uniqid(),
            'category_id' => $this->category->id,
            'content' => 'विस्तृत सामग्री यहाँ है...',
            'is_featured' => false,
        ]);

        $response = $this->actingAs($this->user)->post("/admin/news/{$article->id}/toggle-featured");
        $this->assertTrue($article->fresh()->is_featured);
    }

    public function test_admin_can_update_news()
    {
        $article = NewsArticle::create([
            'title' => 'पुरानी खबर',
            'slug' => 'purani-khabar',
            'category_id' => $this->category->id,
            'content' => 'सामग्री...',
        ]);

        $response = $this->actingAs($this->user)->put("/admin/news/{$article->id}", [
            'title' => 'अपडेटेड खबर शीर्षक',
            'category_id' => $this->category->id,
            'content' => 'नयी विस्तृत सामग्री...',
            'summary' => 'नया सार...',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals('अपडेटेड खबर शीर्षक', $article->fresh()->title);
    }

    public function test_admin_can_delete_news()
    {
        $article = NewsArticle::create([
            'title' => 'हटाने योग्य खबर',
            'slug' => 'to-be-deleted',
            'category_id' => $this->category->id,
            'content' => 'सामग्री...',
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/news/{$article->id}");
        $this->assertDatabaseMissing('news_articles', ['id' => $article->id]);
    }
}
