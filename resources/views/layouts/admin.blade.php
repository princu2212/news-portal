<!DOCTYPE html>
<html lang="hi" class="h-full bg-gray-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'एडमिन कंट्रोल रूम | छत्तीसगढ़ न्यूज़ एक्सप्रेस')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Rajdhani:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans text-gray-800 antialiased flex flex-col min-h-screen bg-gray-100">

    <!-- Top Red Accent Bar -->
    <div class="h-1.5 w-full bg-brand-red"></div>

    <div class="flex-1 flex overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="admin-sidebar" class="w-64 bg-brand-dark text-gray-200 flex flex-col shrink-0 transition-transform duration-200 ease-in-out md:translate-x-0 -translate-x-full fixed md:static inset-y-0 left-0 z-50 md:z-0 shadow-xl md:shadow-none">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-brand-dark-deep bg-brand-dark-deep flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-brand-red rounded-xl flex items-center justify-center text-white font-display font-black text-xl shadow-md">
                        CG
                    </div>
                    <div>
                        <div class="font-display font-black text-white text-base tracking-wide flex items-center space-x-1">
                            <span>सीजी न्यूज़</span>
                            <span class="text-brand-red">एडमिन</span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-semibold block uppercase tracking-wider">कंट्रोल डेस्क</span>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Quick Action: Write News -->
            <div class="p-3">
                <a href="{{ route('admin.news.create') }}"
                    class="w-full flex items-center justify-center space-x-2 bg-gradient-to-r from-brand-red to-red-600 hover:from-red-600 hover:to-brand-red text-white py-2.5 px-4 rounded-lg font-bold text-sm shadow-md hover:shadow-lg transition duration-150 transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>नई खबर लिखें / अपलोड</span>
                </a>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto text-sm">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.news.index') ? 'bg-brand-red text-white font-bold shadow' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>डैशबोर्ड एवं समाचार फीड</span>
                </a>

                <a href="{{ route('admin.news.create') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin.news.create') ? 'bg-brand-red text-white font-bold shadow' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>समाचार लिखें (Write News)</span>
                </a>

                <div class="pt-4 pb-1">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">फ़िल्टर्स एवं क्विक व्यू</span>
                </div>

                <a href="{{ route('admin.dashboard', ['is_breaking' => 1]) }}"
                    class="flex items-center justify-between px-3 py-2 rounded-lg font-medium text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        <span>ब्रेकिंग न्यूज़ टिकर</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('admin.dashboard', ['is_featured' => 1]) }}"
                    class="flex items-center justify-between px-3 py-2 rounded-lg font-medium text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <div class="flex items-center space-x-2">
                        <span class="text-amber-400 text-xs">★</span>
                        <span>मुख्य स्लाइडर (Featured)</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <div class="pt-4 pb-1">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">सिस्टम एवं लिंक्स</span>
                </div>

                <a href="{{ route('news.index') }}" target="_blank"
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg font-medium text-xs text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>लाइव पोर्टल देखें (Open Site)</span>
                </a>

                <form action="{{ route('admin.sync-rss') }}" method="POST" class="pt-2">
                    @csrf
                    <button type="submit" onclick="return confirm('क्या आप अभी सभी RSS स्रोतों से ताज़ा समाचार सिंक करना चाहते हैं?');"
                        class="w-full flex items-center space-x-2 px-3 py-2 rounded-lg font-medium text-xs text-emerald-300 bg-emerald-950/40 border border-emerald-800/40 hover:bg-emerald-900/60 transition">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>RSS समाचार सिंक करें</span>
                    </button>
                </form>
            </nav>

            <!-- User Footer in Sidebar -->
            <div class="p-3 border-t border-brand-dark-deep bg-brand-dark-deep flex items-center justify-between">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-brand-red text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ mb_substr(auth()->user()->name ?? 'सं', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'एडमिन' }}</div>
                        <div class="text-[10px] text-gray-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="लॉगआउट" class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-gray-800 rounded transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN VIEW AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Admin Top Bar -->
            <header class="bg-white border-b border-gray-200 shadow-xs shrink-0 px-4 py-3 flex items-center justify-between z-10">
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-900 p-1.5 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg md:text-xl font-bold text-gray-900 font-display flex items-center space-x-2">
                            @yield('page_title', 'एडमिन कंट्रोल डेस्क')
                        </h1>
                        <p class="text-xs text-gray-500 hidden sm:block">
                            @yield('page_subtitle', 'समाचार प्रकाशन, मीडिया अपलोड एवं लाइव फीड प्रबंधन')
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.news.create') }}"
                        class="inline-flex items-center space-x-1.5 bg-brand-red hover:bg-brand-red-dark text-white px-3.5 py-1.5 rounded-md text-xs font-bold shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>नई खबर लिखें</span>
                    </a>

                    <a href="{{ route('news.index') }}" target="_blank"
                        class="hidden sm:inline-flex items-center space-x-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md text-xs font-semibold border border-gray-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span>पोर्टल देखें</span>
                    </a>
                </div>
            </header>

            <!-- Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
                <!-- Flash Notification Messages -->
                @if(session('success'))
                    <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0 w-7 h-7 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center font-bold">
                                ✓
                            </div>
                            <div class="text-sm font-semibold text-emerald-800">
                                {{ session('success') }}
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold text-lg">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0 w-7 h-7 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-bold">
                                !
                            </div>
                            <div class="text-sm font-semibold text-red-800">
                                {{ session('error') }}
                            </div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800 font-bold text-lg">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                        <div class="text-sm font-bold text-red-800 mb-1">कृपया निम्नलिखित त्रुटियों को ठीक करें:</div>
                        <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Backdrop for mobile drawer -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
    @yield('scripts')
</body>

</html>
