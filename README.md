# 📦 Inventory Mini - Modern Stok & Envanter Yönetim Sistemi

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![DataTables](https://img.shields.io/badge/DataTables-Bfrtip-0075FF?style=for-the-badge)](https://datatables.net)
[![Tests Passing](https://img.shields.io/badge/Tests-38%20Passed-22c55e?style=for-the-badge&logo=githubactions&logoColor=white)](https://github.com/AhmedER23/Inventory-Mini)
[![Multi-Language](https://img.shields.io/badge/i18n-TR%20%7C%20EN%20%7C%20AR%20(RTL)-0284c7?style=for-the-badge)](lang)

Küçük ve orta ölçekli işletmelerin, depoların ve e-ticaret satıcılarının envanterlerini, stok giriş/çıkış hareketlerini, kritik stok seviyelerini ve personel yetkilerini tek bir merkezden kolayca yönetmelerini sağlayan tam teşekküllü, çok dilli bir web uygulaması.

---

## 🌟 Öne Çıkan Özellikler

### 1. 🏢 Kurumsal Rol & Personel Yetkilendirme Mimarisi
- **Tek Birincil Yönetici (Admin):** Sistemin birincil sahibi (`admin@example.com`) önceden tanımlı güvenli giriş sağlar.
- **Korumalı Personel Kaydı:** Dışarıdan kaydolan yeni çalışanlar asla yetki seçemez; varsayılan olarak **Personel (İzleyici)** rolüyle başlar.
- **Dinamik Görevlendirme Paneli (`/settings`):** Sistem Yöneticisi, Ayarlar panelinden tüm çalışanları tek tıkla **Depo Sorumlusu (Manager)** veya **Sistem Yöneticisi (Admin)** yapabilir; ayrılan personeli silebilir.

### 2. 🔐 6 Haneli PIN ile Gerçek E-posta Doğrulaması
- Yeni kayıt olan çalışanların e-posta adresine Gmail SMTP entegrasyonu ile 6 haneli tek kullanımlık doğrulama kodu (OTP) iletilir.
- 15 dakikalık zaman aşımı süresi ve yeniden kod isteme desteği bulunur.
- Çift mail karışıklığını önlemek için standart linkli e-postalar engellenmiş, yalnızca şık PIN kodu maili gönderilir.

### 3. 🌐 Tam Kapsamlı Çok Dilli Arayüz (TR / EN / AR)
- **Türkçe (Varsayılan):** Tamamen yerel işletme terimleri (Sistem Yöneticisi, Depo Sorumlusu, Personel, Adet, Koli, Kg...).
- **İngilizce:** Uluslararası ekipler için hazır.
- **Arapça (RTL Desteği):** `dir="rtl"`, Bootstrap RTL ve DataTables RTL uyumu ile sağdan sola kusursuz akış (Örn: `10 قطعة`).

### 4. ⚡ Hızlı Stok Yönetimi & Barkod Etiketi Yazdırma
- **Hızlı Giriş/Çıkış:** Gösterge panelindeki hızlı butonlar veya Ürünler tablosundaki tek tıkla `+` / `-` butonlarıyla anında miktar güncelleme.
- **Yetersiz Stok Koruması:** Mevcut stoktan fazla çıkış yapılmak istendiğinde işlem engellenir ve kullanıcı aynı sayfada tekil, şık bir Toastr uyarısıyla bilgilendirilir.
- **Birim Desteği:** Adet, Koli, Kg, Litre, Paket, Metre.
- **Code128 Barkod & QR Etiket:** Ürün detay sayfasında dinamik barkod üretilir ve tek tıkla standart ürün etiketi yazıcı çıktısı (sticker) alınabilir.

### 5. 📊 Raporlama & Dışa Aktarma
- 7, 30 ve 90 günlük dönemsel stok hareketleri ve en hareketli ürün analizleri.
- DataTables entegrasyonu ile tüm tabloları tek tıkla **Excel, PDF, CSV, Kopyala ve Yazdır** seçenekleriyle dışa aktarma.
- Performans için 300 saniyelik akıllı rapor önbellekleme (hareket kaydedildiğinde otomatik yenilenir).

---

## 🛠️ Teknoloji Yığını

- **Backend:** PHP 8.2+, Laravel 12, Laravel Breeze
- **Veritabanı:** SQLite (varsayılan sıfır yapılandırma) veya MySQL
- **Frontend:** Blade Templating, Tailwind CSS, Bootstrap 5.3 (LTR + RTL), Alpine.js, Vite
- **Kütüphaneler:** DataTables 1.13 + Buttons Extension, Toastr.js, JsBarcode
- **E-posta Servisi:** Gmail SMTP / TLS entegrasyonu
- **Test:** PHPUnit / Pest uyumlu feature test paketi (38 test, 153 assertion)

---

## 🚀 Hızlı Kurulum & Çalıştırma

Projeyi yerel ortamınızda ayağa kaldırmak için aşağıdaki adımları izleyin:

### 1. Projeyi Klonlayın & Bağımlılıkları Kurun
```bash
git clone https://github.com/AhmedER23/Inventory-Mini.git
cd inventory-mini

composer install
npm install
npm run build
```

### 2. Ortam Dosyasını Hazırlayın
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Veritabanını Hazırlayın & Başlatın
Varsayılan olarak sıfır ek kurulum gerektiren SQLite ayarlıdır:
```bash
# Windows (PowerShell):
New-Item database/database.sqlite -ItemType File -Force

# macOS / Linux:
touch database/database.sqlite

# Migrasyonları ve örnek verileri yükleyin:
php artisan migrate:fresh --seed
```

### 4. Sunucuyu Başlatın
```bash
php artisan serve
```
Tarayıcınızdan `http://127.0.0.1:8000` adresine gidin.

---

## 🔑 Varsayılan Yönetici Giriş Bilgileri

Veritabanı seed edildiğinde sistemde hazır gelen yönetici hesabı:
- **E-posta:** `admin@example.com`
- **Şifre:** `password`
- **Rol:** `Sistem Yöneticisi (Admin)`

> *.env* dosyasındaki `ADMIN_EMAIL` ve `ADMIN_PASSWORD` değişkenlerini değiştirerek kendi yönetici bilgilerinizi özelleştirebilirsiniz.

---

## 🧪 Otomatik Testler

Uygulamanın tüm güvenlik, yetki, envanter, çok dilli arayüz ve e-posta akışları test edilmiştir:

```bash
php artisan test
```
```text
   PASS  Tests\Unit\ExampleTest
   PASS  Tests\Feature\Auth\AuthenticationTest
   PASS  Tests\Feature\Auth\PasswordUpdateTest
   PASS  Tests\Feature\Auth\RegistrationTest
   PASS  Tests\Feature\ExampleTest
   PASS  Tests\Feature\InventoryTest
   PASS  Tests\Feature\ProfileTest
   PASS  Tests\Feature\SystemHealthTest

  Tests:    38 passed (153 assertions)
  Duration: 3.2s
```

---

## 📁 Proje Dizin Yapısı

```text
inventory-mini/
├── app/
│   ├── Http/Controllers/    # Dashboard, Product, Transaction, Report, Settings, Auth
│   ├── Mail/                # 6 haneli PIN doğrulama e-posta şablonu (VerificationCodeMail)
│   ├── Models/              # User, Product, StockTransaction, Setting
│   └── Http/Middleware/     # RoleMiddleware, SetLocale
├── database/
│   ├── migrations/          # Rol, ürün birimleri, ayarlar, PIN doğrulama tabloları
│   └── seeders/             # UserSeeder (Admin), ProductSeeder
├── lang/
│   ├── tr/ui.php            # Türkçe dil paketi
│   ├── en/ui.php            # İngilizce dil paketi
│   └── ar/ui.php            # Arapça (RTL) dil paketi
├── public/
│   ├── favicon.svg          # Vektörel IM marka logosu
│   └── favicon.ico          # Standart favicon
├── resources/
│   ├── views/               # Blade arayüzleri (Dashboard, Products, Transactions, Settings...)
│   └── css/app.css          # Özel kurumsal tema, RTL ve DataTables stilleri
└── routes/
│   ├── web.php              # /{locale} ön ekli uygulama rotaları
│   └── auth.php             # Breeze kimlik doğrulama rotaları
```

---

## 📄 Lisans

Bu proje [MIT Lisansı](LICENSE) altında açık kaynak olarak sunulmaktadır.
Dilediğiniz gibi geliştirebilir, ticari veya kişisel projelerinizde kullanabilirsiniz.
