<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Role-based redirect
            return match($user->role) {
                'superadmin' => redirect()->route('superadmin.dashboard'),
                'admin' => redirect()->route('admin.dashboard'),
                'coach' => redirect()->route('coach.dashboard'),
                'parent' => redirect()->route('parent.dashboard'),
                default => redirect()->route('home'),
            };
        }

        // Demo talebi henüz onaylanmamış mı?
        $pendingApplication = Application::where('email', $request->email)->where('status', 'pending')->first();
        if ($pendingApplication) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Demo talebiniz henüz başlatılmadı. Talebiniz değerlendirildikten sonra e-posta ile bilgilendirileceksiniz. Sorularınız için iletişim sayfamızdan bize ulaşabilirsiniz.',
                ]);
        }

        throw ValidationException::withMessages([
            'email' => 'Girdiğiniz bilgiler kayıtlarımızla eşleşmiyor.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
