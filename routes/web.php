<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::get('/press-release/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/category/{slug}', [NewsController::class, 'category'])->name('news.category');
Route::get('/district/{slug}', [NewsController::class, 'district'])->name('news.district');
Route::get('/search', [NewsController::class, 'search'])->name('news.search');
Route::get('/api/district-news/{id}', [NewsController::class, 'getDistrictNews'])->name('api.district-news');
