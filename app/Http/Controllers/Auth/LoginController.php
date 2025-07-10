<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AppLog;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            AppLog::create([
                'user_id' => Auth::id(),
                'action' => 'login',
                'module' => 'auth',
                'ip_address' => $request->ip(),
            ]);
            return redirect()->intended('dashboard');
        }

        AppLog::create([
            'user_id' => null,
            'action' => 'login_failed',
            'module' => 'auth',
            'ip_address' => $request->ip(),
        ]);
        return back()->withErrors([
            'email' => 'Email or password is incorrect.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        AppLog::create([
            'user_id' => $userId,
            'action' => 'logout',
            'module' => 'auth',
            'ip_address' => $request->ip(),
        ]);
        return redirect('/login');
    }
}
