# छत्तीसगढ़ न्यूज़ एक्सप्रेस (CG News Express)

छत्तीसगढ़ न्यूज़ एक्सप्रेस (CG News Express) एक आधुनिक, तेज़ और उत्तरदायी **निजी हिंदी समाचार पोर्टल** है। यह वेबसाइट छत्तीसगढ़ राज्य के विभिन्न संभागों/जिलों और विषयों (राजनीति, मनोरंजन, खेल, टेक, व्यापार) से संबंधित समाचारों की विस्तृत सूची और विस्तृत पठन अनुभव प्रदान करती है।

यह परियोजना डिफ़ॉल्ट लारावेल फ्रेमवर्क को एक पूर्ण-विशेषताओं वाले न्यूज़ पोर्टल के रूप में परिवर्तित करके बनाई गई है।

---

## 🚀 मुख्य विशेषताएं (Key Features)

1. **ब्रांड-संचालित आकर्षक डिज़ाइन (Brand Aesthetics):**
   - निजी समाचार मीडिया चैनलों के पैटर्न पर आधारित एक प्रीमियम रेड-चारकोल (`#E1261C` और `#1A1A1A`) थीम।
   - आकर्षक एनिमेशन से सुसज्जित लगातार स्क्रॉल होने वाला **"ब्रेकिंग न्यूज़"** टिकर।
   - पूरी तरह उत्तरदायी (Responsive Layout) ग्रिड जो मोबाइल और डेस्कटॉप दोनों पर बेहतरीन अनुभव देता है।

2. **अन्तरक्रियाशीलता (Interactivity):**
   - **जिला समाचार टैब (District Bulletins):** मुख्य पृष्ठ पर बस्तर, रायपुर, बिलासपुर, दुर्ग और सरगुजा जिलों की खबरों के लिए बिना पेज रीलोड हुए तुरंत स्विच होने वाले गतिशील बटन्स।
   - **मॉक ओपिनियन पोल (Interactive Opinion Poll):** पाठकों के लिए एक क्रियाशील पोल ब्लॉक जिसमें वोट करते ही परिणाम (Yes/No प्रतिशत) स्क्रीन पर दिखाई देते हैं।
   - **नागरिक राय (Citizen Comments):** विस्तृत समाचारों पर पाठकों के लिए एक मॉक कमेंट फीड, जिसमें तत्काल कमेंट पोस्ट करने की सुविधा है।

3. **अभिगम्यता और सुगमता (Accessibility Panel):**
   - **अक्षर आकार नियंत्रण (Font Scaling):** देवनागरी लिपि को सुगमता से पढ़ने के लिए मुख्य पृष्ठ और विस्तृत पठन पेज पर फ़ॉन्ट साइज़ को तुरंत छोटा/बड़ा करने (`A-`, `A`, `A+`) की सुविधा।
   - **कंट्रास्ट मोड (High Contrast Toggle):** दृष्टि सुगमता के लिए ब्लैक और येलो कलर कॉम्बिनेशन वाला कंट्रास्ट टॉगल।
   - **लाइव हिंदी कैलेंडर और घड़ी:** हिंदी पंचांग/तारीख और लाइव भारतीय समय (IST) का प्रदर्शन।

4. **प्रिंट अनुकूलित रूप (Print-Friendly Layout):**
   - लेखों को प्रिंट या पीडीएफ में सेव करते समय बिना किसी अतिरिक्त नेविगेशन, विज्ञापन या हेडर/फूटर के एक साफ-सुथरा न्यूज़पेपर क्लिपिंग रूप प्रदान करने के लिए विशेष प्रिंट मीडिया शैलियाँ।

---

## 🛠️ तकनीकी स्टैक (Technology Stack)

- **फ्रेमवर्क:** Laravel 12.x (PHP 8.2+)
- **डेटाबेस:** SQLite (सरल और पोर्टेबल डेवलपमेंट के लिए)
- **स्टाइलिंग:** Tailwind CSS v4.0 (Vite कंपाइलर के साथ)
- **फ्रंटएंड इंजन:** Laravel Blade templates, AlpineJS & Vanilla Javascript
- **बंडल कंपाइलर:** Vite

---

## 📦 संस्थापन और सेटअप (Installation & Setup)

स्थानीय स्तर पर इस परियोजना को चलाने के लिए नीचे दिए गए चरणों का पालन करें:

### १. प्रोजेक्ट को क्लोन/ओपन करें
सुनिश्चित करें कि आप न्यूज़ पोर्टल के मुख्य फ़ोल्डर (`news-portal`) में हैं।

### २. एनवायरनमेंट फाइल सेटअप करें
परियोजना में डेटाबेस के लिए SQLite कॉन्फ़िगर किया गया है। `.env` फ़ाइल में डेटाबेस कनेक्शन की जांच करें:
```env
DB_CONNECTION=sqlite
```
*नोट: `database/database.sqlite` नाम की फ़ाइल `database/` निर्देशिका में स्वतः मौजूद होनी चाहिए। यदि नहीं है, तो एक खाली फ़ाइल इस नाम से बना लें।*

### ३. कंपोजर और पैकेज इंस्टॉल करें
टर्मिनल में रन करें:
```bash
composer install
```

### ४. माइग्रेशन और सी़ड डेटा रन करें
डेटाबेस टेबल बनाने और उसमें 15+ विस्तृत समाचार लेख, जिले और श्रेणियां डालने के लिए निम्नलिखित कमांड चलाएँ:
```bash
php artisan migrate:fresh --seed
```
*सीडर व्यवस्थापक क्रेडेंशियल बनाता है: `email: editor@cgnewsexpress.com` और `password: password123`*

### ५. फ्रंटएंड एसेट्स कंपाइल करें
एसेट्स और शैलियों को कंपाइल करने के लिए:
```bash
npm install
npm run build
```

### ६. लोकल सर्वर प्रारंभ करें
लारावेल और वीट सर्वर को लाइव शुरू करने के लिए:
```bash
php artisan serve
```
वेबसाइट को अपने ब्राउज़र में **`http://127.0.0.1:8000`** पर खोलें।

---

## 📂 महत्वपूर्ण फाइलें और संरचना (Project Structure)

परियोजना में किए गए प्रमुख बदलाव इन स्थानों पर केंद्रित हैं:

- **रूट्स (Routing):** [`routes/web.php`](file:///c:/Users/Admin/Desktop/File/news-portal/routes/web.php) - समाचार और श्रेणियों के मार्गों की मैपिंग।
- **नियंत्रक (Controller):** [`app/Http/Controllers/NewsController.php`](file:///c:/Users/Admin/Desktop/File/news-portal/app/Http/Controllers/NewsController.php) - मुख्य पृष्ठ, श्रेणी, जिला और खोज तर्क।
- **डेटाबेस सी़डर (Seeding):** [`database/seeders/DatabaseSeeder.php`](file:///c:/Users/Admin/Desktop/File/news-portal/database/seeders/DatabaseSeeder.php) - हिंदी लेखों और जिलों का डेटा।
- **थीम स्टाइलिंग:** [`resources/css/app.css`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/css/app.css) - प्राइवेट न्यूज़ कलर्स औरanimations।
- **लेआउट और व्यूज (Blade Views):**
  - [`resources/views/layouts/app.blade.php`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/views/layouts/app.blade.php) - मुख्य ढांचा, हेडर, एक्सेसिबिलिटी स्क्रिप्ट।
  - [`resources/views/news/index.blade.php`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/views/news/index.blade.php) - मुख्य पृष्ठ, जिला टैब्स, पोल विजेट।
  - [`resources/views/news/show.blade.php`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/views/news/show.blade.php) - समाचार पठन पेज, प्रिंट मोड।
  - [`resources/views/news/category.blade.php`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/views/news/category.blade.php) - श्रेणीवार समाचार।
  - [`resources/views/news/district.blade.php`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/views/news/district.blade.php) - जिलावार समाचार।
  - [`resources/views/news/search.blade.php`](file:///c:/Users/Admin/Desktop/File/news-portal/resources/views/news/search.blade.php) - खोज परिणाम पेज।
