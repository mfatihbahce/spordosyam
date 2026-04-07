<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mobil login (JSON).
     * Veli ve antrenör için token döner. Bearer token ile API istekleri yapılır.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Geçersiz giriş bilgileri.',
            ], 401);
        }

        $user = $request->user();

        // Sadece veli ve antrenör API kullanabilir
        if (!in_array($user->role, ['parent', 'coach'])) {
            Auth::logout();
            return response()->json([
                'message' => 'Bu uygulama sadece veli ve antrenör hesapları için geçerlidir.',
            ], 403);
        }

        // Lisans kontrolü
        $school = $user->getSchoolForLicense();
        if ($school && $school->isLicenseExpired()) {
            Auth::logout();
            return response()->json([
                'message' => 'Okul lisans süresi dolmuş. Lütfen yöneticinizle iletişime geçin.',
            ], 403);
        }

        // Mevcut tokenları temizle, yeni token oluştur (tek cihaz için)
        $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Mevcut kullanıcı bilgisi (token ile).
     */
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Token'ı sil
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Oturum kapatıldı.',
        ]);
    }
}

