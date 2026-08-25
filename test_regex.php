<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NewsArticle;

$art = NewsArticle::find(66);
if ($art) {
    $content = $art->content;
    
    // Let's test a very broad regex pattern that strips related news and footer items
    $patterns = [
        '/(ये खबर भी|यह भी|ये भी|खबर भी|और पढ़ें|और पढें|और पढ़े|और पढे).*?(पढ़ें|पढ़ें|पढ़े|पढे|पढ़|पढ|पठें|पठे)/iu',
        '/read also/i',
        '/also read/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $offset = $matches[0][1];
            echo "Pattern: $pattern matched at offset $offset!\n";
            echo "Match: " . $matches[0][0] . "\n";
            echo "Before: " . substr($content, max(0, $offset - 50), 50) . "\n";
            echo "After: " . substr($content, $offset, 150) . "\n";
            
            // Try cleaning
            $clean = preg_replace($pattern . '.*$/ius', '', $content);
            echo "Original length: " . strlen($content) . " -> Cleaned length: " . strlen($clean) . "\n";
        }
    }
}
