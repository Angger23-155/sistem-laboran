# 🧪 Sistem Informasi Manajemen Laboran (TIF LAB)

Aplikasi manajemen tugas laboran dan pelaporan berbasis web yang terintegrasi dengan **WhatsApp Gateway (WAHA - WhatsApp HTTP API)** untuk mengirimkan notifikasi penugasan, deadline, approval, dan laporan secara *real-time*.

---

## 📋 Prasyarat Sistem (Prerequisites)

Sebelum menjalankan aplikasi, pastikan perangkat atau server telah terpasang:
- **PHP** >= 8.2 (dengan ekstensi: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`)
- **Composer** (v2.x)
- **Node.js** (v18.x / v20.x) & **NPM**
- **MySQL / MariaDB** (melalui Laragon / XAMPP / MySQL Server bawaan)
- **Docker Desktop** (untuk menjalankan gateway WhatsApp WAHA)
- **Git**

---

## 🚀 Langkah Instalasi & Konfigurasi

Ikuti langkah-langkah di bawah ini secara berurutan:

### 1. Clone Repository
```bash
git clone https://github.com/USERNAME/sistem-laboran.git
cd sistem-laboran
```

### 2. Install Dependensi PHP & JavaScript
```bash
# Install paket backend Laravel
composer install

# Install dependensi frontend
npm install
```

### 3. Konfigurasi File Environment (`.env`)
Salin file template `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*(Di Windows Command Prompt bisa gunakan: `copy .env.example .env`)*

Buka file `.env` dan sesuaikan konfigurasi database Anda:
```env
APP_NAME="Sistem Laboran"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_SYSTEM_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_laboran
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key Laravel:
```bash
php artisan key:generate
```

### 4. Setup Database
1. Buat database baru di MySQL dengan nama `sistem_laboran`.
2. Import file SQL yang sudah disediakan di root project (`db_sistem_laboran.sql`) ke database `sistem_laboran` melalui phpMyAdmin / HeidiSQL / terminal:
   ```bash
   mysql -u root -p sistem_laboran < db_sistem_laboran.sql
   ```
*(Atau jika ingin menggunakan migrasi bawaan: `php artisan migrate`)*

### 5. Setup Storage Link
Buat symlink folder storage agar bukti laporan/foto dapat diakses publik:
```bash
php artisan storage:link
```

---

## 📱 Konfigurasi Gateway WhatsApp (WAHA)

Aplikasi ini menggunakan Docker untuk menjalankan **WAHA (WhatsApp HTTP API)** sebagai pengirim pesan WhatsApp otomatis.

### 1. Jalankan Container WAHA
Di direktori project, jalankan:
```bash
docker compose up -d
```
Container akan berjalan pada port `3000`.

### 2. Buka Dashboard WAHA
Akses dashboard WAHA melalui browser:
- **URL Dashboard:** `http://localhost:3000/dashboard/`
- **Username:** `admin`
- **Password:** `admin`

### 3. Hubungkan WhatsApp (Scan QR)
1. Di halaman dashboard WAHA, pastikan ada worker/session dengan nama `default` (sesuai setting `WAHA_SESSION=default` di `.env`).
2. Klik tombol **Start** pada session `default`.
3. Klik ikon QR Code dan **scan menggunakan WhatsApp di HP** Anda (melalui menu *Perangkat Tertaut* / *Linked Devices*).
4. Pastikan status session berubah menjadi **WORKING: 1** (Warna Hijau).

### 4. Ambil API Key & Masukkan ke `.env`
1. Di dashboard WAHA, lihat menu/ikon **API Key** pada worker.
2. Salin API Key tersebut, lalu masukkan ke dalam file `.env`:
   ```env
   WAHA_URL=http://localhost:3000
   WAHA_API_KEY=masukkan_api_key_waha_disini
   WAHA_SESSION=default
   ```
3. Jika sebelumnya config pernah di-cache, jalankan:
   ```bash
   php artisan config:clear
   ```

---

## 💻 Menjalankan Aplikasi

Buka 2 jendela terminal:

**Terminal 1 — Build Frontend Assets:**
```bash
npm run build
# Atau untuk mode development:
# npm run dev
```

**Terminal 2 — Jalankan Server Laravel:**
```bash
php artisan serve
```

Aplikasi sekarang dapat diakses di browser pada:  
👉 **`http://127.0.0.1:8000`**

---

## ⏰ Cron Job / Pengecekan Deadline Otomatis

Aplikasi memiliki perintah CLI untuk memeriksa tugas yang mendekati deadline (5 jam sebelum) dan tugas yang terlambat:

```bash
php artisan tugas:cek-deadline
```

Untuk otomasi di server produksi (Linux), tambahkan cron job berikut (`crontab -e`):
```bash
* * * * * cd /path-ke-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## ⚠️ Catatan Penting & Troubleshooting

1. **Format Nomor WhatsApp Pengguna:**  
   Pastikan nomor WhatsApp pengguna diisi dengan awalan nomor standar Indonesia (`08xxxxxxxx` atau `+628xxxxxxxx`). Sistem secara otomatis akan memformatnya menjadi format internasional `628xxxx@c.us`.
2. **Jangan Jalankan `php artisan config:cache` di Mode Development:**  
   Jika mengubah nilai di `.env`, Anda tidak perlu me-restart server secara manual selama tidak menjalankan `config:cache`. Jika ada konfigurasi baru yang tidak terbaca, cukup jalankan `php artisan config:clear`.
3. **Session WhatsApp Terputus / Status Error:**  
   Jika WhatsApp di HP keluar atau error `getChat`:
   - Buka `http://localhost:3000/dashboard/`
   - Klik tombol **Stop** pada session `default`, lalu klik **Start** kembali dan scan ulang QR code.
   - Atau perbarui image WAHA: `docker compose pull && docker compose up -d`.

---

## 📄 Lisensi
Sistem Laboran dikembangkan untuk kebutuhan operasional laboratorium teknik informatika.
