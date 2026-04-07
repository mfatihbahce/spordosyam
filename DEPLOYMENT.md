# Spordosyam - Canlı Sistem Dağıtım Rehberi

**Canlı URL:** https://altinpaletyuzmeakademi.com/spordosyam/spordosyam-web/

## Sunucu .env Ayarları

Sunucudaki `.env` dosyasında aşağıdaki değerlerin doğru olduğundan emin olun:

```env
APP_NAME=Spordosyam
APP_ENV=production
APP_DEBUG=false
APP_URL=https://altinpaletyuzmeakademi.com/spordosyam/spordosyam-web

# Veritabanı (sunucu bilgilerinize göre)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=spordosyam
DB_USERNAME=...
DB_PASSWORD=...

# Görseller yüklenmiyorsa
# MEDIA_URL_PREFIX=public

LOG_LEVEL=error
```

## Mobil Uygulama API

Mobil uygulama (`spordosyam-mobil`) production API adresi:

```
https://altinpaletyuzmeakademi.com/spordosyam/spordosyam-web/api
```

`lib/core/constants/app_constants.dart` içinde `baseUrl` bu adrese ayarlı olmalı.

## CORS

`config/cors.php` - API istekleri için `allowed_origins` = `['*']` (mobil uygulama dahil tüm origin'lere izin).

## Yayın Sonrası Kontroller

- [ ] `php artisan config:cache` çalıştırıldı
- [ ] `php artisan route:cache` çalıştırıldı
- [ ] `php artisan view:cache` çalıştırıldı
- [ ] `storage/` ve `bootstrap/cache/` yazılabilir
- [ ] `.env` dosyası güvenli (git'e eklenmemeli)
