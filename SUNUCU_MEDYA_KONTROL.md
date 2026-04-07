# Sunucuda Medya (Görsel) Yüklenmiyorsa Kontrol Listesi

## Yapılan Kod Değişikliği
`asset()` yerine `media_url()` kullanılıyor. Bu fonksiyon URL'i **request'ten** alır, böylece APP_URL yanlış olsa bile subpath ile doğru çalışır.

## Dosyalar Var Ama Görünmüyorsa

**Muhtemel neden:** Document root proje kökü, dosyalar ise `public/uploads/` altında. URL'de `public` eksik.

**Çözüm:** Sunucu `.env` dosyasına ekleyin:
```
MEDIA_URL_PREFIX=public
```

Bu ayarla URL şöyle olur: `https://site.com/.../public/uploads/media/1/foto.jpg`

## Sunucuda Kontrol Edilecekler

### 1. `public/uploads` klasörü
- Dosya yolu: `public/uploads/media/{okul_id}/`
- Bu klasör sunucuda **var mı** ve **yazılabilir mi** kontrol edin
- FTP/cPanel ile: `public_html/spordosyam/spordosyam-web/public/uploads/` (veya sunucu yapınıza göre)

### 2. Klasör izinleri
```bash
chmod -R 755 public/uploads
# veya web sunucusu kullanıcısı yazabilsin:
chown -R www-data:www-data public/uploads  # Linux/Apache
```

### 3. Dosyalar gerçekten yükleniyor mu?
- Sunucuda `public/uploads/media/1/` (veya ilgili okul id) altında `.jpg`, `.png` vb. dosyalar var mı?
- Yoksa: Yükleme sırasında hata olabilir veya `public_path()` farklı bir dizine işaret ediyor olabilir

### 4. .htaccess veya web sunucusu
- `uploads` klasörüne doğrudan erişim engellenmemiş olmalı
- Apache: `public/.htaccess` sadece mevcut olmayan dosyaları `index.php`'ye yönlendirir; `uploads/` altındaki gerçek dosyalar doğrudan sunulur

### 5. Deployment sonrası
- `composer dump-autoload` çalıştırıldı mı? (yeni `media_url` helper için)
- Config cache: `php artisan config:clear` (gerekirse)
