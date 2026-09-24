<!DOCTYPE html>
<html lang="hi" class="h-full bg-gray-900">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>एडमिन लॉगिन | छत्तीसगढ़ न्यूज़ एक्सप्रेस</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Rajdhani:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gradient-to-br from-brand-dark-deep via-gray-950 to-brand-dark flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo -->
        <a href="{{ route('news.index') }}" class="inline-flex items-center space-x-3 group">
            <div class="w-14 h-14 bg-brand-red rounded-2xl flex items-center justify-center text-white font-display font-black text-2xl shadow-lg shadow-red-900/50 group-hover:scale-105 transition duration-200">
                CG
            </div>
            <div class="text-left">
                <h1 class="text-2xl font-black font-display text-white tracking-tight leading-none">
                    छत्तीसगढ़ न्यूज़ <span class="text-brand-red">एक्सप्रेस</span>
                </h1>
                <p class="text-xs text-gray-400 font-semibold tracking-wider uppercase mt-1">एडमिन कंट्रोल रूम</p>
            </div>
        </a>
        <h2 class="mt-6 text-xl font-bold tracking-tight text-white">
            संपादकीय पैनल में प्रवेश करें
        </h2>
        <p class="mt-1 text-xs text-gray-400">
            समाचार लेखन, लाइव फीड अपलोड और बुलेटिन प्रबंधन हेतु
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-gray-900/90 backdrop-blur border border-gray-800 py-8 px-6 shadow-2xl rounded-2xl sm:px-10">

            @if(session('info'))
                <div class="mb-5 bg-blue-950/60 border border-blue-800 text-blue-300 text-xs p-3 rounded-lg flex items-center space-x-2">
                    <svg class="w-4 h-4 shrink-0 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 bg-red-950/60 border border-red-800 text-red-300 text-xs p-3 rounded-lg">
                    <div class="font-bold mb-1">लॉगिन विफल:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-5" action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">ईमेल आईडी (Email)</label>
                    <div class="mt-1.5 relative">
                        <input id="email" name="email" type="email" autocomplete="email" required
                            value="{{ old('email', 'editor@cgnewsexpress.com') }}"
                            placeholder="admin@cgnewsexpress.com"
                            class="w-full px-4 py-2.5 bg-gray-800/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red transition placeholder-gray-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider">पासवर्ड (Password)</label>
                    <div class="mt-1.5 relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            value="password123"
                            placeholder="••••••••"
                            class="w-full px-4 py-2.5 bg-gray-800/80 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-red focus:border-brand-red transition placeholder-gray-500">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" checked
                            class="h-4 w-4 rounded border-gray-700 bg-gray-800 text-brand-red focus:ring-brand-red">
                        <label for="remember" class="ml-2 block text-xs text-gray-300">मुझे याद रखें (Remember me)</label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-brand-red to-red-600 hover:from-red-600 hover:to-brand-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition duration-150 transform active:scale-98">
                        एडमिन पैनल में लॉगिन करें &rarr;
                    </button>
                </div>
            </form>

            <!-- Quick Demo Auto-Fill Box -->
            <div class="mt-6 pt-5 border-t border-gray-800">
                <div class="bg-gray-800/50 p-3 rounded-lg border border-gray-700 text-xs text-gray-300 flex items-center justify-between">
                    <div>
                        <span class="text-brand-accent font-bold block text-[11px] uppercase">डिफ़ॉल्ट एडमिन क्रेडेंशियल:</span>
                        <div class="text-gray-400 font-mono text-[11px] mt-0.5">
                            User: <strong class="text-white">editor@cgnewsexpress.com</strong><br>
                            Pass: <strong class="text-white">password123</strong>
                        </div>
                    </div>
                    <button type="button" onclick="fillDemo()"
                        class="px-2.5 py-1 bg-brand-dark-deep hover:bg-brand-red text-white text-[11px] font-bold rounded border border-gray-600 transition">
                        ऑटो-फिल
                    </button>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('news.index') }}" class="text-xs text-gray-400 hover:text-white flex items-center justify-center space-x-1 transition">
                    <span>&larr;</span>
                    <span>पोर्टल के मुख्य पृष्ठ पर लौटें</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function fillDemo() {
            document.getElementById('email').value = 'editor@cgnewsexpress.com';
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>

</html>
