@extends('layouts.app')

@section('title')
    {{ $article->title }} | छत्तीसगढ़ न्यूज़ एक्सप्रेस
@endsection

@section('content')
    <!-- Print-friendly stylesheet -->
    <style>
        @media print {

            /* Hide all interface chrome */
            header,
            footer,
            nav,
            aside,
            .accessibility-bar,
            .no-print,
            #hindi-clock,
            #hindi-date,
            .animate-ticker {
                display: none !important;
            }

            main {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-header {
                display: block !important;
            }

            .print-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 13pt !important;
            }
        }

        .print-header {
            display: none;
        }
    </style>

    <div class="space-y-6">

        <!-- Breadcrumbs -->
        <nav class="text-xs text-gray-400 flex items-center space-x-1.5 no-print">
            <a href="{{ route('news.index') }}" class="hover:underline hover:text-brand-red">होम</a>
            <span>&rsaquo;</span>
            <a href="{{ route('news.category', $article->category->slug) }}"
                class="hover:underline hover:text-brand-red">{{ $article->category->name }}</a>
            <span>&rsaquo;</span>
            @if($article->district)
                <a href="{{ route('news.district', $article->district->slug) }}"
                    class="hover:underline hover:text-brand-red">{{ $article->district->name }}</a>
                <span>&rsaquo;</span>
            @endif
            <span class="text-gray-600 line-clamp-1">{{ $article->title }}</span>
        </nav>

        <!-- Sleek Print Header (Only visible during print) -->
        <div class="print-header text-center space-y-1 border-b border-brand-red pb-4">
            <h1 class="text-2xl font-black text-brand-dark">छत्तीसगढ़ न्यूज़ एक्सप्रेस</h1>
            <p class="text-xs text-gray-500">ताज़ा तरीन ख़बरें, बिना किसी पक्षपात के | cgnewsexpress.com</p>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Press Release Details (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">

                <div class="bg-white p-6 md:p-8 rounded-xl border border-gray-200 shadow-2xs print-container relative">
                    <!-- Share Floating / Sticky Widget (no-print) -->
                    <div class="absolute -left-14 top-20 hidden xl:flex flex-col space-y-3 no-print">
                        <button onclick="shareOnWhatsApp()"
                            class="w-10 h-10 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center shadow hover:scale-110 transition duration-150"
                            title="व्हाट्सएप पर साझा करें">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.477 2 2 6.477 2 12c0 2.136.67 4.116 1.81 5.74L2.05 22l4.385-1.15C8.016 21.572 9.94 22 12 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 1.62c4.628 0 8.38 3.753 8.38 8.38 0 4.628-3.753 8.38-8.38 8.38-1.892 0-3.645-.632-5.06-1.7l-.36-.214-2.585.678.69-2.523-.235-.373c-1.15-1.823-1.81-3.6-1.81-5.632 0-4.628 3.753-8.38 8.38-8.38z" />
                            </svg>
                        </button>
                        <button onclick="copyArticleLink()"
                            class="w-10 h-10 rounded-full bg-gray-500 hover:bg-gray-600 text-white flex items-center justify-center shadow hover:scale-110 transition duration-150"
                            title="लिंक कॉपी करें">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                        </button>
                        <button onclick="window.print()"
                            class="w-10 h-10 rounded-full bg-brand-dark hover:bg-brand-red text-white flex items-center justify-center shadow hover:scale-110 transition duration-150"
                            title="प्रिंट करें">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Header Information -->
                    <div class="space-y-4 border-b border-gray-100 pb-5">
                        <div class="flex flex-wrap justify-between items-center text-xs text-gray-400 gap-2 no-print">
                            <span class="bg-brand-red/5 text-brand-red font-bold px-2.5 py-1 rounded">
                                {{ $article->category->name }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <span>पढ़ा गया: <strong>{{ $article->views }} बार</strong></span>
                            </div>
                        </div>

                        <h2 class="text-xl md:text-2xl font-bold font-display text-gray-900 leading-snug">
                            {{ $article->title }}
                        </h2>

                        <!-- Publish info -->
                        <div class="flex justify-between items-center text-xs text-gray-500 pt-1">
                            <div>
                                <span>लेखक: <strong>{{ $article->author_name }}</strong></span>
                                @if($article->district)
                                    <span class="ml-2 pl-2 border-l border-gray-300">स्थान:
                                        <strong>{{ $article->district->name }}</strong></span>
                                @endif
                                @if($article->source_url)
                                    <span class="ml-2 pl-2 border-l border-gray-300">स्रोत: <a href="{{ $article->source_url }}"
                                            target="_blank" class="text-brand-red hover:underline font-bold">मूल खबर लिंक
                                            &nearr;</a></span>
                                @endif
                            </div>
                            <span>दिनांक: {{ $article->published_at->format('d M Y') }}</span>
                        </div>
                    </div>

                    <!-- Reading & Print Controls Toolbar (no-print) -->
                    <div
                        class="bg-gray-50 p-3 rounded-lg border border-gray-150 flex flex-wrap justify-between items-center my-4 gap-2 no-print text-xs text-gray-600">
                        <div class="flex items-center space-x-1.5">
                            <span>अक्षर आकार:</span>
                            <button onclick="localTextScale('small')"
                                class="bg-white hover:bg-brand-red hover:text-white px-2 py-0.5 rounded border border-gray-250 font-medium">छोटा</button>
                            <button onclick="localTextScale('normal')"
                                class="bg-white hover:bg-brand-red hover:text-white px-2 py-0.5 rounded border border-gray-250 font-medium bg-brand-red/5 border-brand-red/20 text-brand-red">सामान्य</button>
                            <button onclick="localTextScale('large')"
                                class="bg-white hover:bg-brand-red hover:text-white px-2 py-0.5 rounded border border-gray-250 font-medium">बड़ा</button>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button onclick="window.print()"
                                class="text-brand-dark hover:text-brand-red font-bold flex items-center space-x-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>प्रिंट आउट लें</span>
                            </button>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    @if($article->image_url)
                        <div class="my-6 rounded-lg overflow-hidden border border-gray-150 shadow-2xs max-h-[420px]">
                            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <!-- Article Body Content (Limited Preview with Official Link) -->
                    @php
                        $wordLimit = 150;
                        $fullContent = $article->content;
                        $limitedContent = \Illuminate\Support\Str::words($fullContent, $wordLimit, '...');
                        $targetUrl = $article->source_url ?: 'https://cmo.cg.gov.in/';
                    @endphp

                    <div class="relative space-y-6">
                        <!-- Limited Text Content -->
                        <div id="article-detail-body"
                            class="text-base leading-relaxed text-gray-800 space-y-4 transition-all duration-200">
                            {!! nl2br(e($limitedContent)) !!}
                        </div>

                        <!-- Prominent Official Redirection Card -->
                        <div
                            class="p-6 bg-gradient-to-br from-gray-50 via-red-50/40 to-gray-50 rounded-xl border-2 border-brand-red/20 shadow-xs space-y-4">
                            <div class="space-y-1.5">
                                <div
                                    class="inline-flex items-center space-x-1.5 bg-brand-red/10 text-brand-red px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>आधिकारिक स्रोत एवं पूर्ण विवरण</span>
                                </div>
                                <h3 class="text-base md:text-lg font-bold text-gray-900">
                                    पूरी खबर पढ़ने के लिए आधिकारिक वेबसाइट पर जाएं
                                </h3>
                                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                                    यह समाचार <strong>{{ $article->author_name }}</strong> द्वारा जारी किया गया है। संपूर्ण
                                    लेख, शासकीय परिपत्र/आदेश और विस्तृत जानकारी प्राप्त करने के लिए नीचे दिए गए बटन पर क्लिक
                                    करें।
                                </p>
                            </div>

                            <div class="pt-2 flex flex-col sm:flex-row items-center gap-4">
                                <a href="{{ $targetUrl }}" target="_blank" rel="noopener noreferrer"
                                    class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-brand-red hover:bg-brand-red-dark text-white font-bold text-sm px-6 py-3.5 rounded-lg shadow-md hover:shadow-lg transition-all duration-150 group">
                                    <span>आधिकारिक वेबसाइट पर पूरी खबर पढ़ें</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-150"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                </a>
                                @if($article->source_url)
                                    <span class="text-xs text-gray-500 font-medium">
                                        वेबसाइट: <span
                                            class="text-brand-dark font-mono font-semibold">{{ parse_url($article->source_url, PHP_URL_HOST) ?: 'आधिकारिक पोर्टल' }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tags List at bottom -->
                    @if($article->tags->count() > 0)
                        <div class="mt-8 pt-4 border-t border-gray-100 flex flex-wrap gap-2 items-center no-print">
                            <span class="text-xs font-bold text-gray-400">टैग्स:</span>
                            @foreach($article->tags as $tag)
                                <a href="{{ route('news.search') }}?query={{ $tag->name }}"
                                    class="text-xs bg-gray-100 hover:bg-brand-red hover:text-white px-2.5 py-1 rounded transition duration-100">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                </div>

                <!-- Section: Related Articles (संबंधित समाचार) - (no-print) -->
                @if($relatedArticles->count() > 0)
                    <section class="space-y-4 no-print">
                        <h3
                            class="text-lg font-bold text-brand-dark flex items-center space-x-2 border-b-2 border-brand-red pb-1.5">
                            <span class="w-3.5 h-3.5 bg-brand-red rounded-tr-lg rounded-bl-lg"></span>
                            <span>संबंधित समाचार</span>
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($relatedArticles as $rel)
                                <div
                                    class="bg-white p-4 rounded-lg border border-gray-200 shadow-2xs hover:shadow-xs transition duration-150 flex flex-col justify-between">
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-brand-accent block uppercase">{{ $rel->category->name }}</span>
                                        <h4
                                            class="text-xs md:text-sm font-bold text-gray-800 line-clamp-2 hover:text-brand-red mt-1 leading-snug">
                                            <a href="{{ route('news.show', $rel->slug) }}">{{ $rel->title }}</a>
                                        </h4>
                                    </div>
                                    <a href="{{ route('news.show', $rel->slug) }}"
                                        class="text-xs font-bold text-brand-red hover:underline block mt-4 text-right">पढ़ें
                                        &rarr;</a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Section: Comment Section (प्रतिक्रिया) - (no-print) -->
                <section class="bg-white p-6 rounded-xl border border-gray-200 shadow-2xs space-y-4 no-print">
                    <h3 class="text-base font-bold text-brand-dark border-b border-gray-100 pb-2">
                        नागरिक राय (टिप्पणी करें)
                    </h3>

                    <!-- Comment submission form -->
                    <form id="comment-form" onsubmit="addMockComment(event)" class="space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" id="commenter-name" placeholder="अपना नाम दर्ज करें..."
                                class="px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-brand-red w-full bg-white"
                                required>
                            <input type="text" id="commenter-location" placeholder="स्थान (उदा. बिलासपुर)..."
                                class="px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-brand-red w-full bg-white"
                                required>
                        </div>
                        <textarea id="comment-text" rows="3" placeholder="खबर पर अपनी राय व्यक्त करें..."
                            class="w-full px-3 py-2 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-brand-red bg-white"
                            required></textarea>
                        <button type="submit"
                            class="bg-brand-red hover:bg-brand-red-dark text-white px-4 py-2 rounded text-xs font-bold transition duration-150 border border-brand-red">
                            टिप्पणी सबमिट करें
                        </button>
                    </form>

                    <!-- Comments list container -->
                    <div id="comments-list" class="space-y-4 pt-2">
                        <div class="bg-gray-50 p-4 rounded border border-gray-100 text-xs space-y-1">
                            <div class="flex justify-between items-center font-bold text-gray-700">
                                <span>मुकेश पटेल (रायपुर)</span>
                                <span class="text-gray-400 font-normal">अभी-अभी</span>
                            </div>
                            <p class="text-gray-600 leading-relaxed">स्थानीय स्तर पर इस प्रकार के विकास और रोजगारोन्मुखी
                                पहलों से लोगों को निश्चित रूप से फायदा मिल रहा है। इसे राज्य के अन्य जिलों में भी फैलाना
                                चाहिए।</p>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Right Column: Sidebar (no-print) -->
            <aside class="lg:col-span-4 space-y-6 no-print">
                <!-- Popular widget -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                    <h3
                        class="font-display font-bold text-base text-brand-dark border-b border-gray-100 pb-2 flex items-center space-x-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-red" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        <span>सबसे लोकप्रिय ख़बरें</span>
                    </h3>
                    <div class="space-y-4">
                        @foreach($popularNews as $idx => $pop)
                            <div class="flex items-start space-x-3 group">
                                <span
                                    class="w-6 h-6 rounded-full bg-brand-red/10 text-brand-red text-xs font-bold flex items-center justify-center shrink-0 group-hover:bg-brand-red group-hover:text-white transition duration-150">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h4
                                        class="text-xs md:text-sm font-bold text-gray-800 leading-snug hover:text-brand-red group-hover:underline">
                                        <a href="{{ route('news.show', $pop->slug) }}">{{ $pop->title }}</a>
                                    </h4>
                                    <span class="text-[10px] text-gray-400 block mt-1">देखे गए: {{ $pop->views }} बार</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

        </div>
    </div>

    <!-- Scripts for show page -->
    <script>
        function localTextScale(size) {
            const body = document.getElementById('article-detail-body');
            if (!body) return;

            body.classList.remove('text-sm', 'text-base', 'text-lg', 'text-xl');

            if (size === 'small') {
                body.classList.add('text-sm');
            } else if (size === 'normal') {
                body.classList.add('text-base');
            } else if (size === 'large') {
                body.classList.add('text-lg');
            } else if (size === 'xlarge') {
                body.classList.add('text-xl');
            }
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent("{{ $article->title }} - छत्तीसगढ़ न्यूज़ एक्सप्रेस: ");
            window.open(`https://api.whatsapp.com/send?text=${text}${url}`, '_blank');
        }

        function copyArticleLink() {
            navigator.clipboard.writeText(window.location.href)
                .then(() => alert('लिंक सफलतापूर्वक कॉपी हो गया है!'))
                .catch(() => alert('लिंक कॉपी करने में त्रुटि हुई।'));
        }

        function addMockComment(event) {
            event.preventDefault();
            const nameEl = document.getElementById('commenter-name');
            const locationEl = document.getElementById('commenter-location');
            const textEl = document.getElementById('comment-text');

            if (!nameEl.value || !locationEl.value || !textEl.value) return;

            const commentsContainer = document.getElementById('comments-list');

            const commentDiv = document.createElement('div');
            commentDiv.className = "bg-gray-50 p-4 rounded border border-brand-red/20 text-xs space-y-1 animate-fade-in";
            commentDiv.innerHTML = `
                        <div class="flex justify-between items-center font-bold text-gray-700">
                            <span class="text-brand-red">${escapeHtml(nameEl.value)} (${escapeHtml(locationEl.value)})</span>
                            <span class="text-gray-400 font-normal">अभी-अभी</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed">${escapeHtml(textEl.value)}</p>
                    `;

            commentsContainer.insertBefore(commentDiv, commentsContainer.firstChild);
            document.getElementById('comment-form').reset();
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
@endsection