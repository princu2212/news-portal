<!DOCTYPE html>
<html lang="hi" id="html-root">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'छत्तीसगढ़ न्यूज़ एक्सप्रेस | निष्पक्षता के साथ ताज़ा समाचार')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Rajdhani:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 font-sans transition-colors duration-200">

    <!-- 1. Sleek Red Brand Top Accent Bar -->
    <div class="h-1.5 w-full bg-brand-red"></div>

    <!-- 2. Accessibility & Admin Top Bar -->
    <div class="bg-brand-dark text-white text-xs py-2 px-4 shadow-sm border-b border-brand-dark-deep">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-2">
            <!-- Left: Brand badge, Hindi Calendar & Clock -->
            <div class="flex items-center space-x-3">
                <span class="font-bold bg-brand-red px-2.5 py-0.5 rounded text-[10px] uppercase tracking-wider">CG Express</span>
                <span id="hindi-date" class="hidden md:inline text-gray-300"></span>
                <span id="hindi-clock" class="font-mono text-gray-300"></span>
            </div>

            <!-- Right: Admin Actions, Font Resizing & Contrast -->
            <div class="flex items-center space-x-3 flex-wrap justify-center">
                <!-- Admin Auth Controls -->
                @auth
                    <div class="flex items-center space-x-2 bg-brand-dark-deep px-2.5 py-1 rounded border border-gray-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] text-gray-200 font-semibold">{{ auth()->user()->name }}</span>
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-[10px] bg-brand-red hover:bg-brand-red-dark text-white font-bold px-2 py-0.5 rounded transition">
                            डैशबोर्ड
                        </a>
                        <a href="{{ route('admin.news.create') }}"
                            class="text-[10px] bg-gray-800 hover:bg-gray-700 text-white font-bold px-2 py-0.5 rounded transition">
                            + खबर लिखें
                        </a>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-[10px] text-gray-400 hover:text-red-400 transition ml-1" title="लॉगआउट">
                                लॉगआउट
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-brand-dark-deep hover:bg-brand-red text-gray-300 hover:text-white px-2.5 py-1 rounded text-[11px] font-semibold flex items-center space-x-1 border border-gray-700 transition">
                        <svg class="w-3 h-3 text-brand-red group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>✍️ एडमिन / रिपोर्टर लॉगिन</span>
                    </a>
                @endauth

                <!-- Font Sizing -->
                <div class="flex items-center space-x-1 border-l border-gray-700 pl-3">
                    <span class="mr-1 text-[11px] text-gray-400">फ़ॉन्ट:</span>
                    <button onclick="changeFontSize(-1)"
                        class="bg-brand-dark-deep hover:bg-brand-red w-6 h-6 rounded flex items-center justify-center font-bold"
                        title="छोटा आकार">A-</button>
                    <button onclick="changeFontSize(0)"
                        class="bg-brand-dark-deep hover:bg-brand-red w-6 h-6 rounded flex items-center justify-center font-bold"
                        title="सामान्य आकार">A</button>
                    <button onclick="changeFontSize(1)"
                        class="bg-brand-dark-deep hover:bg-brand-red w-6 h-6 rounded flex items-center justify-center font-bold"
                        title="बड़ा आकार">A+</button>
                </div>

                <!-- High Contrast Toggle -->
                <button onclick="toggleContrast()"
                    class="bg-brand-dark-deep text-[11px] px-2.5 py-1 rounded hover:bg-brand-red font-semibold flex items-center space-x-1 border border-gray-700"
                    title="कंट्रास्ट बदलें">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>कंट्रास्ट</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. News Branding & Header -->
    <header class="bg-white py-4 px-4 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <!-- Left Logo & Header Title -->
            <a href="{{ route('news.index') }}" class="flex items-center space-x-4">
                <!-- Brand Icon -->
                <div class="w-16 h-16 shrink-0 flex items-center justify-center bg-brand-red rounded-2xl shadow-md p-1 group hover:scale-105 transition-transform duration-200"
                    title="छत्तीसगढ़ न्यूज़ एक्सप्रेस">
                    <div class="text-white text-center">
                        <div class="font-display font-black text-2xl tracking-tighter leading-none">CG</div>
                        <div class="font-sans font-bold text-[9px] uppercase tracking-widest leading-none mt-0.5">News</div>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black font-display text-brand-dark tracking-tight leading-none flex items-center space-x-2">
                        <span>छत्तीसगढ़ न्यूज़</span>
                        <span class="text-brand-red">एक्सप्रेस</span>
                    </h1>
                    <p class="text-xs md:text-sm text-gray-500 font-medium mt-1">
                        ताज़ा तरीन ख़बरें, बिना किसी पक्षपात के | CG News Express
                    </p>
                </div>
            </a>

            <!-- Right: Search Bar & Write News Button -->
            <div class="w-full md:w-auto flex items-center space-x-3">
                <form action="{{ route('news.search') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="query" placeholder="खबरें खोजें (यहाँ टाइप करें)..."
                        value="{{ request('query') }}"
                        class="w-full pl-3 pr-9 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red bg-gray-50"
                        required>
                    <button type="submit" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-brand-red">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>

                @auth
                    <a href="{{ route('admin.news.create') }}"
                        class="hidden sm:inline-flex items-center space-x-1.5 bg-brand-red hover:bg-brand-red-dark text-white px-3.5 py-2 rounded-lg text-xs font-bold shadow-xs transition shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ खबर लिखें</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center space-x-1.5 bg-brand-dark hover:bg-black text-white px-3 py-2 rounded-lg text-xs font-bold shadow-xs transition shrink-0">
                        <svg class="w-3.5 h-3.5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>एडमिन पैनल</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- 4. Dynamic News Ticker (ब्रेकिंग न्यूज़) -->
    @if(isset($breakingNews) && $breakingNews->count() > 0)
        <div class="bg-brand-red text-white flex overflow-hidden border-b border-brand-red-dark h-10 items-center text-sm">
            <div class="bg-brand-dark-deep px-4 py-2 font-bold shrink-0 z-10 flex items-center space-x-1.5 animate-pulse-live">
                <span class="w-2.5 h-2.5 bg-brand-red rounded-full inline-block"></span>
                <span class="tracking-wide">ब्रेकिंग न्यूज़</span>
            </div>
            <div class="relative w-full overflow-hidden flex items-center h-full">
                <div class="animate-ticker absolute whitespace-nowrap flex space-x-12 py-1 items-center">
                    @foreach($breakingNews as $item)
                        <a href="{{ route('news.show', $item->slug) }}"
                            class="hover:text-brand-accent font-medium flex items-center space-x-2">
                            <span class="text-brand-accent text-[10px]">&#9670;</span>
                            <span>{{ $item->title }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- 5. Navigation Bar -->
    <nav class="bg-brand-dark text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4 overflow-x-auto scrollbar-none md:overflow-x-visible">
            <div class="flex space-x-1 items-center py-1 md:py-0 whitespace-nowrap">
                <!-- Home Link -->
                <a href="{{ route('news.index') }}"
                    class="px-4 py-3 text-sm font-semibold hover:bg-brand-red transition-all duration-150 flex items-center space-x-1 {{ request()->routeIs('news.index') ? 'bg-brand-red text-white border-b-2 border-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>होम</span>
                </a>

                <!-- Category Links -->
                @if(isset($navCategories))
                    @foreach($navCategories as $cat)
                        <a href="{{ route('news.category', $cat->slug) }}"
                            class="px-3.5 py-3 text-sm font-semibold hover:bg-brand-red transition-all duration-150 {{ isset($category) && $category->id == $cat->id ? 'bg-brand-red text-white border-b-2 border-white' : '' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                @endif

                <!-- Districts Dropdown -->
                @if(isset($navDistricts) && $navDistricts->count() > 0)
                    <div class="relative group whitespace-nowrap">
                        <button class="px-3.5 py-3 text-sm font-semibold hover:bg-brand-red flex items-center space-x-1 focus:outline-none transition-all duration-150 {{ isset($district) ? 'bg-brand-red text-white' : '' }}">
                            <span>जिला समाचार</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-0 bg-white text-gray-800 shadow-xl rounded-b-md border border-gray-150 w-52 py-2 hidden group-hover:block hover:block z-40">
                            @foreach($navDistricts as $dist)
                                <a href="{{ route('news.district', $dist->slug) }}"
                                    class="block px-4 py-2 text-xs font-medium hover:bg-gray-100 hover:text-brand-red transition-colors duration-150">
                                    📍 {{ $dist->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Navbar Link: Write / Admin -->
            <div class="hidden md:flex items-center pl-2 whitespace-nowrap">
                <a href="{{ auth()->check() ? route('admin.news.create') : route('login') }}"
                    class="bg-brand-red hover:bg-brand-red-dark text-white text-xs font-bold px-3 py-1.5 rounded flex items-center space-x-1.5 transition shadow-xs">
                    <span>✍️</span>
                    <span>खबर लिखें / अपलोड करें</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- 6. Main Content Area -->
    <main class="max-w-7xl mx-auto py-6 px-4 md:px-6">
        <div id="content-body" class="text-[15px] leading-relaxed">
            @yield('content')
        </div>
    </main>

    <!-- 7. Footer -->
    <footer class="bg-brand-dark text-white border-t-4 border-brand-red py-10 px-4 mt-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Col 1: About Platform -->
            <div class="space-y-4">
                <h3 class="font-display font-bold text-lg border-b border-brand-dark-deep pb-2 text-brand-red uppercase tracking-wider">
                    हमारे बारे में
                </h3>
                <p class="text-xs text-gray-400 leading-loose">
                    <strong>छत्तीसगढ़ न्यूज़ एक्सप्रेस (CG News Express)</strong> राज्य का प्रमुख डिजिटल न्यूज़ नेटवर्क
                    है। हमारा उद्देश्य पाठकों तक निष्पक्षता, सत्यता और तीव्रता के साथ समाचार पहुंचाना है।
                </p>
                <div class="text-xs text-gray-400">
                    ईमेल: contact@cgnewsexpress.com<br>
                    फ़ोन: 0771-3567890
                </div>
            </div>

            <!-- Col 2: Useful Sections & Editorial Portal -->
            <div class="space-y-4">
                <h3 class="font-display font-bold text-lg border-b border-brand-dark-deep pb-2 text-brand-red uppercase tracking-wider">
                    संपादकीय एवं लिंक्स
                </h3>
                <ul class="text-xs space-y-2 text-gray-400">
                    <li><a href="{{ route('news.index') }}" class="hover:underline hover:text-brand-red">नवीनतम मुख्य ख़बरें</a></li>
                    <li><a href="{{ route('login') }}" class="hover:underline hover:text-brand-red font-semibold text-brand-accent">✍️ एडमिन / रिपोर्टर लॉगिन</a></li>
                    <li><a href="{{ route('admin.news.create') }}" class="hover:underline hover:text-brand-red">नई खबर लिखें एवं अपलोड करें</a></li>
                    <li><a href="{{ route('cron.fetch-rss') }}" target="_blank" class="hover:underline hover:text-brand-red">RSS फीड स्थिति</a></li>
                </ul>
            </div>

            <!-- Col 3: Categories -->
            <div class="space-y-4">
                <h3 class="font-display font-bold text-lg border-b border-brand-dark-deep pb-2 text-brand-red uppercase tracking-wider">
                    श्रेणियां
                </h3>
                <ul class="text-xs space-y-2 text-gray-400">
                    @if(isset($navCategories))
                        @foreach($navCategories as $cat)
                            <li><a href="{{ route('news.category', $cat->slug) }}"
                                    class="hover:underline hover:text-brand-red">{{ $cat->name }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <!-- Col 4: Social feeds / Copyright notes -->
            <div class="space-y-4">
                <h3 class="font-display font-bold text-lg border-b border-brand-dark-deep pb-2 text-brand-red uppercase tracking-wider">
                    सोशल मीडिया
                </h3>
                <p class="text-xs text-gray-400">हमें सोशल मीडिया हैंडल्स पर भी फॉलो करें ताकि कोई भी बड़ी ब्रेकिंग खबर आपसे न छूटे:</p>
                <div class="flex items-center space-x-2 pt-1">
                    <a href="#" onclick="event.preventDefault(); alert('फेसबुक पेज लोड हो रहा है...');"
                        class="bg-brand-dark-deep hover:bg-brand-red px-3 py-1.5 rounded text-[10px] text-gray-300 font-bold border border-brand-dark-deep transition">Facebook</a>
                    <a href="#" onclick="event.preventDefault(); alert('ट्विटर फीड लोड हो रहा है...');"
                        class="bg-brand-dark-deep hover:bg-brand-red px-3 py-1.5 rounded text-[10px] text-gray-300 font-bold border border-brand-dark-deep transition">X / Twitter</a>
                    <a href="#" onclick="event.preventDefault(); alert('यूट्यूब चैनल लोड हो रहा है...');"
                        class="bg-brand-dark-deep hover:bg-brand-red px-3 py-1.5 rounded text-[10px] text-gray-300 font-bold border border-brand-dark-deep transition">YouTube</a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto border-t border-brand-dark-deep mt-8 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-400 gap-4">
            <p>सर्वाधिकार सुरक्षित © २०२६ - छत्तीसगढ़ न्यूज़ एक्सप्रेस नेटवर्क (CG News Express)</p>
            <div class="flex space-x-4">
                <a href="{{ route('login') }}" class="hover:underline text-gray-500 hover:text-gray-300">एडमिन लॉगिन</a>
                <a href="#" class="hover:underline">गोपनीयता नीति</a>
                <a href="#" class="hover:underline">उपयोग की शर्तें</a>
            </div>
        </div>
    </footer>

    <!-- 8. Interactive Javascript utilities for Accessibility -->
    <script>
        const contrastKey = 'cg_news_contrast_mode';
        const fontSizeKey = 'cg_news_font_size';

        if (localStorage.getItem(contrastKey) === 'high') {
            document.getElementById('html-root').classList.add('high-contrast');
        }

        const savedFontSize = parseInt(localStorage.getItem(fontSizeKey) || '0');
        applyFontSize(savedFontSize);

        function toggleContrast() {
            const root = document.getElementById('html-root');
            if (root.classList.contains('high-contrast')) {
                root.classList.remove('high-contrast');
                localStorage.setItem(contrastKey, 'normal');
            } else {
                root.classList.add('high-contrast');
                localStorage.setItem(contrastKey, 'high');
            }
        }

        function applyFontSize(size) {
            const content = document.getElementById('content-body');
            if (!content) return;

            content.classList.remove('text-[14px]', 'text-[15px]', 'text-[17px]', 'text-[19px]');

            if (size === -1) {
                content.classList.add('text-[14px]');
            } else if (size === 0) {
                content.classList.add('text-[15px]');
            } else if (size === 1) {
                content.classList.add('text-[17px]');
            } else if (size === 2) {
                content.classList.add('text-[19px]');
            }
        }

        function changeFontSize(direction) {
            let current = parseInt(localStorage.getItem(fontSizeKey) || '0');
            if (direction === -1 && current > -1) {
                current -= 1;
            } else if (direction === 1 && current < 2) {
                current += 1;
            } else if (direction === 0) {
                current = 0;
            }

            localStorage.setItem(fontSizeKey, current);
            applyFontSize(current);
        }

        function updateHindiClock() {
            const now = new Date();
            const hours = now.getHours();
            const mins = now.getMinutes().toString().padStart(2, '0');
            const secs = now.getSeconds().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'अपराह्न' : 'पूर्वाह्न';
            const displayHours = (hours % 12 || 12).toString().padStart(2, '0');

            const clockString = `${displayHours}:${mins}:${secs} ${ampm}`;
            const clockEl = document.getElementById('hindi-clock');
            if (clockEl) clockEl.textContent = clockString;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateString = now.toLocaleDateString('hi-IN', options);
            const dateEl = document.getElementById('hindi-date');
            if (dateEl) dateEl.textContent = dateString;
        }

        setInterval(updateHindiClock, 1000);
        updateHindiClock();
    </script>
</body>

</html>