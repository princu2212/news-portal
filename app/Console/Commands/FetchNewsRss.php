<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\District;
use App\Models\NewsArticle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchNewsRss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch-rss {--dry-run : Output feed items without writing to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from RSS feeds and APIs of CMO Chhattisgarh, Jansampark DPR, IBC24, Vistaar News, and DPRCG';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info("Dry run enabled. No changes will be written to the database.\n");
        }

        $feeds = [
            [
                'name' => 'CMO Chhattisgarh RSS',
                'url' => 'https://news.google.com/rss/search?q=site:cmo.cg.gov.in&hl=hi&gl=IN&ceid=IN:hi',
                'author' => 'मुख्यमंत्री कार्यालय छत्तीसगढ़ (CMO CG)',
                'default_source' => 'https://cmo.cg.gov.in/'
            ],
            [
                'name' => 'Jansampark DPR News Portal RSS',
                'url' => 'https://news.google.com/rss/search?q=site:jansampark.cg.gov.in&hl=hi&gl=IN&ceid=IN:hi',
                'author' => 'छत्तीसगढ़ जनसंपर्क (Jansampark DPR)',
                'default_source' => 'https://jansampark.cg.gov.in/dprnewsportal/'
            ],
            [
                'name' => 'DPRCG Portal RSS',
                'url' => 'https://news.google.com/rss/search?q=site:dprcg.gov.in&hl=hi&gl=IN&ceid=IN:hi',
                'author' => 'छत्तीसगढ़ जनसंपर्क (DPRCG)',
                'default_source' => 'https://dprcg.gov.in/'
            ],
            [
                'name' => 'IBC24',
                'url' => 'https://www.ibc24.in/feed',
                'author' => 'IBC24 न्यूज़',
                'default_source' => 'https://www.ibc24.in/'
            ],
            [
                'name' => 'Vistaar News',
                'url' => 'https://vistaarnews.com/feed',
                'author' => 'Vistaar NEWS',
                'default_source' => 'https://vistaarnews.com/'
            ]
        ];

        // Cache categories and districts to minimize queries
        $categories = Category::all()->keyBy('slug');
        $districts = District::all();

        if ($categories->isEmpty()) {
            $this->error('No categories found in the database. Please seed the database first.');
            return 1;
        }

        $totalProcessed = 0;
        $totalImported = 0;
        $totalSkipped = 0;

        // 1. Fetch high-fidelity articles directly from CMO Chhattisgarh Official Portal API
        $this->fetchCmoDirectArticles($dryRun, $categories, $districts, $totalProcessed, $totalImported, $totalSkipped);

        // 2. Fetch standard RSS feeds
        foreach ($feeds as $feed) {
            $this->info("Fetching feed from: {$feed['name']} ({$feed['url']})...");

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/xml,text/xml,*/*',
                ])->withoutVerifying()->timeout(15)->get($feed['url']);

                if ($response->failed()) {
                    $this->error("Failed to fetch feed: HTTP Code " . $response->status());
                    continue;
                }

                $body = $response->body();
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);

                if (!$xml) {
                    $this->error("Failed to parse XML response.");
                    foreach (libxml_get_errors() as $error) {
                        $this->error("XML Error: " . trim($error->message));
                    }
                    libxml_clear_errors();
                    continue;
                }

                $items = $xml->channel->item ?? [];
                $count = 0;

                foreach ($items as $item) {
                    $totalProcessed++;

                    // 1. Identify Unique ID (GUID or Link)
                    $guid = (string)$item->guid;
                    if (empty($guid)) {
                        $guid = (string)$item->link;
                    }
                    if (empty($guid)) {
                        $guid = md5((string)$item->title);
                    }

                    $originalLink = (string)$item->link;
                    if (empty($originalLink) && isset($feed['default_source'])) {
                        $originalLink = $feed['default_source'];
                    }

                    // 2. Extract and clean Title
                    $title = (string)$item->title;
                    // Strip common publisher suffixes in RSS headlines
                    $title = preg_replace('/\s*-\s*Jansampark\s*CG\s*.*$/iu', '', $title);
                    $title = preg_replace('/\s*-\s*Chief\s*Minister\s*Office\s*.*$/iu', '', $title);
                    $title = preg_replace('/\s*-\s*DPR\s*CG\s*.*$/iu', '', $title);
                    $title = preg_replace('/\s*-\s*Vistaar\s+.*$/iu', '', $title);
                    $title = preg_replace('/\s*-\s*IBC24\s*.*$/iu', '', $title);
                    $title = trim($title);

                    if (empty($title) || in_array(mb_strtolower($title, 'UTF-8'), [
                        'dpr cg',
                        'jansampark cg',
                        'jansampark',
                        'jansampark.cg.gov.in',
                        'cmo.cg.gov.in',
                        'chief minister office',
                        'chief minister office - chhattisgarh',
                        'chief minister office - government of chhattisgarh',
                        'ibc24',
                        'vistaar news'
                    ]) || mb_strlen($title, 'UTF-8') < 8) {
                        continue;
                    }

                    // 3. Generate Transliterated Slug
                    $slug = Str::slug($title);
                    if (empty($slug)) {
                        $slug = Str::slug(Str::ascii($title));
                    }

                    // 4. Duplicate Check
                    $exists = NewsArticle::where('rss_guid', $guid)
                        ->orWhere('slug', $slug)
                        ->exists();

                    if ($exists) {
                        $totalSkipped++;
                        continue;
                    }

                    // 5. Parse Date
                    $pubDateStr = (string)$item->pubDate;
                    $publishedAt = $pubDateStr ? Carbon::parse($pubDateStr) : Carbon::now();

                    // 6. Extract Image URL
                    $namespaces = $item->getNameSpaces(true);
                    $imageUrl = $this->extractImageUrl($item, $namespaces);

                    // 7. Parse Content & Summary
                    $rawContent = '';
                    if (isset($namespaces['content'])) {
                        $rawContent = (string)$item->children($namespaces['content'])->encoded;
                    }
                    if (empty($rawContent)) {
                        $rawContent = (string)$item->description;
                    }

                    // Convert HTML paragraphs and line breaks to proper newlines
                    $cleanContent = preg_replace('/<\/p>/i', "\n\n", $rawContent);
                    $cleanContent = preg_replace('/<br\s*\/?>/i', "\n", $cleanContent);
                    $cleanContent = strip_tags($cleanContent);
                    $cleanContent = html_entity_decode($cleanContent, ENT_QUOTES, 'UTF-8');
                    $cleanContent = preg_replace('/\n{3,}/', "\n\n", $cleanContent);
                    $cleanContent = trim($cleanContent);

                    // For Google News and short feeds, provide default text if empty
                    if (empty($cleanContent)) {
                        $cleanContent = $title . "\n\nअधिक जानकारी के लिए मूल खबर की लिंक पर जाएं।";
                    }

                    // Generate a brief summary snippet
                    $summary = (string)$item->description;
                    $summary = strip_tags($summary);
                    $summary = html_entity_decode($summary, ENT_QUOTES, 'UTF-8');
                    $summary = trim(preg_replace('/\s+/', ' ', $summary));
                    if (empty($summary)) {
                        $summary = Str::limit($cleanContent, 180, '...');
                    } else {
                        $summary = Str::limit($summary, 180, '...');
                    }

                    // 8. Auto-Categorize based on Title & Content keywords
                    $category = $this->determineCategory($title, $cleanContent, $item, $categories);

                    // 9. Auto-detect District association
                    $district = $this->determineDistrict($title, $cleanContent, $districts);

                    $count++;
                    $totalImported++;

                    if ($dryRun) {
                        $this->info("  [DRY RUN] Would import: \"{$title}\"");
                        $this->line("    - Slug: {$slug}");
                        $this->line("    - Category: {$category->name} ({$category->slug})");
                        $this->line("    - District: " . ($district ? "{$district->name} ({$district->slug})" : "None"));
                        $this->line("    - Image: " . ($imageUrl ?: "None"));
                        $this->line("    - Date: " . $publishedAt->toDateTimeString());
                        $this->line("    - Summary: " . Str::limit($summary, 80));
                        $this->line("    - Original Link: " . $originalLink);
                        $this->line("");
                    } else {
                        NewsArticle::create([
                            'category_id' => $category->id,
                            'district_id' => $district ? $district->id : null,
                            'title' => $title,
                            'slug' => $slug,
                            'summary' => $summary,
                            'content' => $cleanContent,
                            'image_url' => $imageUrl,
                            'author_name' => $feed['author'],
                            'source_url' => $originalLink,
                            'rss_guid' => $guid,
                            'published_at' => $publishedAt,
                            'is_featured' => false,
                            'is_breaking' => false,
                            'views' => 0
                        ]);
                    }
                }

                $this->info("Completed {$feed['name']}: Imported {$count} new articles.");
            } catch (\Exception $e) {
                $this->error("An exception occurred while processing {$feed['name']}: " . $e->getMessage());
            }
        }

        $this->info("\n--- Import Summary ---");
        $this->info("Processed: {$totalProcessed} items");
        $this->info("Imported: {$totalImported}");
        $this->info("Skipped (duplicates): {$totalSkipped}");

        return Command::SUCCESS;
    }

    /**
     * Direct collector for Chief Minister Office (cmo.cg.gov.in) portal articles.
     */
    private function fetchCmoDirectArticles($dryRun, $categories, $districts, &$totalProcessed, &$totalImported, &$totalSkipped)
    {
        $this->info("Fetching official updates from CMO Chhattisgarh Portal API (https://cmo.cg.gov.in/)...");

        try {
            $payload = [
                'listType' => 1,
                'categoryId' => 2, // News Updates
                'statusCode' => 3, // Verified
                'pageNo' => 1,
                'pageSize' => 20,
                'orderBy' => 2,
                'sortOrder' => 'DESC',
                'eventDateTimeStart' => '',
                'eventDateTimeEnd' => '',
                'filterKey' => '',
                'isPublic' => 1,
                'isActive' => 1
            ];

            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->withoutVerifying()->timeout(15)->post('https://cmo.cg.gov.in/cmocgmainapi/api/ArticleDetails/GetArticleDetailsList', $payload);

            if ($response->failed()) {
                $this->warn("CMO API returned status " . $response->status());
                return;
            }

            $data = $response->json();
            $articles = $data['tables'][0] ?? [];
            $count = 0;

            foreach ($articles as $art) {
                $totalProcessed++;

                $articleId = $art['articleId'] ?? null;
                $guid = 'cmo-' . $articleId;
                $title = trim($art['subjectHindi'] ?? $art['subjectEnglish'] ?? '');

                if (empty($title)) {
                    continue;
                }

                $slug = Str::slug($title);
                if (empty($slug)) {
                    $slug = Str::slug(Str::ascii($title));
                }

                $exists = NewsArticle::where('rss_guid', $guid)
                    ->orWhere('slug', $slug)
                    ->exists();

                if ($exists) {
                    $totalSkipped++;
                    continue;
                }

                $publishedAtStr = $art['creationDate'] ?? $art['publishDate'] ?? null;
                $publishedAt = $publishedAtStr ? Carbon::parse($publishedAtStr) : Carbon::now();

                // Extract high-res featured cover image from uploadFiles
                $imageUrl = null;
                $htmlFilePath = null;
                $uploadFiles = json_decode($art['uploadFiles'] ?? '[]', true);

                if (is_array($uploadFiles)) {
                    foreach ($uploadFiles as $file) {
                        // docType 1 is image (imageSize 3 is Large, 2 is Medium, 1 is Small)
                        if (($file['docType'] ?? null) == 1 && empty($imageUrl)) {
                            if (!empty($file['filePath'])) {
                                $imageUrl = 'https://cmo.cg.gov.in' . $file['filePath'];
                            }
                        }
                        // docType 4 is Hindi HTML content
                        if (($file['docType'] ?? null) == 4 && !empty($file['filePath'])) {
                            $htmlFilePath = 'https://cmo.cg.gov.in' . $file['filePath'];
                        }
                    }
                }

                // Fetch rich Hindi HTML body if available
                $cleanContent = '';
                if ($htmlFilePath) {
                    try {
                        $htmlRes = Http::withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        ])->withoutVerifying()->timeout(8)->get($htmlFilePath);

                        if ($htmlRes->successful()) {
                            $rawBody = $htmlRes->body();
                            $cleanContent = preg_replace('/<\/p>/i', "\n\n", $rawBody);
                            $cleanContent = preg_replace('/<br\s*\/?>/i', "\n", $cleanContent);
                            $cleanContent = strip_tags($cleanContent);
                            $cleanContent = html_entity_decode($cleanContent, ENT_QUOTES, 'UTF-8');
                            $cleanContent = preg_replace('/\n{3,}/', "\n\n", $cleanContent);
                            $cleanContent = trim($cleanContent);
                        }
                    } catch (\Exception $e) {
                        // Fallback below
                    }
                }

                if (empty($cleanContent)) {
                    $cleanContent = trim($art['shortDescriptionHindi'] ?? $art['shortDescriptionEnglish'] ?? '');
                }
                if (empty($cleanContent)) {
                    $cleanContent = $title . "\n\nअधिक जानकारी के लिए मुख्यमंत्री कार्यालय छत्तीसगढ़ के आधिकारिक पोर्टल पर जाएं।";
                }

                $summary = trim($art['shortDescriptionHindi'] ?? $art['shortDescriptionEnglish'] ?? '');
                if (empty($summary)) {
                    $summary = Str::limit($cleanContent, 180, '...');
                } else {
                    $summary = Str::limit(strip_tags(html_entity_decode($summary, ENT_QUOTES, 'UTF-8')), 180, '...');
                }

                $category = $this->determineCategory($title, $cleanContent, null, $categories);
                $district = $this->determineDistrict($title, $cleanContent, $districts);
                $sourceUrl = 'https://cmo.cg.gov.in/news/' . Str::slug($title) . '/' . $articleId;

                $count++;
                $totalImported++;

                if ($dryRun) {
                    $this->info("  [DRY RUN - CMO API] Would import: \"{$title}\"");
                    $this->line("    - GUID: {$guid}");
                    $this->line("    - Category: {$category->name} ({$category->slug})");
                    $this->line("    - District: " . ($district ? "{$district->name} ({$district->slug})" : "None"));
                    $this->line("    - Image: " . ($imageUrl ?: "None"));
                    $this->line("    - Date: " . $publishedAt->toDateTimeString());
                    $this->line("    - Summary: " . Str::limit($summary, 80));
                    $this->line("    - Source URL: " . $sourceUrl);
                    $this->line("");
                } else {
                    NewsArticle::create([
                        'category_id' => $category->id,
                        'district_id' => $district ? $district->id : null,
                        'title' => $title,
                        'slug' => $slug,
                        'summary' => $summary,
                        'content' => $cleanContent,
                        'image_url' => $imageUrl,
                        'author_name' => 'मुख्यमंत्री कार्यालय छत्तीसगढ़ (CMO CG)',
                        'source_url' => $sourceUrl,
                        'rss_guid' => $guid,
                        'published_at' => $publishedAt,
                        'is_featured' => false,
                        'is_breaking' => false,
                        'views' => 0
                    ]);
                }
            }

            $this->info("Completed CMO Official Portal: Imported {$count} new articles.");
        } catch (\Exception $e) {
            $this->error("An exception occurred while processing CMO Portal API: " . $e->getMessage());
        }
    }

    /**
     * Extract featured image URL from RSS XML structure or embedded HTML.
     */
    private function extractImageUrl($item, $namespaces)
    {
        // Check standard enclosure
        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
            return (string)$item->enclosure['url'];
        }

        // Check specific image element (common in WordPress RSS)
        if (isset($item->image) && !empty((string)$item->image)) {
            return (string)$item->image;
        }

        // Check media namespace elements
        if (isset($namespaces['media'])) {
            $media = $item->children($namespaces['media']);
            if (isset($media->content) && isset($media->content->attributes()->url)) {
                return (string)$media->content->attributes()->url;
            }
            if (isset($media->thumbnail) && isset($media->thumbnail->attributes()->url)) {
                return (string)$media->thumbnail->attributes()->url;
            }
        }

        // Fallback: search for first img src in content:encoded
        if (isset($namespaces['content'])) {
            $content = (string)$item->children($namespaces['content'])->encoded;
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    private function determineCategory($title, $content, $item, $categories)
    {
        // 1. Strip related news footer links/widgets common in RSS descriptions
        $rawText = $title . ' ' . $content;
        $pattern = '/(ये खबर भी|यह भी|ये भी|खबर भी|और पढ़ें|और पढें|और पढ़े|और पढे|read also|also read).*?(पढ़ें|पढ़ें|पढ़े|पढे|पढ़|पढ|पठें|पठे|read|also).*$/ius';
        $cleanedText = preg_replace($pattern, '', $rawText);
        $text = mb_strtolower($cleanedText, 'UTF-8');

        // Extract feed category tags
        $itemCategories = [];
        if (isset($item->category)) {
            foreach ($item->category as $cat) {
                $itemCategories[] = mb_strtolower((string)$cat, 'UTF-8');
            }
        }

        // --- PHASE 1: Priority check on feed category tags ---
        foreach ($itemCategories as $fcat) {
            if (in_array($fcat, ['sports', 'sport', 'cricket', 'tennis', 'football', 'खेल', 'क्रीड़ा'])) {
                return $categories->get('sports') ?? $categories->get('state');
            }
            if (in_array($fcat, ['tech', 'technology', 'gadgets', 'विज्ञान', 'तकनीक', 'गैजेट', 'स्मार्टफोन'])) {
                return $categories->get('tech') ?? $categories->get('state');
            }
            if (in_array($fcat, ['business', 'finance', 'economy', 'shares', 'saving', 'investment', 'व्यापार', 'मार्केट', 'बिज़नेस', 'retirement', 'lpg', 'goyal investment', 'investment'])) {
                return $categories->get('business') ?? $categories->get('state');
            }
            if (in_array($fcat, ['politics', 'political', 'election', 'congress', 'bjp', 'राजनीति', 'चुनाव', 'अधिसूचना'])) {
                return $categories->get('politics') ?? $categories->first();
            }
            if (in_array($fcat, ['entertainment', 'cinema', 'bollywood', 'मनोरंजन', 'फिल्म', 'गीत', 'शो', 'सीरियल'])) {
                return $categories->get('entertainment') ?? $categories->get('state');
            }
            if (in_array($fcat, ['world', 'international', 'global', 'विदेश', 'दुनिया', 'दुनिया की खबर | world news in hindi'])) {
                return $categories->get('world') ?? $categories->get('state');
            }
            if (in_array($fcat, ['chhattisgarh', 'cg', 'chhatisgarh', 'raipur', 'chhattisgarh news', 'छत्तीसगढ़'])) {
                return $categories->get('chhattisgarh') ?? $categories->get('state');
            }
            if (in_array($fcat, ['madhya pradesh', 'mp', 'bhopal', 'indore', 'gwalior', 'मध्यप्रदेश'])) {
                return $categories->get('madhya-pradesh') ?? $categories->get('state');
            }
            if (in_array($fcat, ['national', 'india', 'delhi', 'mumbai', 'bihar', 'maharashtra', 'uttar pradesh', 'देश', 'राष्ट्रीय'])) {
                return $categories->get('national') ?? $categories->get('state');
            }
        }

        // --- PHASE 2: Fallback keyword matching on cleaned title + description ---
        // 1. Sports (खेल)
        if (
            Str::contains($text, ['खेल', 'क्रिकेट', 'ओलंपिक', 'FINAL मुकाबला', 'खिलाड़ी', 'शतक', 'विकेट', 'टूर्नामेंट', 'कुश्ती', 'हॉकी', 'फुटबॉल', 'हाफ सेंचुरी', 'बैडमिंटन', 'टेनिस', 'गोल्फ', 'शतरंज', 'अंपायर', 'खेलों'])
        ) {
            return $categories->get('sports') ?? $categories->get('state');
        }

        // 2. Tech (तकनीक)
        if (
            Str::contains($text, ['स्मार्टफोन', 'गैजेट', 'एंड्रॉयड', 'आईफोन', 'सॉफ्टवेयर', 'कंप्यूटर', 'लैपटॉप', 'साइबर', 'व्हाट्सएप', 'सिम कार्ड', '5g मोबाइल', 'टेलीकॉम', 'ऐप्लीकेशन', 'मशीन लर्निंग', 'डेटाबेस', 'गैजेट्स'])
        ) {
            return $categories->get('tech') ?? $categories->get('state');
        }

        // 3. Politics (राजनीति)
        if (
            Str::contains($text, ['चुनाव', 'कांग्रेस', 'भाजपा', 'बीजेपी', 'नेता', 'मंत्री', 'मुख्यमंत्री', 'विधानसभा', 'संसद', 'विपक्ष', 'सरकार', 'मोदी', 'राहुल', 'पार्टी', 'वोट', 'कैबिनेट', 'राज्यपाल', 'अधिसूचना', 'गहलोत', 'शाह', 'सोनिया'])
        ) {
            return $categories->get('politics') ?? $categories->first();
        }

        // 4. Business (व्यापार)
        if (
            Str::contains($text, ['व्यापार', 'कारोबार', 'मार्केट', 'सेंसेक्स', 'निफ्टी', 'सर्राफा', 'जीएसटी', 'बजट', 'इनकम टैक्स', 'ब्याज दर', 'बैंक', 'फाइनेंस', 'उद्योग', 'मुनाफा', 'म्युचुअल फंड', 'निवेश', 'रिटायरमेंट', 'बचत योजना', 'रेपो रेट', 'शेयर बाजार', 'सोने का भाव', 'चांदी का भाव', 'सर्राफा बाजार', 'शेयरों'])
        ) {
            return $categories->get('business') ?? $categories->get('state');
        }

        // 5. Entertainment (मनोरंजन)
        if (
            Str::contains($text, ['मनोरंजन', 'फिल्म', 'सिनेमा', 'बॉलीवुड', 'अभिनेता', 'अभिनेत्री', 'एक्टर', 'एक्ट्रेस', 'थियेटर', 'गाना', 'गीत', 'फिल्मों', 'कलाकार', 'सीरियल', 'बॉक्स ऑफिस', 'टीज़र', 'फिल्म रिलीज'])
        ) {
            return $categories->get('entertainment') ?? $categories->get('state');
        }

        // 6. World (दुनिया)
        if (
            Str::contains($text, ['दुनिया', 'विदेश', 'ग्लोबल', 'अमेरिका', 'रूस', 'यूक्रेन', 'चीन', 'पाकिस्तान', 'इंडोनेशिया', 'ईरान', 'जापान', 'मलेशिया', 'लंदन', 'वाशिंगटन', 'इजरायल', 'गाजा', 'हमास'])
        ) {
            return $categories->get('world') ?? $categories->get('state');
        }

        // 7. Chhattisgarh (छत्तीसगढ़)
        if (
            Str::contains($text, ['छत्तीसगढ़', 'बस्तर', 'चित्रकोट', 'तीरथगढ़', 'महतारी वंदन', 'राजनंदगांव', 'रायपुर', 'बिलासपुर', 'दुर्ग', 'भिलाई', 'सरगुजा', 'रायगढ़', 'धमतरी', 'कांकेर', 'कोंडागांव'])
        ) {
            return $categories->get('chhattisgarh') ?? $categories->get('state');
        }

        // 8. Madhya Pradesh (मध्यप्रदेश)
        if (
            Str::contains($text, ['मध्यप्रदेश', 'मध्य प्रदेश', 'लाडली बहना', 'भोपाल', 'इंदौर', 'ग्वालियर', 'जबलपुर', 'उज्जैन', 'शिवराज', 'मोहन यादव', 'धार जिला', 'रीवा', 'जबलपुर'])
        ) {
            return $categories->get('madhya-pradesh') ?? $categories->get('state');
        }

        // 9. National (देश)
        if (
            Str::contains($text, ['देश', 'भारत', 'इंडिया', 'दिल्ली', 'मुंबई', 'उत्तर प्रदेश', 'बिहार', 'महाराष्ट्र', 'राजस्थान', 'गुजरात', 'झारखंड', 'सुप्रीम कोर्ट', 'हाई कोर्ट', 'संसद', 'सैनिक', 'सेना'])
        ) {
            return $categories->get('national') ?? $categories->get('state');
        }
        // Default to State News (राज्य)
        return $categories->get('state') ?? $categories->first();
    }

    /**
     * Detect if the article belongs to a specific district.
     */
    private function determineDistrict($title, $content, $districts)
    {
        $text = $title . ' ' . $content;
        foreach ($districts as $district) {
            // Check if district name is in title or content
            if (mb_stripos($text, $district->name) !== false) {
                return $district;
            }
        }
        return null;
    }
}
