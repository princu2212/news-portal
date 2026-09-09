<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::get('/press-release/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/category/{slug}', [NewsController::class, 'category'])->name('news.category');
Route::get('/district/{slug}', [NewsController::class, 'district'])->name('news.district');
Route::get('/search', [NewsController::class, 'search'])->name('news.search');
Route::get('/api/district-news/{id}', [NewsController::class, 'getDistrictNews'])->name('api.district-news');

// Web-based cron trigger to manually or automatically sync RSS feeds
Route::get('/cron/fetch-rss', function () {
    \Illuminate\Support\Facades\Artisan::call('news:fetch-rss');
    $output = \Illuminate\Support\Facades\Artisan::output();
    $total = \App\Models\NewsArticle::count();
    return response("<pre style='background:#111;color:#4ade80;padding:20px;font-family:monospace;'>{$output}\n\n=== Status ===\nTotal News Articles in Database: {$total}</pre>");
})->name('cron.fetch-rss');
