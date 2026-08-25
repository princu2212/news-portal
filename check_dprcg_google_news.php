<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$url = 'https://news.google.com/rss/search?q=site:dprcg.gov.in&hl=hi&gl=IN&ceid=IN:hi';

try {
    echo "Testing Google News RSS for dprcg.gov.in...\n";
    $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ])->timeout(10)->get($url);
    
    echo " - Status: " . $response->status() . "\n";
    echo " - Length: " . strlen($response->body()) . " bytes\n";
    if ($response->status() == 200 && strlen($response->body()) > 0) {
        $xml = simplexml_load_string($response->body());
        if ($xml) {
            echo "   -> Valid RSS! Channel Title: " . ($xml->channel->title ?? 'N/A') . "\n";
            echo "   -> First 5 items:\n";
            $count = 0;
            foreach ($xml->channel->item as $item) {
                if ($count++ >= 5) break;
                echo "      * Title: " . $item->title . "\n";
                echo "      * Link: " . $item->link . "\n";
                echo "      * Date: " . $item->pubDate . "\n";
            }
        }
    }
} catch (\Exception $e) {
    echo " - Exception: " . $e->getMessage() . "\n";
}
