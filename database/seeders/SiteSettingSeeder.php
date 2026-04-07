<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Footer Açıklama
        SiteSetting::set('footer_description', 'Spor okulları için kapsamlı yönetim sistemi. Öğrencilerinizi yönetin, ödemeleri takip edin ve velilerle iletişim kurun.', 'text');

        // Sosyal Medya Linkleri
        SiteSetting::set('footer_facebook_url', 'https://facebook.com/spordosyam', 'text');
        SiteSetting::set('footer_twitter_url', 'https://twitter.com/spordosyam', 'text');
        SiteSetting::set('footer_instagram_url', 'https://instagram.com/spordosyam', 'text');
        SiteSetting::set('footer_linkedin_url', 'https://linkedin.com/company/spordosyam', 'text');

        // Hızlı Linkler
        SiteSetting::set('footer_quick_links_title', 'Hızlı Linkler', 'text');
        SiteSetting::set('footer_quick_links', [
            ['title' => 'Özellikler', 'url' => '#ozellikler'],
            ['title' => 'Nasıl Çalışır', 'url' => '#nasil-calisir'],
            ['title' => 'Avantajlar', 'url' => '#avantajlar'],
            ['title' => 'Demo Talep Et', 'url' => '/register'],
        ], 'json');

        // Destek Linkleri
        SiteSetting::set('footer_support_title', 'Destek', 'text');
        SiteSetting::set('footer_support_links', [
            ['title' => 'Yardım Merkezi', 'url' => '/yardim'],
            ['title' => 'İletişim', 'url' => '/iletisim'],
            ['title' => 'SSS', 'url' => '/sss'],
            ['title' => 'Giriş Yap', 'url' => '/login'],
        ], 'json');

        // İletişim Bilgileri
        SiteSetting::set('footer_email', 'info@spordosyam.com', 'text');
        SiteSetting::set('footer_phone', '+90 (212) 555 00 00', 'text');
        SiteSetting::set('footer_address', 'İstanbul, Türkiye', 'text');

        // Copyright
        SiteSetting::set('footer_copyright', 'Spordosyam. Tüm hakları saklıdır.', 'text');

        // Footer Hakkında Başlık
        SiteSetting::set('footer_about_title', 'Hakkımızda', 'text');
        SiteSetting::set('footer_about_text', 'Spordosyam, spor okullarının dijital dönüşümü için geliştirilmiş kapsamlı bir yönetim sistemidir. Öğrenci yönetiminden ödeme takibine kadar tüm süreçleri tek platformda yönetin.', 'text');

        // İletişim Başlık
        SiteSetting::set('footer_contact_title', 'İletişim', 'text');
    }
}
