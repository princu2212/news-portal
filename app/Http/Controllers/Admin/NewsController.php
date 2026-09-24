<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\District;
use App\Models\NewsArticle;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display the Admin News Feed Management / Dashboard.
     */
    public function index(Request $request)
    {
        $query = NewsArticle::with(['category', 'district'])->latest('published_at');

        // Search Filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('document_no', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // District Filter
        if ($districtId = $request->input('district_id')) {
            $query->where('district_id', $districtId);
        }

        // Breaking Filter
        if ($request->filled('is_breaking')) {
            $query->where('is_breaking', $request->boolean('is_breaking'));
        }

        // Featured Filter
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $articles = $query->paginate(15)->withQueryString();

        // Dashboard statistics
        $stats = [
            'total_articles' => NewsArticle::count(),
            'today_articles' => NewsArticle::whereDate('published_at', today())->count(),
            'breaking_count' => NewsArticle::where('is_breaking', true)->count(),
            'featured_count' => NewsArticle::where('is_featured', true)->count(),
            'total_views'    => NewsArticle::sum('views'),
            'categories'     => Category::count(),
            'districts'      => District::count(),
        ];

        $categories = Category::orderBy('name')->get();
        $districts = District::orderBy('name')->get();

        return view('admin.dashboard', compact('articles', 'stats', 'categories', 'districts'));
    }

    /**
     * Show the form for writing and uploading new news.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.news.create', compact('categories', 'districts', 'tags'));
    }

    /**
     * Store a newly created news article and upload to feed.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:500',
            'category_id'  => 'required|exists:categories,id',
            'district_id'  => 'nullable|exists:districts,id',
            'summary'      => 'nullable|string|max:1000',
            'content'      => 'required|string',
            'author_name'  => 'nullable|string|max:150',
            'document_no'  => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'is_featured'  => 'nullable|boolean',
            'is_breaking'  => 'nullable|boolean',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url'    => 'nullable|string|max:1000',
            'source_url'   => 'nullable|url|max:1000',
            'tags'         => 'nullable|string|max:500',
        ], [
            'title.required'       => 'समाचार का शीर्षक दर्ज करना अनिवार्य है।',
            'category_id.required' => 'कृपया एक श्रेणी का चयन करें।',
            'content.required'     => 'समाचार की विस्तृत सामग्री दर्ज करना अनिवार्य है।',
            'image_file.image'     => 'अपलोड की गई फ़ाइल केवल इमेज (JPG, PNG, WEBP) होनी चाहिए।',
            'image_file.max'       => 'इमेज का आकार अधिकतम 5 MB हो सकता है।',
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($validated['title']);
        if (empty($baseSlug)) {
            $baseSlug = 'news-' . time();
        }
        $slug = $baseSlug;
        $counter = 1;
        while (NewsArticle::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Handle Image Upload
        $finalImageUrl = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $finalImageUrl = 'storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $finalImageUrl = $validated['image_url'];
        }

        // Auto summary if not provided
        $summary = $validated['summary'] ?? null;
        if (empty($summary) && !empty($validated['content'])) {
            $plainText = strip_tags($validated['content']);
            $summary = Str::limit($plainText, 220);
        }

        $article = NewsArticle::create([
            'title'        => $validated['title'],
            'slug'         => $slug,
            'category_id'  => $validated['category_id'],
            'district_id'  => $validated['district_id'] ?? null,
            'summary'      => $summary,
            'content'      => $validated['content'],
            'author_name'  => !empty($validated['author_name']) ? $validated['author_name'] : (auth()->user()->name ?? 'संपादक मंडल'),
            'document_no'  => $validated['document_no'] ?? null,
            'image_url'    => $finalImageUrl,
            'source_url'   => $validated['source_url'] ?? null,
            'is_featured'  => $request->boolean('is_featured'),
            'is_breaking'  => $request->boolean('is_breaking'),
            'views'        => 0,
            'published_at' => !empty($validated['published_at']) ? \Carbon\Carbon::parse($validated['published_at']) : now(),
        ]);

        // Handle Tags
        if (!empty($request->input('tags'))) {
            $tagNames = array_filter(array_map('trim', explode(',', $request->input('tags'))));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if (empty($name)) continue;
                $tagSlug = Str::slug($name) ?: 'tag-' . Str::random(5);
                $tag = Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => $tagSlug]
                );
                $tagIds[] = $tag->id;
            }
            $article->tags()->sync($tagIds);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'समाचार सफलतापूर्वक लाइव फीड में प्रकाशित और अपलोड कर दिया गया है!');
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit($id)
    {
        $article = NewsArticle::with(['tags'])->findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        $currentTags = $article->tags->pluck('name')->implode(', ');

        return view('admin.news.edit', compact('article', 'categories', 'districts', 'tags', 'currentTags'));
    }

    /**
     * Update the specified news article in feed.
     */
    public function update(Request $request, $id)
    {
        $article = NewsArticle::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:500',
            'category_id'  => 'required|exists:categories,id',
            'district_id'  => 'nullable|exists:districts,id',
            'summary'      => 'nullable|string|max:1000',
            'content'      => 'required|string',
            'author_name'  => 'nullable|string|max:150',
            'document_no'  => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'is_featured'  => 'nullable|boolean',
            'is_breaking'  => 'nullable|boolean',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url'    => 'nullable|string|max:1000',
            'source_url'   => 'nullable|url|max:1000',
            'tags'         => 'nullable|string|max:500',
        ], [
            'title.required'       => 'समाचार का शीर्षक दर्ज करना अनिवार्य है।',
            'category_id.required' => 'कृपया एक श्रेणी का चयन करें।',
            'content.required'     => 'समाचार की विस्तृत सामग्री दर्ज करना अनिवार्य है।',
        ]);

        // If title changed, update slug
        if ($article->title !== $validated['title']) {
            $baseSlug = Str::slug($validated['title']) ?: 'news-' . time();
            $slug = $baseSlug;
            $counter = 1;
            while (NewsArticle::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $article->slug = $slug;
        }

        // Handle Image Upload / URL
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $article->image_url = 'storage/' . $path;
        } elseif ($request->filled('image_url')) {
            $article->image_url = $validated['image_url'];
        }

        // Auto summary if empty
        $summary = $validated['summary'] ?? null;
        if (empty($summary) && !empty($validated['content'])) {
            $plainText = strip_tags($validated['content']);
            $summary = Str::limit($plainText, 220);
        }

        $article->title = $validated['title'];
        $article->category_id = $validated['category_id'];
        $article->district_id = $validated['district_id'] ?? null;
        $article->summary = $summary;
        $article->content = $validated['content'];
        $article->author_name = !empty($validated['author_name']) ? $validated['author_name'] : $article->author_name;
        $article->document_no = $validated['document_no'] ?? null;
        $article->source_url = $validated['source_url'] ?? null;
        $article->is_featured = $request->boolean('is_featured');
        $article->is_breaking = $request->boolean('is_breaking');

        if (!empty($validated['published_at'])) {
            $article->published_at = \Carbon\Carbon::parse($validated['published_at']);
        }

        $article->save();

        // Handle Tags
        if ($request->has('tags')) {
            $tagNames = array_filter(array_map('trim', explode(',', (string) $request->input('tags'))));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if (empty($name)) continue;
                $tagSlug = Str::slug($name) ?: 'tag-' . Str::random(5);
                $tag = Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => $tagSlug]
                );
                $tagIds[] = $tag->id;
            }
            $article->tags()->sync($tagIds);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'समाचार सफलतापूर्वक अपडेट कर दिया गया है!');
    }

    /**
     * Remove the specified news article from the feed.
     */
    public function destroy($id)
    {
        $article = NewsArticle::findOrFail($id);
        $title = $article->title;
        $article->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'समाचार हटा दिया गया है।',
            ]);
        }

        return redirect()->back()
            ->with('success', "समाचार '{$title}' सफलतापूर्वक हटा दिया गया है।");
    }

    /**
     * Toggle Breaking News status.
     */
    public function toggleBreaking(Request $request, $id)
    {
        $article = NewsArticle::findOrFail($id);
        $article->is_breaking = !$article->is_breaking;
        $article->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_breaking' => $article->is_breaking,
                'message' => $article->is_breaking ? 'ब्रेकिंग न्यूज़ में जोड़ा गया' : 'ब्रेकिंग न्यूज़ से हटाया गया',
            ]);
        }

        return redirect()->back()->with('success', 'ब्रेकिंग स्टेटस अपडेट किया गया।');
    }

    /**
     * Toggle Featured News status.
     */
    public function toggleFeatured(Request $request, $id)
    {
        $article = NewsArticle::findOrFail($id);
        $article->is_featured = !$article->is_featured;
        $article->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_featured' => $article->is_featured,
                'message' => $article->is_featured ? 'मुख्य स्लाइडर में जोड़ा गया' : 'मुख्य स्लाइडर से हटाया गया',
            ]);
        }

        return redirect()->back()->with('success', 'फ़ीचर्ड स्टेटस अपडेट किया गया।');
    }

    /**
     * Manually trigger RSS feed fetch from admin.
     */
    public function syncRss()
    {
        try {
            Artisan::call('news:fetch-rss');
            $output = Artisan::output();
            $total = NewsArticle::count();

            return redirect()->back()->with('success', "RSS फीड सिंक संपन्न हुआ! डेटाबेस में कुल समाचार: {$total}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'RSS सिंक में त्रुटि: ' . $e->getMessage());
        }
    }
}
