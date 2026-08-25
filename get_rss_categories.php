<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$feeds = [
    'ibc24' => 'https://www.ibc24.in/feed',
    'vistaar' => 'https://vistaarnews.com/feed'
];

$allCategories = [];

foreach ($feeds as $name => $url) {
    try {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ])->get($url);
        
        $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml) {
            foreach ($xml->channel->item as $item) {
                if (isset($item->category)) {
                    foreach ($item->category as $cat) {
                        $catName = trim((string)$cat);
                        if (!empty($catName)) {
                            $allCategories[$name][$catName] = ($allCategories[$name][$catName] ?? 0) + 1;
                        }
                    }
                }
            }
        }
    } catch (\Exception $e) {
        echo "Error fetching $name: " . $e->getMessage() . "\n";
    }
}

echo "=== IBC24 Categories ===\n";
if (isset($allCategories['ibc24'])) {
    arsort($allCategories['ibc24']);
    foreach ($allCategories['ibc24'] as $cat => $count) {
        echo " - $cat ($count)\n";
    }
}

echo "\n=== Vistaar News Categories ===\n";
if (isset($allCategories['vistaar'])) {
    arsort($allCategories['vistaar']);
    foreach ($allCategories['vistaar'] as $cat => $count) {
        echo " - $cat ($count)\n";
    }
}
