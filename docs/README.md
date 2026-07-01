# LogiTrack — Panduan Instalasi & Penggunaan

**LogiTrack** adalah aplikasi manajemen pengiriman logistik berbasis web yang dibangun dengan Laravel 13, Filament 5, Livewire 4, dan Tailwind CSS 3. Aplikasi ini melayani tiga peran pengguna: **Admin (Super Admin)**, **Kurir (Courier)**, dan **Pengirim (User)**.

---

## Daftar Isi

1. [Alur Aplikasi](#alur-aplikasi)
2. [Persyaratan Sistem](#persyaratan-sistem)
3. [Instalasi](#instalasi)
4. [Cara Penggunaan](#cara-penggunaan)
   - [4.1 Registrasi & Login](#41-registrasi--login)
   - [4.2 Pengguna Biasa (User)](#42-pengguna-biasa-user)
   - [4.3 Admin (Super Admin)](#43-admin-super-admin)
   - [4.4 Kurir (Courier)](#44-kurir-courier)
   - [4.5 Tracking Publik](#45-tracking-publik)
5. [Struktur Menu](#struktur-menu)
6. [Akun Bawaan](#akun-bawaan)

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
                    ┌─────────┴──────────┐
                    ▼                    ▼
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

---

## Instalasi

### Langkah 1 — Clone & Install Dependensi

```bash
git clone <repository-url> logitrack
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
- **3 User**: admin@mail.com, kurir@mail.com, user@mail.com
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

## Cara Penggunaan

### 4.1 Registrasi & Login

**Login**: Buka `/admin/login` dan masukkan email serta password.

**Registrasi**: Buka `/admin/register` untuk membuat akun baru. Setelah registrasi, akun otomatis mendapat role **user** dan langsung login ke dashboard.

Dashboard utama dapat diakses di `/admin`.

### 4.2 Pengguna Biasa (User)

Setelah login sebagai user, Anda dapat:

#### Membuat Pengiriman Baru

1. Buka menu **Shipments** → klik tombol **Create Shipment**
2. Ikuti wizard 3 langkah:
   - **Langkah 1**: Pilih rute/tarif → isi data penerima → isi dimensi & berat paket (biaya terhitung otomatis)
   - **Langkah 2**: Lihat instruksi pembayaran (nomor rekening bank/QRIS) → upload bukti transfer
   - **Langkah 3**: Review data → Submit
3. Sistem akan menghasilkan nomor tracking unik (contoh: `ID-20260630AB12`)
4. Status awal: **Pending** (menunggu verifikasi admin)

#### Melihat Pengiriman Saya

- Buka menu **Shipments** untuk melihat daftar pengiriman milik Anda
- Status ditampilkan dalam badge warna:
  - ⚫ **Pending** — Menunggu verifikasi pembayaran
  - 🟡 **Picked Up** — Paket sudah dijemput kurir
  - 🔵 **In Transit** — Paket dalam perjalanan
  - 🟢 **Delivered** — Paket sudah sampai

#### Pembayaran

- Saat membuat pengiriman, Anda langsung upload bukti pembayaran
- Admin akan memverifikasi bukti tersebut
- Status pembayaran: **Unpaid** → **Proof Uploaded** → **Paid**

### 4.3 Admin (Super Admin)

Login dengan akun `admin@mail.com` (password: `password`).

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

#### Kelola Data Master

| Menu | Fungsi |
|------|--------|
| **Routes** | Kelola rute asal-tujuan pengiriman |
| **Rates** | Atur tarif per kg untuk tiap rute & tipe |
| **Banks** | Kelola rekening bank untuk pembayaran |
| **Users** | Kelola pengguna & role |

### 4.4 Kurir (Courier)

Login dengan akun `kurir@mail.com` (password: `password`).

#### Melihat Tugas

- Buka menu **Shipments** — hanya menampilkan pengiriman yang ditugaskan kepada Anda

#### Update Status Pengiriman

1. Klik tombol **Update Status** pada pengiriman yang ditangani
2. Pilih status baru sesuai progres:
   - **Picked Up** — Paket sudah dijemput dari pengirim
   - **In Transit** — Paket dalam perjalanan
   - **Delivered** — Paket sudah sampai ke penerima
3. Jika memilih **Delivered**, upload foto sebagai bukti pengiriman (wajib)

### 4.5 Tracking Publik

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

| Menu | Ikon | Akses |
|------|------|-------|
| **Dashboard** | — | Semua role |
| **Shipments** | 🚚 | User (milik sendiri), Kurir (ditugaskan), Admin (semua) |
| **Payments** | 💳 | User (milik sendiri), Admin (semua) |
| **Banks** | 🏦 | Admin saja |
| **Routes** | 🗺️ | Admin saja |
| **Rates** | 💰 | Admin saja |
| **Users** | 👥 | Admin saja |

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

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | admin@mail.com | password |
| **Kurir** | kurir@mail.com | password |
| **Pengguna** | user@mail.com | password |

Semua password: `password`

---

© 2026 LogiTrack. Dokumentasi teknis dan panduan penggunaan aplikasi.
