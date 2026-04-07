<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Helpers\EnvHelper;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_tagline' => SiteSetting::get('app_tagline', ''),
            'app_locale' => config('app.locale'),
            'app_timezone' => config('app.timezone'),
            'app_debug' => config('app.debug'),
            'app_env' => config('app.env'),
            'active_design' => SiteSetting::get('active_design', 'design_1'),
            'homepage_theme' => SiteSetting::get('homepage_theme', 'theme_1'),
            'date_format' => SiteSetting::get('date_format', 'd.m.Y'),
            'time_format' => SiteSetting::get('time_format', 'H:i'),
            'default_demo_days' => (int) SiteSetting::get('default_demo_days', 14),
            'laravel_version' => \Illuminate\Foundation\Application::VERSION,
            'php_version' => PHP_VERSION,
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_tagline' => 'nullable|string|max:255',
            'app_locale' => 'required|string|in:tr,en',
            'app_timezone' => 'required|string|max:50',
            'active_design' => 'required|string|in:design_1,design_2',
            'homepage_theme' => 'required|string|in:theme_1,theme_2',
            'app_debug' => 'boolean',
            'date_format' => 'nullable|string|max:50',
            'time_format' => 'nullable|string|max:50',
            'default_demo_days' => 'nullable|integer|min:1|max:365',
        ]);

        // .env dosyasını güncelle
        $envData = [
            'APP_NAME' => $request->app_name,
            'APP_URL' => $request->app_url,
            'APP_LOCALE' => $request->app_locale,
            'APP_TIMEZONE' => $request->app_timezone,
            'APP_DEBUG' => $request->has('app_debug') ? 'true' : 'false',
        ];

        // SiteSetting ile saklanan alanlar
        SiteSetting::set('app_tagline', $request->input('app_tagline', ''), 'text');
        SiteSetting::set('active_design', $request->input('active_design', 'design_1'), 'text');
        SiteSetting::set('homepage_theme', $request->input('homepage_theme', 'theme_1'), 'text');
        SiteSetting::set('date_format', $request->input('date_format', 'd.m.Y'), 'text');
        SiteSetting::set('time_format', $request->input('time_format', 'H:i'), 'text');
        $demoDays = $request->filled('default_demo_days') ? (int) $request->default_demo_days : 14;
        $demoDays = max(1, min(365, $demoDays));
        SiteSetting::set('default_demo_days', $demoDays, 'text');

        if (EnvHelper::updateEnvMultiple($envData)) {
            return redirect()->route('superadmin.settings.index')
                ->with('success', 'Ayarlar başarıyla güncellendi. Değişikliklerin etkili olması için uygulamayı yeniden başlatmanız gerekebilir.');
        } else {
            return redirect()->route('superadmin.settings.index')
                ->with('error', 'Uygulama ayarları (.env) güncellenemedi; varsayılan demo süresi kaydedildi. Lütfen .env dosyasını manuel kontrol edin.');
        }
    }
}
