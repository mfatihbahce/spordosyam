# Spordosyam Mobil Uygulama - Geliştirme Prompt'u

Aşağıdaki prompt'u mobil uygulama geliştirirken (Flutter, React Native vb.) kullanabilirsiniz. Kopyalayıp yapıştırın ve gerekirse özelleştirin.

---

## Örnek Prompt

```
Spordosyam adında bir spor okulu yönetim sistemi için mobil uygulama geliştiriyorum. Web platformu hazır ve çalışıyor. Mobil uygulama web platformundaki Veli ve Antrenör panellerinin birebir aynısı olmalı.

## Proje Bilgisi
- Uygulama adı: Spordosyam
- Hedef kitle: Spor okulu velileri ve antrenörleri
- Dil: Türkçe
- API Base URL: https://altinpaletyuzmeakademi.com/spordosyam/spordosyam-web/api

## Kimlik Doğrulama
- POST /api/login ile e-posta ve şifre ile giriş
- Yanıt: token, token_type: "Bearer", user (id, name, email, role)
- Tüm API isteklerinde header: Authorization: Bearer {token}
- Sadece role: "parent" veya "coach" kullanıcılar giriş yapabilir
- 401/403 hatalarında login ekranına yönlendir

## Kullanıcı Rolleri
1. VELİ (parent): Çocuğunun devam, gelişim, ödeme bilgilerini görür
2. ANTRENÖR (coach): Sınıflarını, yoklamalarını, mesajlarını yönetir

## VELİ UYGULAMASI - Ekranlar ve Özellikler

### 1. Giriş Ekranı
- E-posta ve şifre alanları
- "Giriş Yap" butonu
- "Şifremi Unuttum" linki (web'de /sifremi-unuttum)
- Hata mesajları: "Geçersiz giriş bilgileri", "Bu uygulama sadece veli ve antrenör hesapları için geçerlidir", "Okul lisans süresi dolmuş"

### 2. Menü (Drawer/Sidebar)
GET /api/parent/menu ile menü yapısını al. Dinamik render et.
- makeup_class_enabled: false ise "Telafi Dersleri" menüde GÖSTERME

Menü sırası ve gruplar:
- Dashboard
- [Öğrenci] Çocuğum
- [Takip] Yoklamalar, Gelişim Notları
- [Telafi] Telafi Dersleri (sadece makeup_class_enabled ise)
- [İçerik] Paylaşımlar
- [Ödemeler] Aidatlarım, Ödeme Geçmişi, Faturalar
- [İletişim] Mesajlar
- [Hesap] Profil

### 3. Dashboard
GET /api/parent/dashboard
- Hızlı işlem butonları: Çocuğum, Yoklamalar, Gelişim Notları, Ödemeler, Paylaşımlar, Telafi Dersleri (makeup_class_enabled ise)
- KPI kartları: Çocuğum sayısı, Katılım Oranı (%), Bekleyen Ödeme (₺), Gelişim Notları sayısı
- Ders Takvimi: GET /api/parent/calendar ile ayrı takvim API'si (start_date, end_date opsiyonel)
- Yaklaşan Ödemeler (7 gün): upcoming_fees listesi, "Tümü" linki Aidatlarım'a
- Son Gelişim Notları: recent_progress (5 adet), "Tümü" linki Gelişim Notları'na
- Son Yoklamalar: recent_attendances (10 adet), tablo: Öğrenci, Sınıf, Tarih, Durum (Katıldı/Devamsız)
- Son Paylaşımlar: recent_media (5 adet), "Tümü" linki Paylaşımlar'a

### 4. Çocuğum (Students)
GET /api/parent/students
- Her öğrenci kartı: ad, sınıf, branş, okul
- Yoklama ve Ödeme linkleri

### 5. Yoklamalar (Attendances)
GET /api/parent/attendances
- Liste: tarih, saat, öğrenci, sınıf, durum (Katıldı/Devamsız), notlar

### 6. Gelişim Notları (Progress)
GET /api/parent/progress
- Liste: tarih, öğrenci, sınıf, antrenör, not, puan

### 7. Telafi Dersleri
GET /api/parent/makeup-sessions
- Liste: tarih, saat, öğrenci, şube, antrenör

### 8. Paylaşımlar (Media)
GET /api/parent/media
- Grid/liste: başlık, açıklama, tarih, file_url, file_url_secure
- file_url çalışmazsa file_url_secure kullan: GET /api/parent/media/{id}/file + Authorization: Bearer {token}
- React Native Image: source={{ uri: file_url_secure, headers: { Authorization: 'Bearer '+token } }}

### 9. Aidatlarım (Payments)
GET /api/parent/payments
- Ödenmemiş aidatlar (fees): id, öğrenci, etiket, tutar, vade tarihi
- Her biri için "Öde" butonu
- Öde tıklanınca: Kart formu göster (card_holder_name, card_number, expire_month, expire_year, cvc)
- POST /api/parent/payments/{studentFee}/pay ile ödeme yap (studentFee = fee.id)

### 10. Ödeme Geçmişi
GET /api/parent/payments/history
- Tamamlanmış ödemeler: tutar, tarih, öğrenci, aidat bilgisi

### 11. Faturalar
GET /api/parent/invoices
- Liste: etiket, tutar, vade, durum, öğrenci, son ödeme

### 12. Mesajlar
GET /api/parent/messages
- Konuşma listesi: öğrenci, antrenör, son mesaj
- "Yeni Mesaj" butonu
- Konuşma detayı: GET /api/parent/messages/{id}
- Yeni mesaj: POST /api/parent/messages (student_id, coach_id, body)
- Cevap: POST /api/parent/messages/{id}/reply (body)

### 13. Profil
GET /api/parent/profile
PUT /api/parent/profile
- Alanlar: name, email, phone, address, identity_number
- Şifre değiştirme: current_password, password, password_confirmation

---

## ANTRENÖR UYGULAMASI - Ekranlar ve Özellikler

### 1. Giriş
- Aynı login, role: "coach" ile giriş

### 2. Menü (sabit - API yok)
- Dashboard
- Sınıflar
- Yoklamalar
- Paylaşımlar (Medya)
- Mesajlar
- Profil
- Telafi Dersleri

### 3. Dashboard
GET /api/coach/dashboard
- Sınıf sayısı, toplam yoklama, katılım oranı
- Sınıflar listesi

### 4. Sınıflar
GET /api/coach/classes
- Sınıf adı, öğrenci sayısı

### 5. Yoklamalar
GET /api/coach/attendances

### 6. Paylaşımlar
GET /api/coach/media, GET /api/coach/media/{id}

### 7. Mesajlar
GET /api/coach/messages
GET /api/coach/messages/{id}
POST /api/coach/messages/{id}/reply

### 8. Profil
GET /api/coach/profile | PUT /api/coach/profile | name, email, phone

### 9. Telafi Dersleri
GET /api/coach/makeup-sessions

---

## Teknik Gereksinimler
1. Token'ı güvenli sakla (SecureStorage/Keychain)
2. Her istekte header: Accept: application/json, Authorization: Bearer {token}
3. 401/403: token sil, login ekranına yönlendir
4. 404/422/500: kullanıcıya anlamlı mesaj göster
5. Tüm tarihler Türkçe format (dd.MM.yyyy)
6. Para birimi: ₺ (TL formatında)
7. Offline: token varsa cache'den veri göster, sync gerekirse uyar

## API Referans
Proje içinde API_REFERANS.json dosyası var. Tüm endpoint'ler, request/response formatları orada tanımlı. Eksiksiz uygula.

## Önemli
- Web platformundaki her ekran ve özellik mobilde de olmalı
- Menü sırası ve gruplar web ile aynı
- Telafi Dersleri sadece okul makeup_class_enabled ise görünsün
- Dashboard'daki tüm widget'lar (KPI, takvim, listeler) eksiksiz
```

---

## Kısa Versiyon (Özet Prompt)

```
Spordosyam mobil uygulama: Veli ve Antrenör için. API: https://altinpaletyuzmeakademi.com/spordosyam/spordosyam-web/api
Auth: POST /login ile token al, Bearer token ile tüm istekler.
Veli: Dashboard (stats, takvim, yaklaşan ödemeler, son yoklamalar, gelişim notları, paylaşımlar), Çocuğum, Yoklamalar, Gelişim Notları, Telafi Dersleri (makeup_class_enabled ise), Paylaşımlar, Aidatlarım, Ödeme Geçmişi, Faturalar, Mesajlar, Profil.
Antrenör: Dashboard, Sınıflar, Yoklamalar, Paylaşımlar, Mesajlar, Profil, Telafi Dersleri.
Menü: Veli için GET /parent/menu ile dinamik; Antrenör için sabit menü. API_REFERANS.json'daki tüm endpoint'leri kullan. Web platformu ile birebir aynı.
```

---

## Dosya Referansları
- `API_REFERANS.json` - Tüm API endpoint'leri
- `API_DOKUMANTASYON.md` - Detaylı dokümantasyon
- `Spordosyam_API.postman_collection.json` - Postman test
