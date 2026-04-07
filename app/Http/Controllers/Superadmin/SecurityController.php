<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Helpers\EnvHelper;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index()
    {
        $settings = [
            'password_min_length' => env('PASSWORD_MIN_LENGTH', 8),
            'password_require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
            'password_require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
            'password_require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
            'password_require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', false),
            'session_lifetime' => config('session.lifetime'),
            'session_encrypt' => config('session.encrypt'),
            'two_factor_enabled' => env('TWO_FACTOR_ENABLED', false),
        ];

        return view('superadmin.security.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'password_min_length' => 'required|integer|min:6|max:32',
            'password_require_uppercase' => 'boolean',
            'password_require_lowercase' => 'boolean',
            'password_require_numbers' => 'boolean',
            'password_require_symbols' => 'boolean',
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'session_encrypt' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ]);

        // .env dosyasını güncelle
        $envData = [
            'PASSWORD_MIN_LENGTH' => $request->password_min_length,
            'PASSWORD_REQUIRE_UPPERCASE' => $request->has('password_require_uppercase') ? 'true' : 'false',
            'PASSWORD_REQUIRE_LOWERCASE' => $request->has('password_require_lowercase') ? 'true' : 'false',
            'PASSWORD_REQUIRE_NUMBERS' => $request->has('password_require_numbers') ? 'true' : 'false',
            'PASSWORD_REQUIRE_SYMBOLS' => $request->has('password_require_symbols') ? 'true' : 'false',
            'SESSION_LIFETIME' => $request->session_lifetime,
            'SESSION_ENCRYPT' => $request->has('session_encrypt') ? 'true' : 'false',
            'TWO_FACTOR_ENABLED' => $request->has('two_factor_enabled') ? 'true' : 'false',
        ];

        if (EnvHelper::updateEnvMultiple($envData)) {
            return redirect()->route('superadmin.security.index')
                ->with('success', 'Güvenlik ayarları başarıyla güncellendi. Değişikliklerin etkili olması için uygulamayı yeniden başlatmanız gerekebilir.');
        } else {
            return redirect()->route('superadmin.security.index')
                ->with('error', 'Ayarlar güncellenirken bir hata oluştu. Lütfen .env dosyasını manuel olarak kontrol edin.');
        }
    }
}
