@extends('layouts.admin')

@section('title', 'नई खबर लिखें एवं अपलोड करें | छत्तीसगढ़ न्यूज़ एक्सप्रेस एडमिन')
@section('page_title', 'नया समाचार लिखें एवं अपलोड करें')
@section('page_subtitle', 'ताज़ा समाचार, फोटो, श्रेणी और ब्रेकिंग टिकर के साथ लाइव फीड में प्रकाशित करें')

@section('content')
<form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" id="news-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: Main News Writing Area (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- 1. Headline / Title -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <div>
                    <label for="title" class="block text-sm font-bold text-gray-800 mb-1">
                        समाचार शीर्षक (Headline) <span class="text-brand-red">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        placeholder="उदा: रायपुर में मुख्यमंत्री ने किया नए स्वास्थ्य केंद्र का लोकार्पण, आमजन को मिलेंगी आधुनिक सुविधाएं..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-base font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red placeholder-gray-400">
                    <div class="flex justify-between items-center text-[11px] text-gray-400 mt-1">
                        <span id="title-char-count">0 अक्षर</span>
                        <span class="text-gray-400">आकर्षक और स्पष्ट शीर्षक दें</span>
                    </div>
                </div>

                <!-- Short Summary / Snippet -->
                <div>
                    <label for="summary" class="block text-xs font-bold text-gray-700 mb-1">
                        संक्षिप्त विवरण / सार (Short Summary / Lead Paragraph)
                    </label>
                    <textarea id="summary" name="summary" rows="2"
                        placeholder="समाचार का 1-2 पंक्तियों में मुख्य सार लिखें (यह होमपेज कार्ड्स और सोशल शेयर पर दिखेगा)..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red placeholder-gray-400">{{ old('summary') }}</textarea>
                </div>
            </div>

            <!-- 2. Full Article Content Body with Formatting Toolbar -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <div class="flex justify-between items-center">
                    <label for="news-content" class="block text-sm font-bold text-gray-800">
                        विस्तृत समाचार सामग्री (Full Article Content) <span class="text-brand-red">*</span>
                    </label>
                    <div class="text-xs text-gray-400 font-mono" id="word-stats">0 शब्द • ~1 मिनट पठन</div>
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
                    placeholder="यहाँ पूरा समाचार विस्तार से लिखें..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red font-sans">{{ old('content') }}</textarea>

                <p class="text-[11px] text-gray-400">
                    💡 <strong>सुझाव:</strong> पैराग्राफ में खाली लाइन छोड़ें। महत्वपूर्ण नामों और बयानों को बोल्ड करें।
                </p>
            </div>

            <!-- 3. Photo & Media Upload -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 flex items-center space-x-1.5">
                            <span>📷</span>
                            <span>समाचार फ़ोटो एवं मीडिया (Photo Upload)</span>
                        </h3>
                        <p class="text-xs text-gray-500">होमपेज कार्ड्स और आर्टिकिल के शीर्ष पर प्रदर्शित होगी</p>
                    </div>

                    <!-- Upload Type Tabs -->
                    <div class="flex bg-gray-100 p-1 rounded-lg text-xs font-semibold">
                        <button type="button" id="tab-file-btn" onclick="switchMediaTab('file')"
                            class="px-3 py-1 rounded-md bg-white text-gray-900 shadow-2xs">फ़ाइल अपलोड</button>
                        <button type="button" id="tab-url-btn" onclick="switchMediaTab('url')"
                            class="px-3 py-1 rounded-md text-gray-500 hover:text-gray-900">वेब URL</button>
                    </div>
                </div>

                <!-- Tab 1: Local File Upload -->
                <div id="media-tab-file" class="space-y-3">
                    <div class="border-2 border-dashed border-gray-300 hover:border-brand-red/60 rounded-xl p-6 text-center cursor-pointer bg-gray-50 hover:bg-red-50/20 transition group"
                        onclick="document.getElementById('image_file').click()">
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="hidden" onchange="previewUploadedImage(this)">
                        <div class="w-12 h-12 rounded-full bg-red-100 text-brand-red flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-xs font-bold text-gray-700">कंप्यूटर या मोबाइल से फ़ोटो चुनें</div>
                        <p class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP (अधिकतम 5 MB)</p>
                    </div>
                </div>

                <!-- Tab 2: Image URL -->
                <div id="media-tab-url" class="hidden space-y-2">
                    <label for="image_url" class="block text-xs font-semibold text-gray-700">ऑनलाइन इमेज लिंक (Image URL)</label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}"
                        placeholder="https://example.com/images/news-photo.jpg"
                        oninput="previewUrlImage(this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- Image Preview Display Box -->
                <div id="image-preview-wrapper" class="hidden p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-bold text-gray-700">फ़ोटो पूर्वावलोकन:</span>
                        <button type="button" onclick="clearImagePreview()" class="text-xs text-red-600 hover:underline font-bold">हटाएं</button>
                    </div>
                    <div class="w-full h-48 sm:h-64 rounded-lg overflow-hidden bg-gray-200 relative">
                        <img id="image-preview-img" src="" alt="पूर्वावलोकन" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <!-- 4. Tags and Keywords -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <label for="tags" class="block text-xs font-bold text-gray-700">
                    टैग्स एवं कीवर्ड्स (Tags - अल्पविराम / Comma से अलग करें)
                </label>
                <input type="text" id="tags" name="tags" value="{{ old('tags') }}"
                    placeholder="उदा: छत्तीसगढ़, रायपुर, विकास कार्य, मुख्यमंत्री, स्वास्थ्य"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">

                <!-- Popular Tag Badges -->
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
                    <span>प्रकाशन स्थिति (Publishing)</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                </h3>

                <!-- Category Selector -->
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-700 mb-1">
                        समाचार श्रेणी (Category) <span class="text-brand-red">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                        class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                        <option value="">-- श्रेणी चुनें --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- District Selector -->
                <div>
                    <label for="district_id" class="block text-xs font-bold text-gray-700 mb-1">
                        ज़िला (District - वैकल्पिक)
                    </label>
                    <select id="district_id" name="district_id"
                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red focus:bg-white transition">
                        <option value="">-- राज्य स्तर / सभी ज़िले --</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" {{ old('district_id') == $dist->id ? 'selected' : '' }}>
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
                        value="{{ old('author_name', auth()->user()->name ?? 'विशेष संवाददाता') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- Document / Circular Code -->
                <div>
                    <label for="document_no" class="block text-xs font-bold text-gray-700 mb-1">
                        विज्ञप्ति / दस्तावेज़ क्रमांक (वैकल्पिक)
                    </label>
                    <input type="text" id="document_no" name="document_no" value="{{ old('document_no') }}"
                        placeholder="उदा: PR/2026/09/105"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red font-mono">
                </div>

                <!-- Source URL -->
                <div>
                    <label for="source_url" class="block text-xs font-bold text-gray-700 mb-1">
                        मूल स्रोत लिंक (Source URL - यदि कोई हो)
                    </label>
                    <input type="url" id="source_url" name="source_url" value="{{ old('source_url') }}"
                        placeholder="https://..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- Publication Time -->
                <div>
                    <label for="published_at" class="block text-xs font-bold text-gray-700 mb-1">
                        प्रकाशन समय (Publish Datetime)
                    </label>
                    <input type="datetime-local" id="published_at" name="published_at"
                        value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-brand-red focus:border-brand-red">
                </div>

                <!-- SPECIAL FEED TOGGLES -->
                <div class="pt-3 border-t border-gray-200 space-y-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">विशेष फीड प्राथमिकता</span>

                    <!-- Breaking News Ticker Toggle -->
                    <label class="flex items-start space-x-3 p-3 rounded-lg border border-red-200 bg-red-50/60 hover:bg-red-50 cursor-pointer transition">
                        <input type="checkbox" name="is_breaking" value="1" {{ old('is_breaking') ? 'checked' : '' }}
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
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
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
                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-red to-red-600 hover:from-red-600 hover:to-brand-red text-white py-3 px-4 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition duration-150 transform active:scale-98 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>प्रकाशित करें और फीड में अपलोड करें</span>
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                        class="block text-center text-xs text-gray-500 hover:text-gray-800 font-semibold mt-2.5 py-1">
                        रद्द करें (Cancel)
                    </a>
                </div>
            </div>

            <!-- 2. LIVE FEED CARD PREVIEW (Realtime Feedback) -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <span class="text-xs font-bold text-gray-800">लाइव कार्ड प्रीव्यू (Feed Preview)</span>
                    <span class="text-[10px] bg-brand-red/10 text-brand-red font-bold px-2 py-0.5 rounded">लाइव</span>
                </div>
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-2xs">
                    <div class="h-32 bg-gray-100 relative overflow-hidden">
                        <img id="preview-card-img" src="/images/default-news.jpg" alt="Preview" class="w-full h-full object-cover">
                        <div class="absolute top-2 left-2 bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">
                            <span id="preview-card-cat">श्रेणी</span>
                        </div>
                    </div>
                    <div class="p-3 space-y-1.5">
                        <h4 id="preview-card-title" class="font-bold text-xs text-gray-900 line-clamp-2 leading-snug">
                            यहाँ आपका शीर्षक दिखेगा...
                        </h4>
                        <p id="preview-card-summary" class="text-[10px] text-gray-500 line-clamp-2">
                            संक्षिप्त विवरण यहाँ प्रदर्शित होगा...
                        </p>
                        <div class="flex justify-between items-center text-[9px] text-gray-400 pt-1 border-t border-gray-100">
                            <span id="preview-card-author">ब्यूरो</span>
                            <span>आज</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // 1. Media tab toggle (File vs URL)
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
        document.getElementById('preview-card-img').src = '/images/default-news.jpg';
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

    // 6. Realtime Text sync to Live Card Preview
    const titleInput = document.getElementById('title');
    const summaryInput = document.getElementById('summary');
    const contentInput = document.getElementById('news-content');
    const categorySelect = document.getElementById('category_id');
    const authorInput = document.getElementById('author_name');

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

    authorInput.addEventListener('input', function() {
        const text = this.value.trim();
        document.getElementById('preview-card-author').textContent = text || 'ब्यूरो';
    });

    // 7. Word & Reading Time stats
    contentInput.addEventListener('input', function() {
        const text = this.value.trim();
        const words = text ? text.split(/\s+/).length : 0;
        const readMinutes = Math.max(1, Math.ceil(words / 150));
        document.getElementById('word-stats').textContent = `${words} शब्द • ~${readMinutes} मिनट पठन`;
    });

    // 8. Simple Text formatting helpers for Content
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
