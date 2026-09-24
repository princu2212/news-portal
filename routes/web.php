<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public News Portal Routes
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Management & News Publishing Routes (Protected by Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & News Feed Management
    Route::get('/', [AdminNewsController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminNewsController::class, 'index'])->name('dashboard.index');
    Route::get('/news', [AdminNewsController::class, 'index'])->name('news.index');

    // Write & Upload News
    Route::get('/news/create', [AdminNewsController::class, 'create'])->name('news.create');
    Route::post('/news', [AdminNewsController::class, 'store'])->name('news.store');

    // Edit & Update News
    Route::get('/news/{id}/edit', [AdminNewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{id}', [AdminNewsController::class, 'update'])->name('news.update');

    // Delete News
    Route::delete('/news/{id}', [AdminNewsController::class, 'destroy'])->name('news.destroy');

    // Quick Toggles for Breaking & Featured status
    Route::post('/news/{id}/toggle-breaking', [AdminNewsController::class, 'toggleBreaking'])->name('news.toggle-breaking');
    Route::post('/news/{id}/toggle-featured', [AdminNewsController::class, 'toggleFeatured'])->name('news.toggle-featured');

    // Trigger RSS sync directly from admin
    Route::post('/sync-rss', [AdminNewsController::class, 'syncRss'])->name('sync-rss');
});
