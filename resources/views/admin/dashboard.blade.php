@extends('layouts.admin')

@section('title', 'डैशबोर्ड एवं समाचार फीड | छत्तीसगढ़ न्यूज़ एक्सप्रेस एडमिन')
@section('page_title', 'डैशबोर्ड एवं समाचार फीड')
@section('page_subtitle', 'लाइव समाचार, ब्रेकिंग टिकर और मुख्य स्लाइडर का केंद्रीय प्रबंधन')

@section('content')
<div class="space-y-6">

    <!-- 1. STATS OVERVIEW CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total News -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">कुल समाचार</span>
                <div class="text-2xl font-black font-display text-gray-900 mt-1">{{ number_format($stats['total_articles']) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                📰
            </div>
        </div>

        <!-- Card 2: Today's News -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">आज के समाचार</span>
                <div class="text-2xl font-black font-display text-emerald-600 mt-1">{{ number_format($stats['today_articles']) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                ⚡
            </div>
        </div>

        <!-- Card 3: Breaking News Ticker -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">ब्रेकिंग टिकर</span>
                <div class="text-2xl font-black font-display text-brand-red mt-1">{{ number_format($stats['breaking_count']) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-red-50 text-brand-red flex items-center justify-center font-bold text-lg">
                🔥
            </div>
        </div>

        <!-- Card 4: Featured Slider -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">मुख्य स्लाइडर</span>
                <div class="text-2xl font-black font-display text-amber-600 mt-1">{{ number_format($stats['featured_count']) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                ⭐
            </div>
        </div>

        <!-- Card 5: Total Views -->
        <div class="col-span-2 lg:col-span-1 bg-white p-4 rounded-xl border border-gray-200 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">कुल रीडरशिप</span>
                <div class="text-2xl font-black font-display text-purple-600 mt-1">{{ number_format($stats['total_views']) }}</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                👁️
            </div>
        </div>
    </div>

    <!-- 2. SEARCH & FILTER SECTION -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-3">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Search Keyword -->
            <div class="lg:col-span-4 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="शीर्षक, विवरण या रिपोर्टर का नाम खोजें..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Category Filter -->
            <div class="lg:col-span-2">
                <select name="category_id" class="w-full py-2 px-3 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                    <option value="">-- सभी श्रेणियां --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- District Filter -->
            <div class="lg:col-span-2">
                <select name="district_id" class="w-full py-2 px-3 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                    <option value="">-- सभी ज़िले --</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->id }}" {{ request('district_id') == $dist->id ? 'selected' : '' }}>
                            {{ $dist->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Checkbox toggles -->
            <div class="lg:col-span-2 flex items-center space-x-3 text-xs">
                <label class="inline-flex items-center space-x-1 cursor-pointer">
                    <input type="checkbox" name="is_breaking" value="1" {{ request('is_breaking') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-brand-red focus:ring-brand-red">
                    <span class="text-gray-700 font-medium">🔥 ब्रेकिंग</span>
                </label>
                <label class="inline-flex items-center space-x-1 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ request('is_featured') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    <span class="text-gray-700 font-medium">⭐ फ़ीचर्ड</span>
                </label>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="lg:col-span-2 flex items-center space-x-2">
                <button type="submit"
                    class="flex-1 bg-brand-dark hover:bg-black text-white text-xs font-bold py-2 px-3 rounded-lg transition shadow-xs">
                    फ़िल्टर लगाएं
                </button>
                @if(request()->hasAny(['search', 'category_id', 'district_id', 'is_breaking', 'is_featured']))
                    <a href="{{ route('admin.dashboard') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold py-2 px-3 rounded-lg transition" title="फ़िल्टर हटाएं">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. NEWS FEED MANAGEMENT TABLE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-gray-50/70">
            <div>
                <h2 class="text-base font-bold text-gray-900 font-display">लाइव समाचार सूची (Feed Articles)</h2>
                <span class="text-xs text-gray-500">कुल {{ $articles->total() }} समाचार उपलब्ध</span>
            </div>
            <a href="{{ route('admin.news.create') }}"
                class="inline-flex items-center space-x-1.5 bg-brand-red hover:bg-brand-red-dark text-white px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>नई खबर लिखें एवं अपलोड करें</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100/80 text-gray-600 font-bold uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-16">फ़ोटो</th>
                        <th class="px-4 py-3">समाचार शीर्षक एवं विवरण</th>
                        <th class="px-4 py-3 w-32">श्रेणी / ज़िला</th>
                        <th class="px-4 py-3 w-28">रिपोर्टर</th>
                        <th class="px-4 py-3 w-36 text-center">लाइव स्टेटस</th>
                        <th class="px-4 py-3 w-24 text-center">व्यूज</th>
                        <th class="px-4 py-3 w-36 text-right">कार्य (Actions)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50/80 transition">
                            <!-- Image Thumbnail -->
                            <td class="px-4 py-3">
                                <div class="w-14 h-11 rounded-md overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                                        class="w-full h-full object-cover" onerror="this.src='/images/default-news.jpg'">
                                </div>
                            </td>

                            <!-- Title and Summary -->
                            <td class="px-4 py-3">
                                <div class="max-w-md lg:max-w-lg">
                                    <a href="{{ route('admin.news.edit', $article->id) }}"
                                        class="font-bold text-gray-900 hover:text-brand-red text-sm line-clamp-2 leading-snug">
                                        {{ $article->title }}
                                    </a>
                                    @if($article->summary)
                                        <p class="text-gray-500 text-[11px] line-clamp-1 mt-1">
                                            {{ $article->summary }}
                                        </p>
                                    @endif
                                    <div class="flex items-center space-x-3 text-[10px] text-gray-400 mt-1">
                                        <span>प्रकाशन: <strong>{{ $article->published_at ? $article->published_at->format('d M Y, h:i A') : 'तत्काल' }}</strong></span>
                                        @if($article->document_no)
                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600 font-mono">क्रमांक: {{ $article->document_no }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Category & District -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-brand-red/10 text-brand-red border border-brand-red/20">
                                        {{ $article->category->name ?? 'सामान्य' }}
                                    </span>
                                </div>
                                @if($article->district)
                                    <div class="text-[11px] text-gray-600 font-medium mt-1 flex items-center space-x-1">
                                        <span>📍</span>
                                        <span>{{ $article->district->name }}</span>
                                    </div>
                                @else
                                    <div class="text-[10px] text-gray-400 mt-1">राज्य स्तर</div>
                                @endif
                            </td>

                            <!-- Reporter / Bureau -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-gray-800 font-medium text-xs">{{ $article->author_name ?? 'संपादक मंडल' }}</div>
                            </td>

                            <!-- Live Ticker & Featured Toggles -->
                            <td class="px-4 py-3 text-center whitespace-nowrap space-y-1.5">
                                <!-- Breaking Toggle -->
                                <form action="{{ route('admin.news.toggle-breaking', $article->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        title="ब्रेकिंग टिकर टॉगल करें"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold border transition flex items-center space-x-1 mx-auto {{ $article->is_breaking ? 'bg-red-50 text-red-600 border-red-300 hover:bg-red-100' : 'bg-gray-100 text-gray-400 border-gray-200 hover:bg-gray-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $article->is_breaking ? 'bg-red-500 animate-ping' : 'bg-gray-400' }}"></span>
                                        <span>{{ $article->is_breaking ? '🔥 ब्रेकिंग ऑन' : 'टिकर ऑफ' }}</span>
                                    </button>
                                </form>

                                <!-- Featured Toggle -->
                                <form action="{{ route('admin.news.toggle-featured', $article->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        title="मुख्य स्लाइडर टॉगल करें"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold border transition flex items-center space-x-1 mx-auto {{ $article->is_featured ? 'bg-amber-50 text-amber-700 border-amber-300 hover:bg-amber-100' : 'bg-gray-100 text-gray-400 border-gray-200 hover:bg-gray-200' }}">
                                        <span>{{ $article->is_featured ? '⭐ मुख्य स्लाइडर' : 'स्लाइडर ऑफ' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Views -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="font-bold text-gray-700 font-mono">{{ number_format($article->views) }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <!-- View Live -->
                                    <a href="{{ route('news.show', $article->slug) }}" target="_blank"
                                        title="लाइव पोर्टल पर देखें"
                                        class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.news.edit', $article->id) }}"
                                        title="संपादित करें"
                                        class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-md transition font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.news.destroy', $article->id) }}" method="POST"
                                        onsubmit="return confirm('क्या आप निश्चित रूप से इस समाचार को हटाना चाहते हैं? यह क्रिया वापस नहीं ली जा सकती।');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            title="हटाएं (Delete)"
                                            class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-md transition font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                                    📭
                                </div>
                                <div class="text-sm font-bold text-gray-700">कोई समाचार नहीं मिला</div>
                                <p class="text-xs text-gray-400 mt-1">दिए गए खोज या फ़िल्टर मानदंड से मेल खाने वाला कोई समाचार उपलब्ध नहीं है।</p>
                                <a href="{{ route('admin.news.create') }}" class="inline-block mt-3 bg-brand-red text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-brand-red-dark transition">
                                    नया समाचार लिखें
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50/50">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
