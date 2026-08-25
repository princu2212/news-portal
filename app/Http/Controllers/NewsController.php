<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\District;
use App\Models\NewsArticle;
use App\Models\Tag;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Share common navigation data with all views.
     */
    protected function getCommonData()
    {
        $breakingNews = NewsArticle::breaking()->orderBy('published_at', 'desc')->take(8)->get();
        if ($breakingNews->count() < 8) {
            $extra = NewsArticle::whereNotIn('id', $breakingNews->pluck('id')->toArray())
                ->orderBy('published_at', 'desc')
                ->take(8 - $breakingNews->count())
                ->get();
            $breakingNews = $breakingNews->concat($extra);
        }

        return [
            'navCategories' => Category::orderBy('id', 'asc')->get(),
            'navDistricts' => District::orderBy('name', 'asc')->get(),
            'breakingNews' => $breakingNews,
        ];
    }

    /**
     * Display the portal homepage.
     */
    public function index()
    {
        $common = $this->getCommonData();

        // Featured slider news: latest 4 articles with images to look visually premium
        $featuredNews = NewsArticle::whereNotNull('image_url')
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        if ($featuredNews->count() < 4) {
            $extraFeatured = NewsArticle::whereNotIn('id', $featuredNews->pluck('id')->toArray())
                ->orderBy('published_at', 'desc')
                ->take(4 - $featuredNews->count())
                ->get();
            $featuredNews = $featuredNews->concat($extraFeatured);
        }

        $featuredIds = $featuredNews->pluck('id')->toArray();

        // Latest press releases (excluding slider articles)
        $latestPressReleases = NewsArticle::whereNotIn('id', $featuredIds)
            ->orderBy('published_at', 'desc')
            ->take(8)
            ->get();

        // Specific category queries for homepage sections
        // 1. State News (राज्य समाचार)
        $stateCategory = Category::where('slug', 'state')->first();
        $stateNews = $stateCategory 
            ? $stateCategory->newsArticles()->orderBy('published_at', 'desc')->take(4)->get() 
            : collect();

        // 2. Entertainment News (मनोरंजन समाचार)
        $entertainmentCategory = Category::where('slug', 'entertainment')->first();
        $entertainmentNews = $entertainmentCategory 
            ? $entertainmentCategory->newsArticles()->orderBy('published_at', 'desc')->take(4)->get() 
            : collect();

        // Initial district news for the interactive district tabs (defaults to first district or Raipur)
        $selectedDistrict = District::where('slug', 'raipur')->first() ?? District::first();
        $districtNews = $selectedDistrict 
            ? $selectedDistrict->newsArticles()->orderBy('published_at', 'desc')->take(4)->get() 
            : collect();

        // Popular news by views
        $popularNews = NewsArticle::orderBy('views', 'desc')->take(5)->get();

        // Tags for trending panel
        $trendingTags = Tag::take(8)->get();

        return view('news.index', array_merge($common, [
            'featuredNews' => $featuredNews,
            'latestPressReleases' => $latestPressReleases,
            'stateNews' => $stateNews,
            'entertainmentNews' => $entertainmentNews,
            'selectedDistrict' => $selectedDistrict,
            'districtNews' => $districtNews,
            'popularNews' => $popularNews,
            'trendingTags' => $trendingTags,
        ]));
    }

    /**
     * Display a single press release / article.
     */
    public function show($slug)
    {
        $common = $this->getCommonData();

        $article = NewsArticle::with(['category', 'district', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment article views safely
        $article->increment('views');

        // Fetch related articles (same category, excluding current one)
        $relatedArticles = NewsArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $popularNews = NewsArticle::orderBy('views', 'desc')->take(5)->get();

        return view('news.show', array_merge($common, [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'popularNews' => $popularNews,
        ]));
    }

    /**
     * Display news listing for a specific category.
     */
    public function category($slug)
    {
        $common = $this->getCommonData();

        $category = Category::where('slug', $slug)->firstOrFail();
        
        $articles = NewsArticle::where('category_id', $category->id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $popularNews = NewsArticle::orderBy('views', 'desc')->take(5)->get();

        return view('news.category', array_merge($common, [
            'category' => $category,
            'articles' => $articles,
            'popularNews' => $popularNews,
        ]));
    }

    /**
     * Display news listing for a specific district.
     */
    public function district($slug)
    {
        $common = $this->getCommonData();

        $district = District::where('slug', $slug)->firstOrFail();

        $articles = NewsArticle::where('district_id', $district->id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $popularNews = NewsArticle::orderBy('views', 'desc')->take(5)->get();

        return view('news.district', array_merge($common, [
            'district' => $district,
            'articles' => $articles,
            'popularNews' => $popularNews,
        ]));
    }

    /**
     * Search articles by keywords in Hindi or English.
     */
    public function search(Request $request)
    {
        $common = $this->getCommonData();
        $query = $request->input('query');

        $articles = collect();
        if ($query) {
            $articles = NewsArticle::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%")
                ->orderBy('published_at', 'desc')
                ->paginate(12)
                ->appends(['query' => $query]);
        }

        $popularNews = NewsArticle::orderBy('views', 'desc')->take(5)->get();

        return view('news.search', array_merge($common, [
            'query' => $query,
            'articles' => $articles,
            'popularNews' => $popularNews,
        ]));
    }

    /**
     * Retrieve district news dynamically for AJax/fetch requests.
     */
    public function getDistrictNews($id)
    {
        $articles = NewsArticle::where('district_id', $id)
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        return response()->json($articles);
    }
}
