<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class FooterSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'footer_description' => SiteSetting::get('footer_description', 'Spor okulları için kapsamlı yönetim sistemi.'),
            'footer_facebook_url' => SiteSetting::get('footer_facebook_url', ''),
            'footer_twitter_url' => SiteSetting::get('footer_twitter_url', ''),
            'footer_instagram_url' => SiteSetting::get('footer_instagram_url', ''),
            'footer_linkedin_url' => SiteSetting::get('footer_linkedin_url', ''),
            'footer_quick_links_title' => SiteSetting::get('footer_quick_links_title', 'Hızlı Linkler'),
            'footer_quick_links' => SiteSetting::get('footer_quick_links', []),
            'footer_about_title' => SiteSetting::get('footer_about_title', 'Hakkımızda'),
            'footer_about_text' => SiteSetting::get('footer_about_text', ''),
            'footer_contact_title' => SiteSetting::get('footer_contact_title', 'İletişim'),
            'footer_email' => SiteSetting::get('footer_email', ''),
            'footer_phone' => SiteSetting::get('footer_phone', ''),
            'footer_address' => SiteSetting::get('footer_address', ''),
            'footer_copyright' => SiteSetting::get('footer_copyright', 'Spordosyam. Tüm hakları saklıdır.'),
        ];

        return view('superadmin.footer-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'footer_description' => 'nullable|string|max:500',
            'footer_facebook_url' => 'nullable|url|max:255',
            'footer_twitter_url' => 'nullable|url|max:255',
            'footer_instagram_url' => 'nullable|url|max:255',
            'footer_linkedin_url' => 'nullable|url|max:255',
            'footer_quick_links_title' => 'nullable|string|max:255',
            'footer_quick_links' => 'nullable|array',
            'footer_quick_links.*.title' => 'required_with:footer_quick_links|string|max:255',
            'footer_quick_links.*.url' => 'required_with:footer_quick_links|string|max:255',
            'footer_about_title' => 'nullable|string|max:255',
            'footer_about_text' => 'nullable|string|max:1000',
            'footer_contact_title' => 'nullable|string|max:255',
            'footer_email' => 'nullable|email|max:255',
            'footer_phone' => 'nullable|string|max:50',
            'footer_address' => 'nullable|string|max:500',
            'footer_copyright' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'footer_quick_links') {
                SiteSetting::set($key, $value, 'json');
            } else {
                SiteSetting::set($key, $value, 'text');
            }
        }

        return redirect()->route('superadmin.footer-settings.index')
            ->with('success', 'Footer ayarları başarıyla güncellendi.');
    }
}
