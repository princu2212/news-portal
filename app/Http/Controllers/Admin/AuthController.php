<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle an admin login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'कृपया अपना ईमेल दर्ज करें।',
            'email.email' => 'कृपया एक मान्य ईमेल पता दर्ज करें।',
            'password.required' => 'कृपया पासवर्ड दर्ज करें।',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'सफलतापूर्वक एडमिन पोर्टल में लॉगिन किया गया!');
        }

        return back()->withErrors([
            'email' => 'प्रदान किए गए क्रेडेंशियल्स हमारे रिकॉर्ड से मेल नहीं खाते हैं।',
        ])->onlyInput('email');
    }

    /**
     * Log the admin user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('news.index')
            ->with('info', 'आप एडमिन पोर्टल से सफलतापूर्वक लॉगआउट हो गए हैं।');
    }
}
