<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NewsArticle;
use App\Models\Category;

$business = Category::where('slug', 'business')->first();
$tech = Category::where('slug', 'tech')->first();

echo "=== BUSINESS ARTICLES IN DB ===\n";
$articles = NewsArticle::where('category_id', $business->id)->get();
foreach ($articles as $art) {
    echo "ID: {$art->id} | Title: {$art->title} | Source: {$art->rss_guid}\n";
}

echo "\n=== TECH ARTICLES IN DB ===\n";
$articles = NewsArticle::where('category_id', $tech->id)->get();
foreach ($articles as $art) {
    echo "ID: {$art->id} | Title: {$art->title} | Source: {$art->rss_guid}\n";
}
