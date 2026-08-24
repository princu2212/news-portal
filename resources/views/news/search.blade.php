@extends('layouts.app')

@section('title')
    खोज परिणाम: "{{ $query }}" | छत्तीसगढ़ न्यूज़ एक्सप्रेस
@endsection

@section('content')
<div class="space-y-6">
    <!-- Search Header -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-2xs">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <nav class="text-xs text-gray-400 flex items-center space-x-1.5 mb-1.5">
                    <a href="{{ route('news.index') }}" class="hover:underline hover:text-brand-red">होम</a>
                    <span>&rsaquo;</span>
                    <span class="text-gray-600">खोज परिणाम</span>
                </nav>
                <h1 class="text-2xl font-bold font-display text-brand-dark flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>खोज शब्द: "{{ $query }}"</span>
                </h1>
            </div>
            @if($articles->count() > 0)
                <span class="text-xs font-bold text-gray-400 bg-gray-50 border border-gray-150 px-3 py-1 rounded">
                    कुल परिणाम: {{ $articles->total() }}
                </span>
            @endif
        </div>
    </div>

    <!-- Main Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Search Results -->
        <div class="lg:col-span-8 space-y-8">
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($articles as $item)
                        <article class="bg-white rounded-lg border border-gray-200 shadow-2xs hover:shadow-sm hover:border-brand-red/20 transition duration-150 flex flex-col justify-between">
                            <!-- Image -->
                            @if($item->image_url)
                            <a href="{{ route('news.show', $item->slug) }}" class="h-44 overflow-hidden relative block">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                            </a>
                            @endif

                            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex justify-between items-center text-[10px] text-gray-400 font-bold uppercase mb-1">
                                        <span class="text-brand-red">{{ $item->category->name }}</span>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 hover:text-brand-red line-clamp-2 leading-snug">
                                        <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-500 line-clamp-3 leading-relaxed mt-2">
                                        {{ $item->summary }}
                                    </p>
                                </div>
                                <div class="flex justify-between items-center text-xs text-gray-400 pt-3 border-t border-gray-50">
                                    <span>{{ $item->published_at->format('d M Y') }}</span>
                                    <a href="{{ route('news.show', $item->slug) }}" class="font-bold text-brand-red hover:underline">पूरा पढ़ें &rarr;</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-6">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="bg-white p-12 text-center text-gray-500 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base font-bold text-gray-800">खोज परिणाम शून्य</h3>
                    <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">
                        हमें आपके द्वारा खोजे गए शब्द <strong>"{{ $query }}"</strong> से मेल खाता कोई समाचार नहीं मिला। कृपया अन्य सामान्य शब्दों (जैसे 'क्रिकेट', 'राजनीति', 'रायपुर', 'बस्तर') के साथ पुनः प्रयास करें।
                    </p>
                    <div class="pt-4 max-w-xs mx-auto">
                        <a href="{{ route('news.index') }}" class="bg-brand-red hover:bg-brand-red-dark text-white px-4 py-2 rounded text-xs font-bold transition duration-150 inline-block shadow-sm">
                            मुख्य पृष्ठ पर जाएं
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Sidebar -->
        <aside class="lg:col-span-4 space-y-6">
            <!-- Popular widget -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <h3 class="font-display font-bold text-base text-brand-dark border-b border-gray-100 pb-2 flex items-center space-x-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>सबसे लोकप्रिय ख़बरें</span>
                </h3>
                
                <div class="space-y-4">
                    @foreach($popularNews as $idx => $pop)
                    <div class="flex items-start space-x-3 group">
                        <span class="w-6 h-6 rounded-full bg-brand-red/10 text-brand-red text-xs font-bold flex items-center justify-center shrink-0 group-hover:bg-brand-red group-hover:text-white transition duration-150">
                            {{ $idx + 1 }}
                        </span>
                        <div class="min-w-0">
                            <h4 class="text-xs md:text-sm font-bold text-gray-800 leading-snug hover:text-brand-red group-hover:underline">
                                <a href="{{ route('news.show', $pop->slug) }}">{{ $pop->title }}</a>
                            </h4>
                            <span class="text-[10px] text-gray-400 block mt-1">पढ़ा गया: {{ $pop->views }} बार</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </aside>

    </div>
</div>
@endsection
