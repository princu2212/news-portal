<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$candidates = [
    'dprcg_feed' => 'https://dprcg.gov.in/feed',
    'dprcg_rss' => 'https://dprcg.gov.in/rss',
    'dprcg_feed_slash' => 'https://dprcg.gov.in/feed/',
    'dprcg_xml' => 'https://dprcg.gov.in/feed.xml'
];

foreach ($candidates as $name => $url) {
    try {
        echo "Testing $name ($url)...\n";
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ])->timeout(10)->get($url);
        
        echo " - Status: " . $response->status() . "\n";
        echo " - Content Type: " . $response->header('Content-Type') . "\n";
        $body = $response->body();
        echo " - Length: " . strlen($body) . " bytes\n";
        if ($response->status() == 200 && strlen($body) > 0) {
            echo " - First 150 chars: " . substr($body, 0, 150) . "\n";
            $xml = simplexml_load_string($body);
            if ($xml) {
                echo "   -> Valid RSS! Channel Title: " . ($xml->channel->title ?? 'N/A') . "\n";
            }
        }
    } catch (\Exception $e) {
        echo " - Exception: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
