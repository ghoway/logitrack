# LogiTrack — Panduan Instalasi & Penggunaan

**LogiTrack** adalah aplikasi manajemen pengiriman logistik berbasis web yang dibangun dengan Laravel 13, Filament 5, Livewire 4, dan Tailwind CSS 4. Aplikasi ini melayani tiga peran pengguna: **Admin (Super Admin)**, **Kurir (Courier)**, dan **Pengirim (User)**.

---

## Daftar Isi

1. [Alur Aplikasi](#alur-aplikasi)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Instalasi](#instalasi)
4. [Deployment](#deployment)
5. [Update Aplikasi](#update-aplikasi)
6. [Cara Penggunaan](#cara-penggunaan)
   - [6.1 Registrasi & Login](#61-registrasi--login)
   - [6.2 Pengguna Biasa (User)](#62-pengguna-biasa-user)
   - [6.3 Admin (Super Admin)](#63-admin-super-admin)
   - [6.4 Kurir (Courier)](#64-kurir-courier)
   - [6.5 Tracking Publik](#65-tracking-publik)
7. [Struktur Menu](#struktur-menu)
8. [Akun Bawaan](#akun-bawaan)

---

## Alur Aplikasi

Berikut adalah alur sederhana dari sistem LogiTrack dari awal hingga akhir:

```
                   ╔══════════════════════════════════╗
                   ║   PENGUNJUNG (Guest)              ║
                   ║   ● Lihat landing page            ║
                   ║   ● Tracking resi publik          ║
                   ╚══════════════════════════════════╝
                              │
                     ┌────────┴─────────┐
                     ▼                  ▼
           ┌──────────────────┐  ┌──────────────────┐
           │  Register        │  │  Login            │
           │  /admin/register │  │  /admin/login     │
           └────────┬─────────┘  └────────┬─────────┘
                    │                     │
                    └──────────┬──────────┘
                               ▼
               ╔══════════════════════════════════╗
               ║         USER (Pengirim)           ║
               ║  ● Membuat permintaan pengiriman  ║
               ║  ● Upload bukti pembayaran        ║
               ║  ● Lihat status pengiriman        ║
               ╚══════════════════════════════════╝
                               │
                               ▼
               ┌───────────────────────────────┐
               │  Sistem Generate:             │
               │  ● Nomor tracking (ID-...)    │
               │  ● Record pembayaran (unpaid)  │
               │  ● Status → "pending"         │
               └───────────────┬───────────────┘
                               │
                               ▼
               ╔══════════════════════════════════╗
               ║     ADMIN (Super Admin)           ║
               ║  ● Verifikasi pembayaran         ║
               ║  ● Assign kurir ke pengiriman    ║
               ║  ● Status → "picked_up"          ║
               ╚══════════════════════════════════╝
                               │
                               ▼
               ╔══════════════════════════════════╗
               ║     KURIR (Courier)               ║
               ║  ● Lihat tugas yang ditugaskan    ║
               ║  ● Update status pengiriman:      ║
               ║    picked_up → in_transit →       ║
               ║    delivered                      ║
               ║  ● Upload bukti foto pengiriman   ║
               ╚══════════════════════════════════╝
                               │
                               ▼
               ┌───────────────────────────────┐
               │  Selesai: Status "delivered"  │
               │  Pengirim & publik bisa       │
               │  melacak via nomor tracking   │
               └───────────────────────────────┘
```

### Status Lifecycle Pengiriman

```
pending ──► picked_up ──► in_transit ──► delivered
   │            │              │              │
   │            │              │              │
   ▼            ▼              ▼              ▼
Menunggu    Pembayaran    Paket dalam   Paket sampai,
verifikasi  diverifikasi, perjalanan    bukti foto
pembayaran  paket dijemput             terupload
```

---

## Persyaratan Sistem

Sebelum memulai instalasi, pastikan sistem Anda memenuhi persyaratan berikut:

| Komponen | Versi Minimal |
|----------|---------------|
| PHP | 8.4+ |
| Composer | 2.x |
| Node.js | 20+ |
| NPM | 10+ |
| Database | SQLite (default) atau MySQL 8+ |
| Ekstensi PHP | GD atau Imagick (untuk upload gambar) |
| Web Server | Nginx / Apache / Laravel Herd / Laravel Sail |

---

## Instalasi

### Langkah 1 — Clone & Install Dependensi

```bash
git clone https://github.com/ghoway/logitrack.git logitrack
cd logitrack
composer install
npm install
```

### Langkah 2 — Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan file `.env` jika ingin menggunakan MySQL:

```
DB_CONNECTION=sqlite
# Untuk MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=logitrack
# DB_USERNAME=root
# DB_PASSWORD=
```

### Langkah 3 — Migrasi & Seed Database

```bash
php artisan migrate --seed
```

Perintah ini akan membuat seluruh tabel dan mengisi data awal:
- **3 Role**: `super_admin`, `courier`, `user`
- **5 User**: Wahyu, Humam, Rafi, Usamah, Ellen
- **2 Routes**: Jakarta ↔ Surabaya, Bali ↔ Jakarta
- **4 Rates**: Darat & Pesawat untuk tiap rute
- **2 Banks**: BCA & Mandiri

### Langkah 4 — Build Frontend

```bash
npm run build
```

Untuk pengembangan dengan hot-reload:

```bash
npm run dev
```

### Langkah 5 — Storage Link

```bash
php artisan storage:link
```

Membuat symlink `public/storage` untuk akses file upload (bukti pembayaran & bukti pengiriman).

### Langkah 6 — Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000` dan panel admin di `http://localhost:8000/admin`.

---

## Deployment

### Opsi 1 — Laravel Cloud (Rekomendasi)

[Laravel Cloud](https://cloud.laravel.com/) adalah platform deployment resmi Laravel yang paling cepat dan mudah:

```bash
composer global require laravel/installer
laravel new logitrack --cloud
# Atau deploy project existing:
laravel cloud deploy
```

### Opsi 2 — VPS / Dedicated Server (Laravel Forge)

Gunakan [Laravel Forge](https://forge.laravel.com/) untuk mengelola server VPS (DigitalOcean, Linode, AWS, dll):

1. Buat server di Forge
2. Install PHP 8.4, Nginx, MySQL/PostgreSQL
3. Point domain ke server
4. Deploy via Git di Forge
5. Setel cron job (schedule) dan queue worker

### Opsi 3 — Shared Hosting (cPanel)

1. Upload semua file ke hosting (kecuali folder `.git`)
2. Setel `public/` sebagai document root
3. Sesuaikan `.env` dengan database hosting
4. Jalankan di terminal hosting:
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

### Konfigurasi Penting untuk Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
```

Jalankan optimasi:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

Buat cron job untuk scheduler (Laravel Forge / VPS):

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Update Aplikasi

### Update Source Code

Jika menggunakan Git:

```bash
git pull origin main
# atau
git pull origin dev
```

### Update Dependensi

```bash
composer install --no-interaction
npm install && npm run build
```

### Jalankan Migrasi Baru

```bash
php artisan migrate --force
```

### Ulangi Optimasi

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

> **Catatan**: Jika ada perubahan pada seeder, jalankan `php artisan db:seed --class=DatabaseSeeder --force` untuk menambahkan data baru tanpa menghapus data existing.

---

## Cara Penggunaan

### 6.1 Registrasi & Login

**Login**: Buka `/admin/login` dan masukkan email serta password.

**Registrasi**: Buka `/admin/register` untuk membuat akun baru. Setelah registrasi, akun otomatis mendapat role **user** dan langsung login ke dashboard.

Dashboard utama dapat diakses di `/admin`.

### 6.2 Pengguna Biasa (User)

Setelah login sebagai user, Anda dapat:

#### Membuat Pengiriman Baru

1. Buka menu **Shipments** → klik tombol **Buat Pengiriman**
2. Ikuti wizard 3 langkah:
   - **Langkah 1**: Pilih rute/tarif → isi data penerima → isi dimensi & berat paket (biaya terhitung otomatis)
   - **Langkah 2**: Lihat instruksi pembayaran (nomor rekening bank/QRIS) → upload bukti transfer
   - **Langkah 3**: Review data → Submit
3. Sistem akan menghasilkan nomor tracking unik (contoh: `ID-20260630AB12`)
4. Status awal: **Pending** (menunggu verifikasi admin)

#### Melihat Pengiriman Saya

- Buka menu **Shipments** untuk melihat daftar pengiriman milik Anda
- Status ditampilkan dalam badge warna:
  - **Pending** — Menunggu verifikasi pembayaran
  - **Picked Up** — Paket sudah dijemput kurir
  - **In Transit** — Paket dalam perjalanan
  - **Delivered** — Paket sudah sampai

#### Pembayaran

- Saat membuat pengiriman, Anda langsung upload bukti pembayaran
- Admin akan memverifikasi bukti tersebut
- Status pembayaran: **Unpaid** → **Proof Uploaded** → **Paid**

### 6.3 Admin (Super Admin)

Login dengan akun super admin.

#### Verifikasi Pembayaran

1. Buka menu **Shipments**
2. Cari baris dengan status "Proof Uploaded"
3. Klik tombol **Approve Payment** (icon centang hijau)
4. Status otomatis berubah menjadi **Picked Up**

#### Menetapkan Kurir

1. Buka detail/edit pengiriman
2. Pada field **Courier**, pilih kurir yang tersedia
3. Simpan perubahan

#### Update Status Pengiriman

1. Klik tombol **Update Status** pada baris pengiriman
2. Pilih status baru
3. Jika memilih **Delivered**, upload foto bukti pengiriman

#### Widget Dashboard

Admin melihat dashboard dengan widget:
- **AdminStatsOverview** — Total shipments, pendapatan, pengiriman aktif
- **ShipmentStatusChart** — Grafik distribusi status
- **RevenueChart** — Grafik pendapatan 30 hari terakhir
- **RecentShipmentsTable** — 10 pengiriman terbaru

#### Kelola Data Master

| Menu | Fungsi |
|------|--------|
| **Routes** | Kelola rute asal-tujuan pengiriman |
| **Rates** | Atur tarif per kg untuk tiap rute & tipe |
| **Banks** | Kelola rekening bank untuk pembayaran |
| **Users** | Kelola pengguna & role |

### 6.4 Kurir (Courier)

Login dengan akun kurir.

#### Melihat Tugas

- Buka menu **Shipments** — hanya menampilkan pengiriman yang ditugaskan kepada Anda

#### Update Status Pengiriman

1. Klik tombol **Update Status** pada pengiriman yang ditangani
2. Pilih status baru sesuai progres:
   - **Picked Up** — Paket sudah dijemput dari pengirim
   - **In Transit** — Paket dalam perjalanan
   - **Delivered** — Paket sudah sampai ke penerima
3. Jika memilih **Delivered**, upload foto sebagai bukti pengiriman (wajib)

### 6.5 Tracking Publik

Siapa pun dapat melacak status pengiriman tanpa login:

1. Buka halaman `/tracking`
2. Masukkan nomor tracking (contoh: `ID-20260630AB12`)
3. Klik tombol **Lacak**
4. Sistem menampilkan detail:
   - Status pengiriman (badge warna)
   - Nama pengirim & penerima
   - Rute & tipe layanan
   - Berat tagihan & estimasi
   - Total biaya

---

## Struktur Menu

### Panel Admin (`/admin`)

| Menu | Akses |
|------|-------|
| **Dashboard** | Semua role |
| **Shipments** | User (milik sendiri), Kurir (ditugaskan), Admin (semua) |
| **Payments** | User (milik sendiri), Admin (semua) |
| **Banks** | Admin saja |
| **Routes** | Admin saja |
| **Rates** | Admin saja |
| **Users** | Admin saja |

### Halaman Publik

| Halaman | Route | Fungsi |
|---------|-------|--------|
| Landing Page | `/` | Halaman depan dengan info & statistik |
| Tracking | `/tracking` | Lacak status pengiriman |
| Login | `/admin/login` | Masuk ke panel |
| Register | `/admin/register` | Daftar akun baru |

---

## Akun Bawaan

Setelah menjalankan `php artisan migrate --seed`, akun berikut tersedia:

| Role | Nama | Email | Password |
|------|------|-------|----------|
| **Super Admin** | Wahyu | wahyu@mail.com | password |
| **Kurir** | Humam | humam@mail.com | password |
| **Kurir** | Rafi | rafi@mail.com | password |
| **Pengguna** | Usamah | usamah@mail.com | password |
| **Pengguna** | Ellen | ellen@mail.com | password |

Semua password: `password`

---

© 2026 LogiTrack. Dokumentasi teknis dan panduan penggunaan aplikasi.
