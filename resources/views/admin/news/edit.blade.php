@extends('layouts.admin')

@section('title', 'समाचार संपादित करें | छत्तीसगढ़ न्यूज़ एक्सप्रेस एडमिन')
@section('page_title', 'समाचार संपादित करें (Edit News)')
@section('page_subtitle', 'शीर्षक, विवरण, फोटो और प्राथमिकता में बदलाव करें')

@section('content')
<form action="{{ route('admin.news.update', $article->id) }}" method="POST" enctype="multipart/form-data" id="news-form">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: Main News Editing Area (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- 1. Headline / Title -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-800 mb-1">
                        समाचार शीर्षक (Headline) <span class="text-brand-red">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red">
                    <div class="flex justify-between items-center text-[11px] text-gray-400 mt-1">
                        <span id="title-char-count">{{ mb_strlen($article->title) }} अक्षर</span>
                        <span class="text-gray-400">Slug: <code class="font-mono text-gray-600">{{ $article->slug }}</code></span>
                    </div>
                </div>

                <!-- Short Summary / Snippet -->
                <div>
                    <label for="summary" class="block text-xs font-bold text-gray-700 mb-1">
                        संक्षिप्त विवरण / सार (Short Summary)
                    </label>
                    <textarea id="summary" name="summary" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">{{ old('summary', $article->summary) }}</textarea>
                </div>
            </div>

            <!-- 2. Full Article Content Body -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <div class="flex justify-between items-center">
                    <label for="news-content" class="block text-sm font-bold text-gray-800">
                        विस्तृत समाचार सामग्री (Full Article Content) <span class="text-brand-red">*</span>
                    </label>
                    <div class="text-xs text-gray-400 font-mono" id="word-stats"></div>
                </div>

                <!-- Formatting Helpers Toolbar -->
                <div class="flex flex-wrap gap-1 p-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                    <button type="button" onclick="insertTag('b')" class="px-2.5 py-1 bg-white hover:bg-gray-200 border border-gray-300 rounded font-bold" title="बोल्ड">B</button>
                    <button type="button" onclick="insertTag('i')" class="px-2.5 py-1 bg-white hover:bg-gray-200 border border-gray-300 rounded italic font-serif" title="इटैलिक">I</button>
                    <button type="button" onclick="insertHeading()" class="px-2.5 py-1 bg-white hover:bg-gray-200 border border-gray-300 rounded font-bold text-[11px]" title="उप-शीर्षक">H3 उपशीर्षक</button>
                    <button type="button" onclick="insertBulletList()" class="px-2.5 py-1 bg-white hover:bg-gray-200 border border-gray-300 rounded text-[11px]" title="बुलेट सूची">• बुलेट</button>
                    <button type="button" onclick="insertQuote()" class="px-2.5 py-1 bg-white hover:bg-gray-200 border border-gray-300 rounded text-[11px]" title="बयान / कोट">“ कोट</button>
                    <button type="button" onclick="insertParagraph()" class="px-2.5 py-1 bg-white hover:bg-gray-200 border border-gray-300 rounded text-[11px]" title="नया पैराग्राफ">&para; पैराग्राफ</button>
                </div>

                <textarea id="news-content" name="content" rows="14" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red font-sans">{{ old('content', $article->content) }}</textarea>
            </div>

            <!-- 3. Photo & Media Upload -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 flex items-center space-x-1.5">
                            <span>📷</span>
                            <span>समाचार फ़ोटो / इमेज बदलें</span>
                        </h3>
                        <p class="text-xs text-gray-500">यदि फ़ोटो नहीं बदलनी है तो इसे खाली छोड़ दें</p>
                    </div>

                    <!-- Upload Type Tabs -->
                    <div class="flex bg-gray-100 p-1 rounded-lg text-xs font-semibold">
                        <button type="button" id="tab-file-btn" onclick="switchMediaTab('file')"
                            class="px-3 py-1 rounded-md bg-white text-gray-900 shadow-2xs">फ़ाइल अपलोड</button>
                        <button type="button" id="tab-url-btn" onclick="switchMediaTab('url')"
                            class="px-3 py-1 rounded-md text-gray-500 hover:text-gray-900">वेब URL</button>
                    </div>
                </div>

                <!-- Current Image Box -->
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-24 h-20 rounded-md overflow-hidden bg-gray-200 border border-gray-300 shrink-0">
                        <img id="current-img" src="{{ $article->image_url }}" alt="{{ $article->title }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="text-xs">
                        <span class="font-bold text-gray-800 block">वर्तमान फ़ोटो</span>
                        <p class="text-[11px] text-gray-500 truncate max-w-sm mt-0.5">{{ $article->image_url }}</p>
                        <span class="text-[10px] text-emerald-600 font-semibold mt-1 inline-block">✓ यह फ़ोटो लाइव फीड में दिख रही है</span>
                    </div>
                </div>

                <!-- Tab 1: Local File Upload -->
                <div id="media-tab-file" class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 hover:border-brand-red/60 rounded-xl p-5 text-center cursor-pointer bg-gray-50 hover:bg-red-50/20 transition group"
                        onclick="document.getElementById('image_file').click()">
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="hidden" onchange="previewUploadedImage(this)">
                        <div class="w-10 h-10 rounded-full bg-red-100 text-brand-red flex items-center justify-center mx-auto mb-1.5 group-hover:scale-110 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-gray-700">नई फ़ोटो अपलोड करने के लिए क्लिक करें</div>
                        <p class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP (अधिकतम 5 MB)</p>
                    </div>
                </div>

                <!-- Tab 2: Image URL -->
                <div id="media-tab-url" class="hidden space-y-2">
                    <label for="image_url" class="block text-xs font-semibold text-gray-700">नया ऑनलाइन इमेज लिंक (Image URL)</label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}"
                        placeholder="https://example.com/images/news-photo.jpg"
                        oninput="previewUrlImage(this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- New Image Preview Box -->
                <div id="image-preview-wrapper" class="hidden p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-bold text-emerald-800">नई फ़ोटो चुनी गई (New Photo Selected):</span>
                        <button type="button" onclick="clearImagePreview()" class="text-xs text-red-600 hover:underline font-bold">हटाएं</button>
                    </div>
                    <div class="w-full h-48 rounded-lg overflow-hidden bg-gray-200">
                        <img id="image-preview-img" src="" alt="नया पूर्वावलोकन" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <!-- 4. Tags -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <label for="tags" class="block text-xs font-bold text-gray-700">
                    टैग्स एवं कीवर्ड्स (Tags)
                </label>
                <input type="text" id="tags" name="tags" value="{{ old('tags', $currentTags) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">

                <!-- Tag badges -->
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-[10px] text-gray-400 font-semibold">सुझावित टैग्स:</span>
                    @foreach(['छत्तीसगढ़', 'रायपुर', 'राजनीति', 'विकास', 'बजट', 'क्राइम', 'शिक्षा', 'मौसम'] as $suggestedTag)
                        <button type="button" onclick="addTag('{{ $suggestedTag }}')"
                            class="px-2 py-0.5 bg-gray-100 hover:bg-red-50 hover:text-brand-red text-gray-600 rounded text-[10px] font-medium border border-gray-200 transition">
                            + {{ $suggestedTag }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Publishing Controls & Feed Settings (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- 1. Publish Action Box -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-2 flex items-center justify-between">
                    <span>प्रकाशन स्थिति (Status)</span>
                    <span class="text-xs text-gray-400 font-mono">ID: #{{ $article->id }}</span>
                </h3>

                <!-- Category Selector -->
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-700 mb-1">
                        समाचार श्रेणी (Category) <span class="text-brand-red">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- District Selector -->
                <div>
                    <label for="district_id" class="block text-xs font-bold text-gray-700 mb-1">
                        ज़िला (District)
                    </label>
                    <select id="district_id" name="district_id"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                        <option value="">-- राज्य स्तर / सभी ज़िले --</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" {{ old('district_id', $article->district_id) == $dist->id ? 'selected' : '' }}>
                                📍 {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Author / Reporter Name -->
                <div>
                    <label for="author_name" class="block text-xs font-bold text-gray-700 mb-1">
                        रिपोर्टर / ब्यूरो का नाम
                    </label>
                    <input type="text" id="author_name" name="author_name"
                        value="{{ old('author_name', $article->author_name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- Document / Circular Code -->
                <div>
                    <label for="document_no" class="block text-xs font-bold text-gray-700 mb-1">
                        विज्ञप्ति / दस्तावेज़ क्रमांक
                    </label>
                    <input type="text" id="document_no" name="document_no" value="{{ old('document_no', $article->document_no) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red font-mono">
                </div>

                <!-- Source URL -->
                <div>
                    <label for="source_url" class="block text-xs font-bold text-gray-700 mb-1">
                        मूल स्रोत लिंक (Source URL)
                    </label>
                    <input type="url" id="source_url" name="source_url" value="{{ old('source_url', $article->source_url) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- Publication Time -->
                <div>
                    <label for="published_at" class="block text-xs font-bold text-gray-700 mb-1">
                        प्रकाशन समय (Publish Datetime)
                    </label>
                    <input type="datetime-local" id="published_at" name="published_at"
                        value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- SPECIAL FEED TOGGLES -->
                <div class="pt-3 border-t border-gray-200 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">विशेष फीड प्राथमिकता</span>

                    <!-- Breaking News Ticker Toggle -->
                    <label class="flex items-start space-x-3 p-3 rounded-lg border border-red-200 bg-red-50/60 hover:bg-red-50 cursor-pointer transition">
                        <input type="checkbox" name="is_breaking" value="1" {{ old('is_breaking', $article->is_breaking) ? 'checked' : '' }}
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-red focus:ring-brand-red">
                        <div class="text-xs">
                            <span class="font-bold text-brand-red flex items-center space-x-1">
                                <span>🔥 ब्रेकिंग न्यूज़ टिकर में दिखाएं</span>
                            </span>
                            <p class="text-gray-500 text-[11px] mt-0.5">वेबसाइट के शीर्ष पर लाल पट्टी में स्क्रॉल करेगा</p>
                        </div>
                    </label>

                    <!-- Featured Slider Toggle -->
                    <label class="flex items-start space-x-3 p-3 rounded-lg border border-amber-200 bg-amber-50/60 hover:bg-amber-50 cursor-pointer transition">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                        <div class="text-xs">
                            <span class="font-bold text-amber-800 flex items-center space-x-1">
                                <span>⭐ मुख्य फ़ीचर्ड स्लाइडर में दिखाएं</span>
                            </span>
                            <p class="text-gray-500 text-[11px] mt-0.5">होमपेज के मुख्य टॉप 4 बड़े कार्ड्स में स्थान मिलेगा</p>
                        </div>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 space-y-2">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-red to-red-600 hover:from-red-600 hover:to-brand-red text-white py-3 px-4 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition duration-150 transform active:scale-98 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>अपडेट करें एवं फीड में सहेजें</span>
                    </button>
                    
                    <a href="{{ route('news.show', $article->slug) }}" target="_blank"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-3 rounded-lg font-semibold text-xs flex items-center justify-center space-x-1.5 transition">
                        <span>👁️ लाइव आर्टिकल देखें</span>
                    </a>
                </div>
            </div>

            <!-- 2. LIVE FEED PREVIEW -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="text-xs font-bold text-gray-800">लाइव कार्ड प्रीव्यू</span>
                    <span class="text-[10px] bg-blue-50 text-blue-700 font-bold px-2 py-0.5 rounded">Preview</span>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-2xs">
                    <div class="h-32 bg-gray-100 relative overflow-hidden">
                        <img id="preview-card-img" src="{{ $article->image_url }}" alt="Preview" class="w-full h-full object-cover">
                        <div class="absolute top-2 left-2 bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">
                            <span id="preview-card-cat">{{ $article->category->name ?? 'श्रेणी' }}</span>
                        </div>
                    </div>
                    <div class="p-3 space-y-1.5">
                        <h4 id="preview-card-title" class="font-bold text-xs text-gray-900 line-clamp-2 leading-snug">
                            {{ $article->title }}
                        </h4>
                        <p id="preview-card-summary" class="text-[10px] text-gray-500 line-clamp-2">
                            {{ $article->summary }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // 1. Media tab toggle
    function switchMediaTab(tab) {
        const fileTab = document.getElementById('media-tab-file');
        const urlTab = document.getElementById('media-tab-url');
        const fileBtn = document.getElementById('tab-file-btn');
        const urlBtn = document.getElementById('tab-url-btn');

        if (tab === 'file') {
            fileTab.classList.remove('hidden');
            urlTab.classList.add('hidden');
            fileBtn.className = 'px-3 py-1 rounded-md bg-white text-gray-900 shadow-2xs';
            urlBtn.className = 'px-3 py-1 rounded-md text-gray-500 hover:text-gray-900';
        } else {
            urlTab.classList.remove('hidden');
            fileTab.classList.add('hidden');
            urlBtn.className = 'px-3 py-1 rounded-md bg-white text-gray-900 shadow-2xs';
            fileBtn.className = 'px-3 py-1 rounded-md text-gray-500 hover:text-gray-900';
        }
    }

    // 2. Preview uploaded local image
    function previewUploadedImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewImg = document.getElementById('image-preview-img');
                const cardImg = document.getElementById('preview-card-img');
                const wrapper = document.getElementById('image-preview-wrapper');

                previewImg.src = e.target.result;
                cardImg.src = e.target.result;
                wrapper.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // 3. Preview URL image
    function previewUrlImage(url) {
        if (url && url.length > 5) {
            const previewImg = document.getElementById('image-preview-img');
            const cardImg = document.getElementById('preview-card-img');
            const wrapper = document.getElementById('image-preview-wrapper');

            previewImg.src = url;
            cardImg.src = url;
            wrapper.classList.remove('hidden');
        }
    }

    // 4. Clear Preview
    function clearImagePreview() {
        document.getElementById('image_file').value = '';
        document.getElementById('image_url').value = '';
        document.getElementById('image-preview-wrapper').classList.add('hidden');
        document.getElementById('preview-card-img').src = document.getElementById('current-img').src;
    }

    // 5. Quick Tag Helper
    function addTag(tagName) {
        const tagsInput = document.getElementById('tags');
        const current = tagsInput.value.split(',').map(t => t.trim()).filter(Boolean);
        if (!current.includes(tagName)) {
            current.push(tagName);
            tagsInput.value = current.join(', ');
        }
    }

    // 6. Realtime Text sync
    const titleInput = document.getElementById('title');
    const summaryInput = document.getElementById('summary');
    const contentInput = document.getElementById('news-content');
    const categorySelect = document.getElementById('category_id');

    titleInput.addEventListener('input', function() {
        const text = this.value.trim();
        document.getElementById('preview-card-title').textContent = text || 'यहाँ आपका शीर्षक दिखेगा...';
        document.getElementById('title-char-count').textContent = text.length + ' अक्षर';
    });

    summaryInput.addEventListener('input', function() {
        const text = this.value.trim();
        document.getElementById('preview-card-summary').textContent = text || 'संक्षिप्त विवरण यहाँ प्रदर्शित होगा...';
    });

    categorySelect.addEventListener('change', function() {
        const text = this.options[this.selectedIndex].text;
        document.getElementById('preview-card-cat').textContent = this.value ? text : 'श्रेणी';
    });

    function updateWordStats() {
        const text = contentInput.value.trim();
        const words = text ? text.split(/\s+/).length : 0;
        const readMinutes = Math.max(1, Math.ceil(words / 150));
        document.getElementById('word-stats').textContent = `${words} शब्द • ~${readMinutes} मिनट पठन`;
    }

    contentInput.addEventListener('input', updateWordStats);
    updateWordStats();

    // 7. Formatting Helpers
    function insertTag(tag) {
        const textarea = document.getElementById('news-content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = textarea.value.substring(start, end);
        const replacement = `<${tag}>${selected || 'टेक्स्ट'}</${tag}>`;
        textarea.setRangeText(replacement, start, end, 'end');
        textarea.focus();
    }

    function insertHeading() {
        const textarea = document.getElementById('news-content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = textarea.value.substring(start, end);
        const replacement = `\n\n### ${selected || 'उप-शीर्षक'}\n`;
        textarea.setRangeText(replacement, start, end, 'end');
        textarea.focus();
    }

    function insertBulletList() {
        const textarea = document.getElementById('news-content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = textarea.value.substring(start, end);
        const replacement = `\n• ${selected || 'मुख्य बिंदु 1'}\n• मुख्य बिंदु 2\n`;
        textarea.setRangeText(replacement, start, end, 'end');
        textarea.focus();
    }

    function insertQuote() {
        const textarea = document.getElementById('news-content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selected = textarea.value.substring(start, end);
        const replacement = `\n> "${selected || 'यहाँ किसी नेता या अधिकारी का वक्तव्य / कोट लिखें'}"\n`;
        textarea.setRangeText(replacement, start, end, 'end');
        textarea.focus();
    }

    function insertParagraph() {
        const textarea = document.getElementById('news-content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        textarea.setRangeText('\n\n', start, end, 'end');
        textarea.focus();
    }
</script>
@endsection
