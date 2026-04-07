<?php

if (!function_exists('media_url')) {
    /**
     * Medya dosyası için tam URL oluşturur.
     * Sunucuda subpath veya document root farklıysa .env ile ayarlanabilir.
     *
     * .env seçenekleri:
     * - MEDIA_URL_PREFIX=public  → URL'e /public eklenir (document root proje köküyse)
     * - Boş bırakılırsa → request'ten base URL alınır (varsayılan)
     */
    function media_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $path = ltrim($path, '/');
        $prefix = rtrim((string) config('app.media_url_prefix', ''), '/');
        if ($prefix !== '') {
            $path = $prefix . '/' . $path;
        }

        if (app()->runningInConsole()) {
            return rtrim(config('app.url'), '/') . '/' . $path;
        }

        $base = request()->getSchemeAndHttpHost() . request()->getBasePath();
        return rtrim($base, '/') . '/' . $path;
    }
}
