<div align="center">

# 🅿️ GANPARKING — Sistem Manajemen Parkir Berbasis QR Code

**Platform manajemen parkir terintegrasi: panel admin web untuk operasional & rekapitulasi, serta REST API untuk aplikasi mobile pengguna dan petugas lapangan.**

[![PHP](https://img.shields.io/badge/PHP-%5E8.2-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat-square&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev/)
[![Vite](https://img.shields.io/badge/Vite-6.x-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Midtrans](https://img.shields.io/badge/Payment-Midtrans-1E90FF?style=flat-square)](https://midtrans.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=flat-square)](LICENSE)

</div>

---

## 📑 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Matriks Hak Akses (Role)](#-matriks-hak-akses-role)
- [Tech Stack](#-tech-stack)
- [Prasyarat](#-prasyarat)
- [Panduan Instalasi](#-panduan-instalasi)
- [Konfigurasi Environment](#-konfigurasi-environment)
- [Struktur Direktori](#-struktur-direktori)
- [Dokumentasi API](#-dokumentasi-api)
- [Alur Autentikasi](#-alur-autentikasi)
- [Screenshot Aplikasi](#-screenshot-aplikasi)
- [Troubleshooting](#-troubleshooting)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Proyek

**GANPARKING** adalah aplikasi manajemen parkir berbasis **QR Code** yang dirancang untuk mendigitalkan seluruh siklus operasional area parkir — dari pemindaian masuk/keluar kendaraan, perhitungan tarif otomatis, penagihan denda, hingga rekapitulasi pendapatan dan ekspor laporan.

Aplikasi ini dibangun dengan pendekatan **dua muka (dual-interface)**:

| Antarmuka | Pengguna | Kegunaan |
| :--- | :--- | :--- |
| **Panel Admin (Web)** | `admin_super`, `admin` | Monitoring dashboard, rekapitulasi parkir masuk/keluar, laporan income, manajemen denda, master data, manajemen pengguna, dan ekspor Excel. |
| **REST API (Mobile)** | `user`, `staff` | Registrasi & login, pemindaian QR parkir, pengelolaan kendaraan, top-up saldo (Midtrans), pembayaran denda, dan operasi petugas lapangan. |

**Tujuan utama:**

- Menggantikan proses parkir manual (tiket fisik) dengan verifikasi **QR Code** yang aman (HMAC-SHA256 signature).
- Memberikan **visibilitas real-time** terhadap okupansi, pendapatan, dan tunggakan denda.
- Menyediakan **pembayaran cashless** terintegrasi Midtrans (Snap, QRIS, GoPay).
- Memisahkan hak akses secara tegas antara super admin, admin operasional, petugas lapangan, dan pengguna akhir.

---

## ✨ Fitur Utama

### 👑 Admin Super (Panel Web)

Memiliki **seluruh** kapabilitas role `admin`, **ditambah** pengelolaan akun pengguna sistem:

- **Data Admin** — CRUD akun administrator, verifikasi password super admin sebelum aksi sensitif (edit/hapus), proteksi anti self-delete.
- **Data User** — CRUD akun pengguna aplikasi e-Parkir.
- **Data Staff** — CRUD akun petugas lapangan (`role: staff`).
- Manajemen pengguna disembunyikan dari sidebar dan **diblokir di level route** untuk role `admin`.

### 🛠️ Admin (Panel Web)

Semua fitur operasional, **kecuali** Kelola Data Pengguna:

- **Dashboard** — kartu statistik: total parkir, parkir hari ini, jumlah jenis kendaraan, denda pending, total denda, total income, dan income hari ini.
- **Rekapitulasi Parkir** — data parkir masuk, parkir keluar, dan data gabungan (dengan pencarian plat nomor, sorting, dan pagination).
- **Income** — ringkasan transaksi berbayar (`fee_admin`) beserta total pendapatan.
- **Denda** — daftar denda berstatus *pending*, pencarian multi-field, dan aksi "Selesaikan Denda".
- **Ekspor Excel** — unduh laporan parkir berdasarkan rentang tanggal (`maatwebsite/excel`).
- **Master Data:**
  - **Data Aplikasi** — ubah nama aplikasi, kode parkir, identitas perusahaan, dan logo (termasuk fitur reset ke pengaturan default).
  - **Jenis Kendaraan** — CRUD tipe kendaraan beserta tarif parkir dan keterangan tarif.
  - **Paket Topup** — CRUD paket saldo top-up (resource route).
  - **Latar Aplikasi** — pengaturan tampilan latar.
- **Logout** — dengan dialog konfirmasi SweetAlert2.

### 🧑‍✈️ Staff (REST API)

- **Cek kendaraan via plat nomor** — memverifikasi apakah kendaraan sudah melakukan scan parkir hari ini.
- **Catat denda otomatis** — membuat denda untuk kendaraan terdaftar yang parkir tanpa proses scan (nominal otomatis, status `pending`).
- **Catat denda non-user** — mencatat pelanggaran pelat yang tidak terdaftar di sistem.
- **Update data pengguna** — memperbarui profil/password pengguna dari sisi petugas.

### 👤 User / Pengguna (REST API)

- **Autentikasi** — registrasi & login berbasis **Laravel Sanctum** (personal access token).
- **Manajemen Kendaraan** — tambah, lihat, hapus, dan aktivasi kendaraan pengguna.
- **Parkir QR Code** — catat waktu masuk, validasi QR (HMAC signature), cek status parkir, dan proses keluar beserta perhitungan tarif.
- **Riwayat Parkir** — riwayat kendaraan dan seluruh riwayat parkir pengguna.
- **Top-Up Saldo** — paket top-up aktif, pembuatan transaksi **Midtrans** (Snap API / Core API QRIS & GoPay), pengecekan status, dan penghapusan transaksi pending (dijadwalkan otomatis via queue job).
- **Denda** — daftar denda pending milik pengguna dan pembayaran denda.
- **Saldo** — kalkulasi saldo dari transaksi top-up berstatus `success`.

### 🔔 Integrasi Webhook & Pembayaran

- **Midtrans Notification Handler** — memverifikasi `signature_key` (SHA-512), memperbarui status transaksi (settlement/capture/pending/failed), **menambah saldo** pengguna, dan **membayar denda pending secara otomatis** bila saldo mencukupi.
- **Pembaruan status parkir otomatis** — order parkir yang dibayar via Midtrans langsung ditandai `Sudah Dibayar` dan saldo dipotong otomatis.
- **Queue Job** — transaksi top-up `pending` dihapus otomatis setelah 5 menit (`DeletePendingTopup`).

---

## 🔐 Matriks Hak Akses (Role)

Sistem memiliki **empat role** yang tersebar di dua mekanisme autentikasi berbeda:

| Role | Mekanisme Auth | Panel Web | Kelola Pengguna | API Endpoints |
| :--- | :--- | :---: | :---: | :---: |
| `admin_super` | Session (`loginStatus`, `adminData`, `role`) | ✅ | ✅ | ❌ |
| `admin` | Session (`loginStatus`, `adminData`, `role`) | ✅ | ❌ (hidden & blocked) | ❌ |
| `staff` | Sanctum Token | ❌ | ❌ | ✅ (endpoint staff) |
| `user` | Sanctum Token | ❌ | ❌ | ✅ (endpoint pengguna) |

**Middleware yang digunakan:**

| Alias | Kelas | Fungsi |
| :--- | :--- | :--- |
| `check.role` | `MiddlewareWeb\CheckAdminRole` | Middleware variadik, menerima satu/banyak role, mis. `check.role:admin_super,admin`. |
| `staff` | `CheckStaff` | Membatasi endpoint API khusus petugas. |
| `admin` | `CheckAdmin` | Pemeriksaan admin pada API. |
| `auth:sanctum` | Laravel Sanctum | Proteksi endpoint API berbasis token. |

> **Catatan keamanan:** Setiap aksi sensitif (edit/hapus akun, denda) memverifikasi ulang **password akun yang sedang login** menggunakan `Hash::check` terhadap `session('adminData')`.

---

## 🧰 Tech Stack

### Backend

| Teknologi | Versi | Keterangan |
| :--- | :--- | :--- |
| **PHP** | `^8.2` | Bahasa utama. |
| **Laravel** | `^12.0` | Framework utama (bootstrap `bootstrap/app.php`). |
| **Laravel Sanctum** | `^4.0` | Autentikasi token REST API. |
| **Laravel Tinker** | `^2.10` | REPL interaktif. |
| **Maatwebsite Excel** | `3.1.64` | Ekspor laporan parkir ke `.xlsx`. |
| **Midtrans PHP** | `^2.6` | Payment gateway (Snap token). |
| **Dedoc Scramble** | `^0.12` | Auto-generate dokumentasi OpenAPI. |
| **MySQL** | — | Basis data relasional (default dev: `sistem_parkir`). |

### Tooling (Development)

`phpunit/phpunit ^11.5` · `laravel/pint ^1.13` · `laravel/pail ^1.2` · `laravel/sail ^1.41` · `nunomaduro/collision ^8.6` · `fakerphp/faker ^1.23` · `mockery/mockery ^1.6`

### Frontend

| Teknologi | Versi | Keterangan |
| :--- | :--- | :--- |
| **Vite** | `^6.2` | Build tool & dev server. |
| **Tailwind CSS** | `^4.0` (`@tailwindcss/vite`) | Utility-first CSS (build aset via Vite). |
| **laravel-vite-plugin** | `^1.2` | Integrasi Vite dengan Laravel. |
| **Axios** | `^1.8` | HTTP client. |
| **Alpine.js** | `3.x` (CDN) | Interaksi UI ringan (sidebar, dropdown, toggle). |
| **SweetAlert2** | `11` (CDN) | Notifikasi & dialog konfirmasi global. |
| **Font Awesome** | `5.15` (CDN) | Ikon. |
| **Font Inter / Roboto** | Google Fonts | Tipografi panel admin & halaman login. |

> Panel admin memuat Tailwind, Alpine.js, SweetAlert2, dan Font Awesome melalui **CDN** di layout `app.blade.php`, sementara landing page & aset aplikasi menggunakan **Vite** (`resources/css/app.css`, `resources/js/app.js`).

---

## 📋 Prasyarat

Pastikan perangkat Anda telah terpasang:

- **PHP >= 8.2** dengan ekstensi: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`.
- **Composer 2.x**
- **Node.js >= 18** & **npm**
- **MySQL 8.x** (atau MariaDB) — *atau* SQLite untuk keperluan cepat.
- **Git**

> Lingkungan yang direkomendasikan untuk Windows: **Laragon** (PHP + MySQL + Composer siap pakai).

---

## 🚀 Panduan Instalasi

### 1. Clone Repository

```bash
git clone <URL_REPOSITORY> qr-parking
cd qr-parking
```

### 2. Install Dependency PHP

```bash
composer install
```

### 3. Install Dependency JavaScript

```bash
npm install
```

### 4. Siapkan File Environment

```bash
cp .env.example .env
```

Lalu **generate application key**:

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Buat database baru, misalnya `sistem_parkir`:

```sql
CREATE DATABASE sistem_parkir CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan kredensial pada `.env` (lihat [Konfigurasi Environment](#-konfigurasi-environment)):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_parkir
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Migrasi Database

```bash
php artisan migrate
```

> ⚠️ **Penting — Skema Basis Data.**
> Repositori ini menyertakan migrasi untuk tabel framework (`users`, `cache`, `jobs`, `personal_access_tokens`) dan penambahan kolom `fee_admin` pada tabel `dataparkir`.
> Tabel domain aplikasi — `admin`, `kendaraan`, `dataparkir`, `denda`, `topups`, `paket_topups`, `propertyaplikasi`, `tipe_kendaraan`, dan `nyubeuser` — **dibuat dari database pengembangan yang sudah ada**. Jika Anda memulai dari nol, siapkan tabel-tabel tersebut terlebih dahulu (mis. melalui import SQL dump) sebelum menjalankan aplikasi.
>
> Seeder bawaan bersifat **legacy** dan menargetkan skema lama, sehingga `php artisan db:seed` **tidak wajib** dijalankan. Akun administrator dibuat langsung pada tabel `admin`.

Tambahkan **minimal satu akun administrator** pada tabel `admin` dengan role `admin_super` agar dapat mengakses seluruh menu:

```bash
php artisan tinker
```

```php
App\Models\Admin::create([
    'nama_lengkap'  => 'Super Admin',
    'username'      => 'superadmin@example.com',
    'password'      => Illuminate\Support\Facades\Hash::make('password-kuat'),
    'jenis_kelamin' => 'Laki-laki',
    'no_hp'         => '08123456789',
    'alamat'        => 'Kantor Pusat',
    'role'          => 'admin_super',
]);
```

### 7. Build Aset Frontend

```bash
npm run build
```

> Untuk mode pengembangan dengan *hot reload*, gunakan `npm run dev`.

### 8. Tautkan Storage (Opsional)

Diperlukan bila menggunakan fitur unggah foto profil/logo melalui disk `public`:

```bash
php artisan storage:link
```

### 9. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi dapat diakses di **http://127.0.0.1:8000**

> **Shortcut:** `composer dev` akan menjalankan server, queue listener, log viewer (Pail), dan Vite secara paralel dalam satu perintah.

### 10. Worker Queue (Penting untuk Top-Up)

Fitur top-up menggunakan queue job untuk pembersihan transaksi pending. Jalankan worker pada terminal terpisah:

```bash
php artisan queue:work
```

---

## ⚙️ Konfigurasi Environment

Berikut variabel `.env` yang relevan untuk proyek ini:

| Variabel | Contoh Nilai | Keterangan |
| :--- | :--- | :--- |
| `APP_NAME` | `GANPARKING` | Nama aplikasi. |
| `APP_ENV` | `local` | Lingkungan aplikasi. |
| `APP_URL` | `http://localhost` | URL dasar aplikasi. |
| `DB_CONNECTION` | `mysql` | Driver database (`mysql` / `sqlite`). |
| `DB_DATABASE` | `sistem_parkir` | Nama database. |
| `SESSION_DRIVER` | `database` | Driver sesi (panel admin berbasis session). |
| `QUEUE_CONNECTION` | `database` | Driver queue untuk job `DeletePendingTopup`. |
| `CACHE_STORE` | `database` | Driver cache. |
| `MIDTRANS_MERCHANT_ID` | `G0xxxxxxx` | Merchant ID Midtrans. |
| `MIDTRANS_SERVER_KEY` | `SB-Mid-server-...` | **Server Key** (bersifat rahasia; jangan expose ke frontend). |
| `MIDTRANS_CLIENT_KEY` | `SB-Mid-client-...` | Client Key Midtrans. |
| `MIDTRANS_IS_PRODUCTION` | `false` | `false` = sandbox, `true` = produksi. |
| `QR_SECRET_STATIC` | `...` | Secret key statis untuk penandatanganan QR Code (HMAC-SHA256). |

> 🔒 **Jangan pernah meng-commit file `.env`.** Pastikan `.env` tetap berada di dalam `.gitignore` dan gunakan kredensial sandbox untuk pengembangan.

---

## 📁 Struktur Direktori

```text
qr-parking/
├── app/
│   ├── Exports/                    # Kelas ekspor Excel (ParkirExport)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ControllersWeb/     # Controller panel admin (Dashboard, Admin, Auth, DataParkir, dll.)
│   │   │   ├── api/                # Controller khusus API (DendaControllerApi)
│   │   │   └── *.php               # Controller REST API (Auth, Topup, Kendaraan, scan, staff, dll.)
│   │   ├── Middleware/
│   │   │   ├── MiddlewareWeb/      # CheckAdminRole (variadik), loginCheck
│   │   │   ├── CheckAdmin.php
│   │   │   └── CheckStaff.php
│   │   ├── Requests/               # Form Request validation
│   │   └── Resources/              # API Resource (transformer respons JSON)
│   ├── Jobs/                       # Queue job (DeletePendingTopup)
│   ├── Models/                     # Eloquent model (Admin, User, DataParkir, Denda, Kendaraan, Topup, ...)
│   ├── Rules/                      # Custom rule (loginCheck untuk autentikasi admin)
│   └── Traits/                     # ApiResponse (format respons JSON konsisten)
├── bootstrap/app.php               # Konfigurasi routing & alias middleware
├── config/                         # Konfigurasi Laravel (services.php memuat kredensial Midtrans)
├── database/
│   ├── factories/                  # Factory model
│   ├── migrations/                 # Migrasi skema
│   └── seeders/                    # Seeder (legacy)
├── public/
│   ├── img/                        # Aset gambar & logo aplikasi
│   └── index.php
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/
│       ├── Admin/                  # Seluruh view panel admin
│       ├── vendor/pagination/      # Kustomisasi pagination (custom.blade.php)
│       ├── app.blade.php           # Layout utama panel admin (sidebar, topbar, modal stack)
│       ├── home.blade.php          # Halaman login
│       └── welcome.blade.php       # Landing page
├── routes/
│   ├── web.php                     # Rute panel admin (dengan grup middleware role)
│   └── api.php                     # Rute REST API (Sanctum)
├── storage/                        # Cache, log, sesi, file tersimpan
├── composer.json                   # Dependency PHP
├── package.json                    # Dependency JS
└── vite.config.js                  # Konfigurasi Vite + Tailwind
```

**Catatan penting:**

- **Layout `app.blade.php`** menggunakan `@stack('modals')` untuk merender modal pada level `<body>`, sehingga *overlay* menutupi 100% layar tanpa terpotong oleh konteks `stacking` sidebar.
- **Pagination** dikustomisasi melalui `resources/views/vendor/pagination/custom.blade.php` agar selaras dengan design system (slate/red, `rounded-lg`).
- **Design system**: kartu `rounded-2xl`, `shadow-sm`, `border-slate-200`, aksen merah `red-600`, tipografi Inter.

---

## 🔌 Dokumentasi API

Seluruh endpoint REST API terdaftar di `routes/api.php`. Berikut ringkasan endpoint utama:

### Publik

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/register` | Registrasi pengguna baru. |
| `POST` | `/api/login` | Login & menerbitkan token Sanctum. |
| `POST` | `/api/midtrans-notification` | Webhook notifikasi pembayaran Midtrans. |
| `GET` | `/api/dendaIndex` | Daftar denda (API). |

### Terproteksi (`auth:sanctum`)

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/logout` | Menghapus token aktif. |
| `GET` | `/api/user` | Data pengguna yang login. |
| `PUT` | `/api/update-user/{id_users}` | Perbarui profil pengguna. |
| `POST` | `/api/topup-store` | Membuat transaksi top-up (Midtrans). |
| `GET` | `/api/topup-show` | Daftar paket top-up aktif. |
| `GET` | `/api/topup-show-saldo/{user_id}` | Saldo & riwayat top-up sukses. |
| `POST` | `/api/kendaraan` | Tambah kendaraan. |
| `GET` | `/api/kendaraan-index` | Daftar kendaraan pengguna. |
| `PUT` | `/api/kendaraan-aktifkan/{id}` | Aktifkan kendaraan. |
| `POST` | `/api/validasi-qr` | Validasi QR Code (HMAC signature). |
| `POST` | `/api/catat-waktu-masuk` | Catat waktu masuk parkir. |
| `PUT` | `/api/parkir/{kode_parkir}` | Proses keluar & perhitungan tarif. |
| `GET` | `/api/tampil-riwayat-kendaraan-all/{user_id}` | Riwayat parkir lengkap. |
| `GET` | `/api/denda/user/{id_users}` | Denda pending pengguna. |
| `PUT` | `/api/denda/bayar` | Bayar denda. |

### Khusus Staff (`auth:sanctum` + `staff`)

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/kendaraan/plat/{plat_nomor}` | Cari kendaraan berdasarkan plat. |
| `POST` | `/api/kendaraan/store` | Catat denda kendaraan. |
| `POST` | `/api/kendaraan/dendaNonUser` | Catat denda untuk non-user. |
| `PUT` | `/api/update-staff/{id_users}` | Perbarui data pengguna oleh petugas. |

> 📖 **Dokumentasi OpenAPI otomatis** dihasilkan oleh **Dedoc Scramble** dan dapat diakses pada route default `/docs/api` setelah aplikasi berjalan.
>
> Semua respons JSON mengikuti format konsisten dari trait `App\Traits\ApiResponse` (struktur `successResponse` / `errorResponse`).

---

## 🔑 Alur Autentikasi

### Panel Admin (Session-based)

```text
Login (email + password)
   └─▶ Rule loginCheck
         ├─ Cari admin berdasarkan username (email)
         ├─ Verifikasi password dengan Hash::check
         └─ Simpan ke session: loginStatus=true, adminData, role
                └─▶ Redirect ke /dashboard
                      └─▶ Middleware check.role memvalidasi akses per route
```

### Aplikasi Mobile (Token-based / Sanctum)

```text
POST /api/login (username + password)
   └─▶ Hash::check → createToken('auth_token')
         └─▶ Bearer Token dikirim pada setiap request
               └─▶ Middleware auth:sanctum (dan staff bila perlu)
```

---

## 🖼️ Screenshot Aplikasi

> Letakkan berkas gambar pada folder `docs/screenshots/` lalu sesuaikan path di bawah ini.

<div align="center">

### 🔐 Halaman Login
![Halaman Login](docs/screenshots/login.png)

### 📊 Dashboard Admin
![Dashboard Admin](docs/screenshots/dashboard.png)

### 🅿️ Rekapitulasi Parkir
![Rekapitulasi Parkir](docs/screenshots/rekapitulasi-parkir.png)

### 💰 Laporan Income
![Laporan Income](docs/screenshots/income.png)

### 👥 Kelola Data Pengguna
![Kelola Data Pengguna](docs/screenshots/kelola-pengguna.png)

### 📱 Aplikasi Mobile (REST API Consumer)
![Aplikasi Mobile](docs/screenshots/mobile-app.png)

</div>

---

## 🩺 Troubleshooting

| Masalah | Penyebab | Solusi |
| :--- | :--- | :--- |
| `Vite manifest not found at: .../public/build/manifest.json` | Aset belum di-build atau hash tidak sinkron. | Jalankan `npm run build` ulang. |
| Halaman blank / 500 setelah ubah `.blade.php` | Cache view usang. | `php artisan view:clear && php artisan view:cache` |
| Perubahan `.env` tidak berefek | Konfigurasi ter-cache. | `php artisan config:clear` |
| Top-up tidak ter-update | Queue worker tidak berjalan. | Jalankan `php artisan queue:work`. |
| Ikon/gambar hilang setelah upload | Storage symlink belum dibuat. | `php artisan storage:link` |
| `419 Page Expired` pada form | Token CSRF hilang/kedaluwarsa. | Pastikan `@csrf` ada pada setiap form dan sesi aktif. |
| `SQLSTATE: Unknown database` | Database belum dibuat. | Buat database sesuai `DB_DATABASE` pada `.env`. |

---

## 🧪 Pengujian & Kualitas Kode

```bash
# Menjalankan test suite (PHPUnit)
php artisan test

# Format kode dengan Laravel Pint
./vendor/bin/pint

# Melihat log aplikasi secara real-time (Pail)
php artisan pail
```

---

## 📄 Lisensi

Proyek ini dirilis di bawah **MIT License**.

<div align="center">

---

Dibuat dengan ❤️ menggunakan **Laravel 12** & **Tailwind CSS**.

**GANPARKING** — *Parkir Modern, Tanpa Ribet.*

</div>
