# Spordosyam Mobil API Dokümantasyonu

Mobil uygulama (veli ve antrenör) için REST API. Tüm isteklerde `Accept: application/json` ve `Content-Type: application/json` kullanın.

## Base URL
```
http://localhost/spordosyam-app-v1/spordosyam-web/public/api
```
veya production:
```
https://your-domain.com/api
```

---

## Kimlik Doğrulama (Bearer Token)

### 1. Giriş
```http
POST /api/login
Content-Type: application/json

{
  "email": "veli@example.com",
  "password": "sifre123"
}
```

**Başarılı yanıt (200):**
```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Ahmet Yılmaz",
    "email": "veli@example.com",
    "role": "parent"
  }
}
```

**Hatalı yanıt (401):**
```json
{
  "message": "Geçersiz giriş bilgileri."
}
```

### 2. Tüm API isteklerinde
```http
Authorization: Bearer {token}
```

### 3. Çıkış
```http
POST /api/logout
Authorization: Bearer {token}
```

### 4. Mevcut kullanıcı
```http
GET /api/me
Authorization: Bearer {token}
```

---

## Veli (Parent) API

Platformdaki veli paneli ile birebir aynı yapı. Dashboard tüm verileri içerir, her menü için ayrı endpoint.

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| GET | /api/parent/menu | Sidebar menü yapısı (platformdaki sıra ve gruplar) |
| GET | /api/parent/dashboard | Dashboard - stats, yaklaşan ödemeler, son yoklamalar, gelişim notları, paylaşımlar, takvim |
| GET | /api/parent/calendar | Ders takvimi (öğrenci ders takibi, start_date/end_date opsiyonel) |
| GET | /api/parent/students | Çocuğum |
| GET | /api/parent/attendances | Yoklamalar |
| GET | /api/parent/progress | Gelişim Notları |
| GET | /api/parent/makeup-sessions | Telafi Dersleri |
| GET | /api/parent/media | Paylaşımlar |
| GET | /api/parent/media/{id} | Paylaşım detayı |
| GET | /api/parent/media/{id}/file | Medya dosyası (token ile) |
| GET | /api/parent/payments | Aidatlarım |
| GET | /api/parent/payments/history | Ödeme Geçmişi |
| POST | /api/parent/payments/{studentFee}/pay | Aidat ödeme |
| GET | /api/parent/invoices | Faturalar |
| GET | /api/parent/messages | Mesajlar |
| GET | /api/parent/messages/{id} | Konuşma detayı |
| POST | /api/parent/messages | Yeni mesaj |
| POST | /api/parent/messages/{id}/reply | Mesaja cevap |
| GET | /api/parent/profile | Profil |
| PUT | /api/parent/profile | Profil güncelle |

---

## Antrenör (Coach) API

| Method | Endpoint | Açıklama |
|--------|----------|----------|
| GET | /api/coach/dashboard | Dashboard özeti |
| GET | /api/coach/classes | Sınıflar listesi |
| GET | /api/coach/attendances | Yoklama kayıtları |
| GET | /api/coach/media | Medya listesi |
| GET | /api/coach/media/{id} | Medya detayı |
| GET | /api/coach/media/{id}/file | Medya dosyası (token ile) |
| GET | /api/coach/messages | Mesaj konuşmaları |
| GET | /api/coach/messages/{id} | Konuşma detayı |
| POST | /api/coach/messages/{id}/reply | Mesaja cevap |
| GET | /api/coach/profile | Profil bilgisi |
| PUT | /api/coach/profile | Profil güncelle |
| GET | /api/coach/makeup-sessions | Telafi dersleri |

---

## Örnek İstekler

### Veli - Dashboard
```http
GET /api/parent/dashboard
Authorization: Bearer {token}
```

### Veli - Yeni mesaj gönder
```http
POST /api/parent/messages
Authorization: Bearer {token}
Content-Type: application/json

{
  "student_id": 5,
  "coach_id": 2,
  "body": "Merhaba, çocuğumun durumu hakkında bilgi alabilir miyim?"
}
```

### Veli - Aidat ödeme
```http
POST /api/parent/payments/123/pay
Authorization: Bearer {token}
Content-Type: application/json

{
  "card_holder_name": "AHMET YILMAZ",
  "card_number": "5528790000000008",
  "expire_month": "12",
  "expire_year": "2030",
  "cvc": "123"
}
```

---

## Görsel/Medya Erişimi

Medya yanıtlarında iki URL bulunur:

- **file_url**: Doğrudan erişim (public). Sunucuda `APP_URL` doğru ayarlı olmalı.
- **file_url_secure**: Token ile erişim. `GET /api/parent/media/{id}/file` veya `/api/coach/media/{id}/file` — istekte `Authorization: Bearer {token}` header'ı gönderin.

**Mobil uygulama (React Native):** `file_url` çalışmıyorsa `file_url_secure` kullanın. Image bileşeninde:
```javascript
<Image source={{ 
  uri: file_url_secure, 
  headers: { Authorization: `Bearer ${token}` } 
}} />
```

**Kontrol listesi:**
- Production `.env` içinde `APP_URL` tam adres olmalı (örn: `https://altinpaletyuzmeakademi.com/spordosyam/spordosyam-web`)
- Android: `AndroidManifest.xml` içinde `INTERNET` izni
- iOS: `Info.plist` içinde App Transport Security ayarları (HTTPS kullanıyorsanız sorun olmaz)

---

## Hata Kodları

| Kod | Açıklama |
|-----|----------|
| 401 | Geçersiz veya eksik token |
| 403 | Yetkisiz (rol uyumsuz, lisans süresi dolmuş) |
| 404 | Kayıt bulunamadı |
| 422 | Validasyon hatası |
| 500 | Sunucu hatası |
