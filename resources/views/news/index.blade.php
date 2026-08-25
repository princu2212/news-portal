@extends('layouts.app')

@section('title', 'छत्तीसगढ़ न्यूज़ एक्सप्रेस | निष्पक्षता के साथ ताज़ा समाचार - मुख्य पृष्ठ')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- LEFT COLUMN: Main News Content (8 Cols on Desktop) -->
        <div class="lg:col-span-8 space-y-10">

            <!-- Section 1: Featured Slider Showcase (मुख्य समाचार) -->
            @if(isset($featuredNews) && $featuredNews->count() > 0)
                <section class="space-y-4">
                    <h2
                        class="text-xl font-bold font-display text-brand-dark flex items-center space-x-2 border-b-2 border-brand-red pb-2">
                        <span class="w-3.5 h-3.5 bg-brand-red rounded-tr-lg rounded-bl-lg"></span>
                        <span>मुख्य समाचार (Top Stories)</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Large featured article (Left, 7 Cols) -->
                        @php $mainHero = $featuredNews->first(); @endphp
                        <div
                            class="md:col-span-7 bg-white rounded-lg overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition duration-200 group flex flex-col justify-between">
                            <a href="{{ route('news.show', $mainHero->slug) }}"
                                class="block relative overflow-hidden h-64 md:h-80">
                                <img src="{{ $mainHero->image_url }}" alt="{{ $mainHero->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div
                                    class="absolute top-3 left-3 bg-brand-red text-white font-bold text-[10px] uppercase px-2 py-0.5 rounded tracking-wider shadow-sm flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full inline-block animate-pulse-live"></span>
                                    <span>ब्रेकिंग</span>
                                </div>
                            </a>
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-brand-red uppercase tracking-wider bg-brand-red/5 px-2 py-0.5 rounded">{{ $mainHero->category->name }}</span>
                                    <h3 class="text-lg md:text-xl font-bold text-gray-900 mt-1.5 hover:text-brand-red">
                                        <a href="{{ route('news.show', $mainHero->slug) }}">{{ $mainHero->title }}</a>
                                    </h3>
                                    <p class="text-gray-600 text-sm mt-2 line-clamp-3 leading-relaxed">
                                        {{ $mainHero->summary }}
                                    </p>
                                </div>
                                <div
                                    class="flex justify-between items-center text-xs text-gray-400 pt-2 border-t border-gray-100">
                                    <span>रिपोर्टर: <strong>{{ $mainHero->author_name }}</strong></span>
                                    <span>{{ $mainHero->published_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary featured list (Right, 5 Cols) -->
                        <div class="md:col-span-5 flex flex-col gap-3">
                            @foreach($featuredNews->skip(1) as $secHero)
                                <div
                                    class="bg-white p-3 rounded-lg border border-gray-100 shadow-2xs hover:shadow-xs hover:border-gray-200 transition duration-150 flex space-x-3 items-center">
                                    <a href="{{ route('news.show', $secHero->slug) }}"
                                        class="w-24 h-24 shrink-0 rounded overflow-hidden relative">
                                        <img src="{{ $secHero->image_url }}" alt="{{ $secHero->title }}"
                                            class="w-full h-full object-cover">
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <span
                                            class="text-[9px] font-bold text-brand-accent uppercase tracking-wider">{{ $secHero->category->name }}</span>
                                        <h4
                                            class="text-xs md:text-sm font-bold text-gray-800 line-clamp-2 leading-snug hover:text-brand-red mt-0.5">
                                            <a href="{{ route('news.show', $secHero->slug) }}">{{ $secHero->title }}</a>
                                        </h4>
                                        <span
                                            class="text-[10px] text-gray-400 block mt-1">{{ $secHero->published_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- Section 2: Latest News Grid (ताज़ा ख़बरें) -->
            <section class="space-y-4">
                <div class="flex justify-between items-end border-b-2 border-brand-red pb-2">
                    <h2 class="text-xl font-bold font-display text-brand-dark flex items-center space-x-2">
                        <span class="w-3.5 h-3.5 bg-brand-red rounded-tr-lg rounded-bl-lg"></span>
                        <span>ताज़ा ख़बरें</span>
                    </h2>
                    <a href="{{ route('news.category', 'politics') }}"
                        class="text-xs font-bold text-brand-red hover:underline">और देखें &rarr;</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($latestPressReleases->take(6) as $item)
                        <article
                            class="bg-white rounded-lg border border-gray-200 shadow-2xs hover:shadow-sm hover:border-brand-red/20 transition duration-150 flex flex-col justify-between">
                            <div class="p-5 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-brand-red/5 text-brand-red">
                                        {{ $item->category->name }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">{{ $item->district ? $item->district->name . '
                                                बुलेटिन' : $item->author_name }}</span>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 hover:text-brand-red line-clamp-2 leading-snug">
                                    <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                                </h3>
                                <p class="text-xs text-gray-500 line-clamp-3 leading-relaxed">
                                    {{ $item->summary }}
                                </p>
                            </div>
                            <div
                                class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center rounded-b-lg">
                                <span class="text-[11px] text-gray-400 flex items-center space-x-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $item->published_at->format('d M Y') }}</span>
                                </span>
                                <a href="{{ route('news.show', $item->slug) }}"
                                    class="text-xs font-bold text-brand-red hover:underline flex items-center space-x-0.5">
                                    <span>पढ़ें खबर</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <!-- Section 3: Interactive District bulletin (जिला बुलेटिन) -->
            <section class="bg-brand-red/5 p-6 rounded-xl border border-brand-red/10 space-y-4">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-end gap-2 border-b border-brand-red/20 pb-3">
                    <div>
                        <h2 class="text-lg font-bold text-brand-dark flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-red" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>स्थानीय जिला बुलेटिन</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">अपने जिले की हर छोटी-बड़ी खबर पर रखें पैनी नजर</p>
                    </div>

                    <!-- District buttons -->
                    <div class="flex flex-wrap gap-1">
                        @if(isset($navDistricts))
                            @foreach($navDistricts->take(5) as $idx => $dist)
                                <button onclick="switchDistrictTab('dist-{{ $dist->id }}', this)"
                                    class="district-tab-btn px-3 py-1 text-xs font-bold rounded-md transition duration-150 {{ $idx == 0 ? 'bg-brand-red text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100' }}">
                                    {{ $dist->name }}
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- District news panels -->
                <div class="relative">
                    @if(isset($navDistricts))
                        @foreach($navDistricts->take(5) as $idx => $dist)
                            @php 
                                $dNews = $dist->newsArticles()->orderBy('published_at', 'desc')->take(3)->get();
                            @endphp
                            <div id="dist-{{ $dist->id }}"
                                class="district-news-panel {{ $idx == 0 ? 'block' : 'hidden' }} space-y-3">
                                @if($dNews->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        @foreach($dNews as $dn)
                                            <div
                                                class="bg-white p-4 rounded-lg border border-gray-100 shadow-2xs hover:shadow-xs transition flex flex-col justify-between">
                                                <div>
                                                    <span
                                                        class="text-[10px] text-gray-400 font-bold block">{{ $dn->published_at->format('d M Y') }}</span>
                                                    <h4 class="text-sm font-bold text-gray-800 line-clamp-2 hover:text-brand-red mt-1">
                                                        <a href="{{ route('news.show', $dn->slug) }}">{{ $dn->title }}</a>
                                                    </h4>
                                                    <p class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed">{{ $dn->summary }}
                                                    </p>
                                                </div>
                                                <a href="{{ route('news.show', $dn->slug) }}"
                                                    class="text-xs font-bold text-brand-red hover:underline block mt-4 text-right">पढ़ें खबर
                                                    &rarr;</a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-right pt-2">
                                        <a href="{{ route('news.district', $dist->slug) }}"
                                            class="text-xs font-bold text-brand-red hover:underline">
                                            {{ $dist->name }} जिला के सभी समाचार देखें &rarr;
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-6 text-gray-400 text-xs">
                                        इस जिले के लिए वर्तमान में कोई समाचार उपलब्ध नहीं है।
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </section>

            <!-- Section 4: CG State News (राज्य की ख़बरें) -->
            <section class="space-y-4">
                <div class="flex justify-between items-end border-b-2 border-brand-red pb-2">
                    <h2 class="text-xl font-bold font-display text-brand-dark flex items-center space-x-2">
                        <span class="w-3.5 h-3.5 bg-brand-accent rounded-tr-lg rounded-bl-lg"></span>
                        <span>राज्य और समाज (State & Society)</span>
                    </h2>
                    <a href="{{ route('news.category', 'state') }}"
                        class="text-xs font-bold text-brand-red hover:underline">सभी ख़बरें &rarr;</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($stateNews as $item)
                        <div
                            class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-2xs hover:shadow-sm hover:border-brand-red/30 transition duration-150 flex flex-col justify-between">
                            <div class="h-44 overflow-hidden relative">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                <div
                                    class="absolute top-3 left-3 bg-brand-red text-white font-extrabold text-[10px] px-2.5 py-0.5 rounded tracking-wider shadow">
                                    राज्य विशेष
                                </div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 hover:text-brand-red leading-snug">
                                        <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-2 line-clamp-3 leading-relaxed">
                                        {{ $item->summary }}
                                    </p>
                                </div>
                                <div
                                    class="flex justify-between items-center text-xs text-gray-400 pt-3 border-t border-gray-50">
                                    <span>{{ $item->published_at->format('d M Y') }}</span>
                                    <a href="{{ route('news.show', $item->slug) }}"
                                        class="font-bold text-brand-red hover:underline">पूरी खबर पढ़ें &rarr;</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Section 5: Photo Gallery (तस्वीरें) -->
            <section class="space-y-4">
                <h2
                    class="text-xl font-bold font-display text-brand-dark flex items-center space-x-2 border-b-2 border-brand-red pb-2">
                    <span class="w-3.5 h-3.5 bg-brand-red rounded-tr-lg rounded-bl-lg"></span>
                    <span>समाचार चित्र दीर्घा (Photo Gallery)</span>
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php $photoItems = $latestPressReleases->shuffle()->take(4); @endphp
                    @foreach($photoItems as $item)
                        <div class="relative overflow-hidden rounded-lg group h-40 border border-gray-200 shadow-2xs">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-transparent p-3 flex flex-col justify-end">
                                <h4 class="text-white text-[11px] font-bold line-clamp-2 leading-snug group-hover:underline">
                                    <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                                </h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>

        <!-- RIGHT COLUMN: Private News Sticky Sidebar (4 Cols) -->
        <aside class="lg:col-span-4 space-y-8">

            <!-- Widget 1: Interactive Opinion Poll (ओपिनियन पोल) - Replaces CM Panel -->
            <div id="opinion-poll-card"
                class="bg-gradient-to-b from-brand-dark to-brand-dark-deep text-white rounded-xl shadow-lg border border-brand-dark-deep overflow-hidden">
                <div
                    class="bg-brand-red text-white text-xs font-bold uppercase py-2.5 px-4 flex justify-between items-center">
                    <span>सीजी एक्सप्रेस ओपिनियन पोल</span>
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                </div>

                <!-- Dynamic State 1: Active Poll Form -->
                <div id="poll-active-state" class="p-6 space-y-4">
                    <h3 class="font-display font-bold text-sm text-gray-100 leading-relaxed">
                        प्रश्न: क्या छत्तीसगढ़ में पर्यटन के विकास से ग्रामीण क्षेत्रों के स्थानीय युवाओं को रोजगार के बेहतर
                        अवसर मिल रहे हैं?
                    </h3>

                    <div class="space-y-2 pt-2">
                        <button onclick="submitMockVote('yes')"
                            class="w-full text-left bg-brand-dark-deep hover:bg-brand-red text-xs py-3 px-4 rounded border border-gray-800 transition duration-150">
                            १. हाँ, काफी लाभ हो रहा है
                        </button>
                        <button onclick="submitMockVote('no')"
                            class="w-full text-left bg-brand-dark-deep hover:bg-brand-red text-xs py-3 px-4 rounded border border-gray-800 transition duration-150">
                            २. नहीं, कोई खास असर नहीं है
                        </button>
                        <button onclick="submitMockVote('maybe')"
                            class="w-full text-left bg-brand-dark-deep hover:bg-brand-red text-xs py-3 px-4 rounded border border-gray-800 transition duration-150">
                            ३. कह नहीं सकते / अभी शुरुआत है
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-500 text-center">वोटिंग पूरी तरह से सुरक्षित व गुप्त है।</p>
                </div>

                <!-- Dynamic State 2: Poll Results (Initially Hidden) -->
                <div id="poll-result-state" class="p-6 space-y-5 hidden">
                    <h3 class="font-display font-bold text-xs text-brand-accent uppercase tracking-wider">पोल के परिणाम
                        (लाइव):</h3>
                    <div class="space-y-3.5">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span>हाँ, लाभ हो रहा है</span>
                                <span class="font-bold text-brand-accent" id="pct-yes">७४%</span>
                            </div>
                            <div class="w-full bg-brand-dark-deep rounded-full h-2.5">
                                <div class="bg-brand-red h-2.5 rounded-full" style="width: 74%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span>नहीं, कोई असर नहीं</span>
                                <span class="font-bold text-gray-400" id="pct-no">१८%</span>
                            </div>
                            <div class="w-full bg-brand-dark-deep rounded-full h-2.5">
                                <div class="bg-gray-600 h-2.5 rounded-full" style="width: 18%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span>कह नहीं सकते</span>
                                <span class="font-bold text-gray-400" id="pct-maybe">८%</span>
                            </div>
                            <div class="w-full bg-brand-dark-deep rounded-full h-2.5">
                                <div class="bg-gray-850 h-2.5 rounded-full" style="width: 8%"></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-brand-dark-deep/50 p-3 rounded text-[10px] text-center text-gray-400 border border-gray-850">
                        प्रतिक्रिया देने के लिए धन्यवाद! कुल वोट: ४,८१२
                    </div>
                </div>
            </div>

            <!-- Widget 2: Popular Releases (लोकप्रिय ख़बरें) -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <h3
                    class="font-display font-bold text-base text-brand-dark border-b border-gray-100 pb-2 flex items-center space-x-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-brand-red" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
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
                                <span class="text-[10px] text-gray-400 block mt-1">पढ़ा गया: {{ $pop->views }} बार</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Widget 3: Newsletter Sign-up -->
            <div class="bg-brand-red/5 p-6 rounded-xl border border-brand-red/10 space-y-4">
                <div>
                    <h3 class="text-base font-bold text-brand-dark">छत्तीसगढ़ न्यूज़ बुलेटिन</h3>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                        नवीनतम राजनीतिक उथल-पुथल, खेल और मनोरंजन की ख़बरें प्रतिदिन सुबह सीधे अपने इनबॉक्स में पाएं।
                    </p>
                </div>

                <form onsubmit="event.preventDefault(); alert('धन्यवाद! आपका नामांकन दर्ज कर लिया गया है।'); this.reset();"
                    class="space-y-2">
                    <input type="email" placeholder="अपना ईमेल पता यहाँ लिखें..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-brand-red bg-white"
                        required>
                    <button type="submit"
                        class="w-full bg-brand-red hover:bg-brand-red-dark text-white font-bold py-2 rounded text-xs transition duration-150 shadow-sm border border-brand-red">
                        न्यूज़लेटर सब्सक्राइब करें
                    </button>
                </form>
            </div>

            <!-- Widget 4: Trending Hashtags -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <h3 class="font-display font-bold text-sm text-gray-600 uppercase tracking-wider">ट्रेंडिंग विषय</h3>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($trendingTags as $tag)
                        <a href="#"
                            onclick="event.preventDefault(); alert('इस विषय पर समाचार खोजे जा रहे हैं...'); window.location.href='{{ route('news.search') }}?query={{ $tag->name }}'"
                            class="text-xs bg-gray-100 hover:bg-brand-red hover:text-white px-2.5 py-1 rounded transition duration-150 font-medium">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>

        </aside>

    </div>

    <!-- Vanilla JS for Tab & Poll Switchers -->
    <script>
        function switchDistrictTab(tabId, btnElement) {
            // Hide all panels
            const panels = document.querySelectorAll('.district-news-panel');
            panels.forEach(panel => {
                panel.classList.add('hidden');
                panel.classList.remove('block');
            });

            // Show selected panel
            const activePanel = document.getElementById(tabId);
            if (activePanel) {
                activePanel.classList.remove('hidden');
                activePanel.classList.add('block');
            }

            // Reset button active classes
            const buttons = document.querySelectorAll('.district-tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-brand-red', 'text-white');
                btn.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
            });

            // Add active classes to current button
            btnElement.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
            btnElement.classList.add('bg-brand-red', 'text-white');
        }

        function submitMockVote(opinion) {
            // Hide form, show result animation
            document.getElementById('poll-active-state').classList.add('hidden');
            const resState = document.getElementById('poll-result-state');
            resState.classList.remove('hidden');

            // Mock adjustments based on user vote
            const yesEl = document.getElementById('pct-yes');
            const noEl = document.getElementById('pct-no');
            const maybeEl = document.getElementById('pct-maybe');

            if (opinion === 'yes') {
                yesEl.textContent = '७५%';
                noEl.textContent = '१७%';
            } else if (opinion === 'no') {
                yesEl.textContent = '७२%';
                noEl.textContent = '२०%';
            } else {
                yesEl.textContent = '७३%';
                maybeEl.textContent = '९%';
            }
        }
    </script>
@endsection