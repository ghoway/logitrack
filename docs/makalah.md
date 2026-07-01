# LAPORAN MAKALAH — SISTEM MANAJEMEN PENGIRIMAN LOGISTIK (LogiTrack)

**Aplikasi**: LogiTrack — Solusi Pengiriman Terpercaya  
**Platform**: Web-Based (Laravel 13 + Filament 5 + Livewire 4)  
**Tahun**: 2026

---

## DAFTAR ISI

1. [BAB 1 — ANALISIS SDLC](#bab-1--analisis-sdlc)
2. [BAB 2 — ANALISIS KEBUTUHAN SISTEM](#bab-2--analisis-kebutuhan-sistem)
3. [BAB 3 — PERANCANGAN UML](#bab-3--perancangan-uml)
   - 3.1 Use Case Diagram & Narasi Use Case
   - 3.2 Class Diagram
   - 3.3 Sequence Diagram
4. [BAB 4 — IMPLEMENTASI BACK-END](#bab-4--implementasi-back-end)
5. [BAB 5 — IMPLEMENTASI LANDING PAGE](#bab-5--implementasi-landing-page)
6. [BAB 6 — PENUTUP](#bab-6--penutup)

---

## BAB 1 — ANALISIS SDLC

### 1.1 Pendahuluan

LogiTrack adalah sistem informasi manajemen pengiriman logistik berbasis web yang dikembangkan untuk memfasilitasi proses pengiriman barang dari pengirim (sender) hingga ke penerima (receiver). Sistem ini melibatkan tiga aktor utama, yaitu **User (pengirim)**, **Admin (super_admin)**, dan **Kurir (courier)**. Pengembangan sistem menggunakan pendekatan **System Development Life Cycle (SDLC)** model **Waterfall** karena kebutuhan sistem telah terdefinisi dengan jelas sejak awal.

### 1.2 Tahapan SDLC Waterfall

#### 1.2.1 Requirements Analysis (Analisis Kebutuhan)

Pada tahap ini dilakukan identifikasi kebutuhan fungsional dan non-fungsional sistem melalui wawancara dengan calon pengguna (admin logistik, kurir, dan pelanggan). Hasil analisis dirangkum dalam BAB 2.

Kebutuhan utama yang teridentifikasi:
- Sistem harus memiliki tiga peran pengguna: Admin, Kurir, dan User.
- User dapat mendaftar, login, membuat permintaan pengiriman, dan mengunggah bukti pembayaran.
- Admin dapat memverifikasi pembayaran dan menugaskan kurir.
- Kurir dapat memperbarui status pengiriman dan mengunggah bukti pengiriman.
- Setiap pengiriman memiliki nomor tracking unik yang dapat dilacak secara real-time.
- Biaya pengiriman dihitung berdasarkan berat aktual dan berat volumetrik.

#### 1.2.2 System Design (Perancangan Sistem)

Perancangan sistem meliputi:
- **Perancangan basis data**: Entity Relationship Diagram (ERD) dengan enam entitas utama: Users, Routes, Rates, Shipments, Payments, Banks.
- **Perancangan arsitektur**: Menggunakan arsitektur MVC (Model-View-Controller) Laravel dengan panel admin Filament.
- **Perancangan antarmuka**: Menggunakan komponen Filament untuk panel admin dan Tailwind CSS untuk halaman publik.
- **Perancangan UML**: Use Case Diagram, Class Diagram, dan Sequence Diagram (lihat BAB 3).

#### 1.2.3 Implementation (Implementasi)

Implementasi dilakukan dalam dua lingkungan:
- **Back-end**: Framework Laravel 13 dengan Filament 5 untuk panel admin, Spatie Permission untuk RBAC.
- **Front-end publik**: Tailwind CSS 3, Alpine.js, dan Blade template engine.
- **Basis data**: SQLite (pengembangan) / MySQL (produksi).

Detail implementasi terdapat pada BAB 4 dan BAB 5.

#### 1.2.4 Testing (Pengujian)

Pengujian dilakukan menggunakan **Pest PHP 4** dengan jenis pengujian:
- **Unit Test**: Menguji model dan logika perhitungan biaya pengiriman.
- **Feature Test**: Menguji alur lengkap pembuatan pengiriman, pembayaran, dan persetujuan admin.
- Contoh implementasi pengujian mencakup pengujian pembuatan nomor tracking dan pembayaran otomatis (`tests/Feature/ShipmentCalculationTest.php`).

#### 1.2.5 Deployment (Penempatan)

Sistem dapat di-deploy menggunakan Laravel Cloud atau server Nginx dengan langkah-langkah optimasi Laravel standar (config caching, route caching, queue worker, dll).

#### 1.2.6 Maintenance (Pemeliharaan)

Pemeliharaan sistem mencakup pembaruan versi Laravel/Filament, penambahan fitur baru sesuai kebutuhan pengguna, dan perbaikan bug.

### 1.3 Alasan Pemilihan Model Waterfall

Model Waterfall dipilih karena:
1. Kebutuhan sistem telah terdefinisi dengan baik sejak awal.
2. Lingkup proyek berskala menengah dan tidak kompleks.
3. Tim pengembang kecil sehingga koordinasi mudah dilakukan.
4. Dokumen kebutuhan dan desain dapat difinalisasi sebelum implementasi.

---

## BAB 2 — ANALISIS KEBUTUHAN SISTEM

### 2.1 Kebutuhan Fungsional

#### 2.1.1 Kebutuhan Fungsional untuk User (Pengirim)

| Kode | Deskripsi |
|------|-----------|
| **F-U-01** | Sistem menyediakan halaman registrasi untuk pengguna baru. |
| **F-U-02** | Sistem menyediakan halaman login untuk pengguna terdaftar. |
| **F-U-03** | User dapat membuat permintaan pengiriman (shipment request) dengan mengisi detail paket. |
| **F-U-04** | Sistem secara otomatis menghasilkan nomor tracking unik untuk setiap pengiriman. |
| **F-U-05** | Sistem menghitung biaya pengiriman berdasarkan tarif dan berat (aktual & volumetrik). |
| **F-U-06** | Sistem menampilkan instruksi pembayaran (transfer bank/QRIS) setelah pengiriman dibuat. |
| **F-U-07** | User dapat mengunggah bukti pembayaran (proof of payment). |
| **F-U-08** | User dapat melihat daftar pengiriman miliknya beserta statusnya. |
| **F-U-09** | User dapat melacak status pengiriman melalui halaman tracking publik. |
| **F-U-10** | User dapat melihat riwayat pembayaran miliknya. |

#### 2.1.2 Kebutuhan Fungsional untuk Admin (Super Admin)

| Kode | Deskripsi |
|------|-----------|
| **F-A-01** | Admin dapat melihat seluruh data pengiriman tanpa terkecuali. |
| **F-A-02** | Admin dapat memverifikasi pembayaran pengguna (approve payment). |
| **F-A-03** | Admin dapat menetapkan kurir ke pengiriman tertentu. |
| **F-A-04** | Admin dapat memperbarui status pengiriman. |
| **F-A-05** | Admin dapat mengelola data master: rute, tarif, bank, dan pengguna. |
| **F-A-06** | Admin dapat mengelola roles & permissions pengguna. |
| **F-A-07** | Admin dapat melihat dan mengelola seluruh data pembayaran. |

#### 2.1.3 Kebutuhan Fungsional untuk Kurir (Courier)

| Kode | Deskripsi |
|------|-----------|
| **F-C-01** | Kurir dapat melihat daftar pengiriman yang ditugaskan kepadanya. |
| **F-C-02** | Kurir dapat memperbarui status pengiriman (picked_up, in_transit, delivered). |
| **F-C-03** | Kurir dapat mengunggah bukti foto pengiriman (delivery proof) saat status delivered. |

#### 2.1.4 Kebutuhan Fungsional untuk Publik

| Kode | Deskripsi |
|------|-----------|
| **F-P-01** | Publik dapat melacak status pengiriman menggunakan nomor tracking. |
| **F-P-02** | Halaman tracking menampilkan informasi detail pengiriman dan status terkini. |

### 2.2 Kebutuhan Non-Fungsional

| Kode | Deskripsi |
|------|-----------|
| **NF-01** | Sistem dibangun dengan framework Laravel (PHP 8.4) dan panel Filament 5. |
| **NF-02** | Sistem menggunakan basis data relasional (SQLite/MySQL). |
| **NF-03** | Antarmuka menggunakan responsive design (Tailwind CSS). |
| **NF-04** | Sistem menerapkan Role-Based Access Control (RBAC) dengan Spatie Permission. |
| **NF-05** | Setiap pengguna yang telah terdaftar harus login untuk mengakses panel. |
| **NF-06** | Sistem menggunakan Filament Shield untuk manajemen permission visual. |
| **NF-07** | Perhitungan biaya pengiriman menggunakan rumus berat volumetrik ((P × L × T) / 6000). |
| **NF-08** | File bukti pembayaran dan bukti pengiriman disimpan di penyimpanan publik. |

### 2.3 Analisis Aktor Pengguna

| Aktor | Deskripsi | Hak Akses |
|-------|-----------|-----------|
| **User (Pengirim)** | Pelanggan yang ingin mengirim barang | Membuat pengiriman, upload bukti bayar, lihat status |
| **Admin (Super Admin)** | Pengelola sistem logistik | Manajemen data master, verifikasi pembayaran, assignment kurir |
| **Kurir (Courier)** | Petugas lapangan yang mengantar barang | Update status, upload bukti antar |
| **Tamu (Guest/Public)** | Pengunjung web tanpa login | Tracking pengiriman via nomor resi |

### 2.4 Analisis Entitas Data

Berdasarkan perancangan basis data, terdapat enam entitas utama:

1. **Users** — Data pengguna (name, email, password) dengan relasi roles.
2. **Routes** — Rute/asal-tujuan pengiriman (origin, destination, is_active).
3. **Rates** — Tarif pengiriman per rute (route_id, type, price_per_kg, estimated_days).
4. **Shipments** — Pengiriman (tracking_number, sender_id, courier_id, rate_id, receiver data, weight, fee, status).
5. **Payments** — Pembayaran (shipment_id, amount, proof, is_paid). Setiap pengiriman memiliki satu pembayaran.
6. **Banks** — Data rekening bank untuk pembayaran (bank_name, bank_no, account_name, qris_image).

---

## BAB 3 — PERANCANGAN UML

### 3.1 Use Case Diagram & Narasi Use Case

#### 3.1.1 Use Case Diagram (Naratif)

Sistem memiliki **tiga aktor** dan **satu aktor pasif**:

```
┌─────────────────────────────────────────────────────┐
│                  SISTEM LOGITRACK                    │
│                                                      │
│  ┌──────────┐     ┌──────────────────┐              │
│  │  PUBLIC   │────>│ Tracking Resi    │              │
│  └──────────┘     └──────────────────┘              │
│                                                      │
│  ┌──────────┐     ┌──────────────────┐              │
│  │   USER   │────>│ Register/Login   │              │
│  │ (Sender) │     ├──────────────────┤              │
│  │          │────>│ Request Shipment  │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Upload Payment   │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ View My Shipments│              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ View My Payments │              │
│  └──────────┘     └──────────────────┘              │
│                                                      │
│  ┌──────────┐     ┌──────────────────┐              │
│  │  ADMIN   │────>│ Manage Routes    │              │
│  │ (Super)  │     ├──────────────────┤              │
│  │          │────>│ Manage Rates     │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Manage Banks     │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Manage Users     │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Verify Payment   │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Assign Courier   │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ View All Shipments│              │
│  └──────────┘     └──────────────────┘              │
│                                                      │
│  ┌──────────┐     ┌──────────────────┐              │
│  │  COURIER │────>│ View My Tasks    │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Update Status    │              │
│  │          │     ├──────────────────┤              │
│  │          │────>│ Upload Proof     │              │
│  └──────────┘     └──────────────────┘              │
└─────────────────────────────────────────────────────┘
```

#### 3.1.2 Narasi Use Case Lengkap

---

**UC-01: Registrasi Akun**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Registrasi Akun |
| **Aktor** | User (belum terdaftar) |
| **Pre-condition** | Pengguna belum memiliki akun |
| **Post-condition** | Akun berhasil dibuat dengan role "user" |
| **Skenario Normal** | 1. Pengguna membuka halaman `/admin/register`<br>2. Sistem menampilkan form registrasi (nama, email, password, konfirmasi password)<br>3. Pengguna mengisi data dan menekan tombol "Register"<br>4. Sistem memvalidasi data (email unik, password minimal 8 karakter)<br>5. Sistem membuat user baru<br>6. Sistem meng-assign role "user" ke akun tersebut<br>7. Sistem login otomatis dan mengalihkan ke dashboard `/admin` |
| **Skenario Alternatif** | 4a. Jika email sudah terdaftar, sistem menampilkan error validasi<br>4b. Jika password tidak sesuai ketentuan, sistem menampilkan error |
| **Exception** | 4c. Jika rate limit terlampaui (2 percobaan per menit), sistem menampilkan notifikasi throttle |

---

**UC-02: Login**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Login |
| **Aktor** | User, Admin, Kurir |
| **Pre-condition** | Pengguna sudah memiliki akun |
| **Post-condition** | Pengguna berhasil login dan masuk ke dashboard |
| **Skenario Normal** | 1. Pengguna membuka halaman `/admin/login`<br>2. Sistem menampilkan form login (email, password)<br>3. Pengguna memasukkan kredensial dan menekan "Login"<br>4. Sistem memverifikasi kredensial<br>5. Sistem mengalihkan ke dashboard Filament sesuai panel |
| **Skenario Alternatif** | 4a. Kredensial salah, sistem menampilkan pesan error |
| **Exception** | 4b. Rate limit terlampaui, sistem menampilkan notifikasi |

---

**UC-03: Membuat Permintaan Pengiriman**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Membuat Permintaan Pengiriman (Request Shipment) |
| **Aktor** | User (Sender) |
| **Pre-condition** | User sudah login dan memiliki role "user" |
| **Post-condition** | Shipment baru dibuat dengan status "pending", payment record auto-generated |
| **Skenario Normal** | 1. User membuka menu Shipments → Create Shipment<br>2. Sistem menampilkan wizard 3 langkah<br>3. **Langkah 1 — Detail Pengiriman**:<br>   - User memilih rute/tarif dari dropdown (origin → destination, type, price/kg)<br>   - User mengisi data penerima (nama, telepon, alamat)<br>   - User mengisi spesifikasi paket (berat aktual, panjang, lebar, tinggi)<br>   - Sistem otomatis menghitung berat chargeable dan total biaya secara live<br>4. **Langkah 2 — Pembayaran**:<br>   - Sistem menampilkan total biaya dan instruksi pembayaran (rekening bank aktif atau QRIS)<br>   - User mengunggah bukti pembayaran (file gambar)<br>5. **Langkah 3 — Review**:<br>   - Sistem menampilkan pesan konfirmasi<br>   - User menekan "Submit"<br>6. Sistem menyimpan shipment:<br>   - Men-generate nomor tracking (format: `ID-YYYYMMDDXXXX`)<br>   - Membuat payment record dengan status unpaid<br>   - Menyimpan bukti pembayaran<br>7. Sistem menampilkan notifikasi sukses dan mengalihkan ke halaman daftar shipment |
| **Skenario Alternatif** | 3a. Jika user bukan super_admin, field sender_id diisi otomatis dengan ID user saat ini (disabled)<br>6a. Jika koneksi terputus, data tidak tersimpan |
| **Aturan Bisnis** | - Berat chargeable = max(berat aktual, berat volumetrik)<br>- Berat volumetrik = (P × L × T) / 6000<br>- Total biaya = berat chargeable × price_per_kg<br>- Status awal shipment = "pending"<br>- Payment ter-create otomatis dengan is_paid = false |

---

**UC-04: Mengunggah Bukti Pembayaran**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Mengunggah Bukti Pembayaran |
| **Aktor** | User (Sender) |
| **Pre-condition** | User memiliki shipment dengan payment record (bisa saja bukti sudah diupload saat create, atau ingin upload ulang) |
| **Post-condition** | Bukti pembayaran tersimpan, status payment = "Proof Uploaded" |
| **Skenario Normal** | 1. User membuka daftar shipment miliknya<br>2. User memilih shipment yang akan diupload bukti bayar<br>3. User membuka halaman edit shipment/payment<br>4. User mengunggah file gambar bukti pembayaran<br>5. Sistem menyimpan file di storage publik<br>6. Sistem menampilkan notifikasi sukses |
| **Skenario Alternatif** | 4a. Jika file bukan gambar, sistem menampilkan error validasi<br>4b. Jika file melebihi batas ukuran, sistem menolak upload |

---

**UC-05: Verifikasi Pembayaran oleh Admin**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Verifikasi Pembayaran |
| **Aktor** | Admin (Super Admin) |
| **Pre-condition** | Terdapat shipment dengan status "pending" dan payment.is_paid = false (proof sudah diupload) |
| **Post-condition** | Payment.is_paid berubah menjadi true, status shipment berubah menjadi "picked_up" |
| **Skenario Normal** | 1. Admin membuka daftar shipment<br>2. Admin melihat kolom Payment Status = "Proof Uploaded"<br>3. Admin meng-klik tombol "Approve Payment" pada record yang sesuai<br>4. Sistem menjalankan transaksi database:<br>   - Update payment.is_paid = true<br>   - Update shipment.status = "picked_up"<br>5. Sistem menampilkan notifikasi sukses |
| **Aturan Bisnis** | - Tombol "Approve Payment" hanya muncul untuk admin<br>- Tombol hanya muncul jika payment belum dibayar (is_paid = false)<br>- Approval dilakukan dalam database transaction untuk konsistensi data |

---

**UC-06: Menetapkan Kurir**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Menetapkan Kurir ke Pengiriman |
| **Aktor** | Admin (Super Admin) |
| **Pre-condition** | Shipment sudah memiliki payment yang terverifikasi (bisa juga langsung ditetapkan kapan saja oleh admin) |
| **Post-condition** | Shipment memiliki courier_id yang terisi |
| **Skenario Normal** | 1. Admin membuka halaman edit shipment<br>2. Admin memilih kurir dari dropdown "Courier" yang telah difilter (hanya user dengan role courier)<br>3. Admin menyimpan perubahan<br>4. Sistem meng-update courier_id pada record shipment |
| **Aturan Bisnis** | - Hanya user dengan role "courier" yang muncul di dropdown<br>- Admin super_admin dapat menetapkan/mengubah kurir kapan saja |

---

**UC-07: Memperbarui Status Pengiriman**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Memperbarui Status Pengiriman |
| **Aktor** | Kurir, Admin |
| **Pre-condition** | Shipment sudah ditugaskan ke kurir yang bersangkutan (untuk kurir) |
| **Post-condition** | Status shipment berubah sesuai pilihan |
| **Skenario Normal** | 1. Kurir membuka daftar shipment (hanya yang ditugaskan kepadanya)<br>2. Kurir meng-klik tombol "Update Status" pada record<br>3. Sistem menampilkan modal dengan dropdown status<br>4. Kurir memilih status baru (picked_up / in_transit / delivered)<br>5. Jika memilih "delivered", field upload bukti foto muncul (required)<br>6. Kurir mengunggah foto bukti pengiriman<br>7. Kurir menyimpan<br>8. Sistem meng-update status dan delivery_proof |
| **Aturan Bisnis** | - Kurir hanya bisa meng-update shipment yang ditugaskan kepadanya<br>- Field delivery_proof hanya muncul dan required saat status = "delivered"<br>- Admin bisa meng-update status shipment mana pun |

---

**UC-08: Melacak Pengiriman (Publik)**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Melacak Pengiriman via Nomor Tracking |
| **Aktor** | Publik (Guest) |
| **Pre-condition** | Pengirim telah membuat shipment dan memiliki nomor tracking |
| **Post-condition** | Sistem menampilkan detail pengiriman |
| **Skenario Normal** | 1. Pengunjung membuka halaman `/tracking`<br>2. Sistem menampilkan form input nomor tracking<br>3. Pengunjung memasukkan nomor tracking dan menekan "Lacak"<br>4. Sistem mencari shipment berdasarkan nomor tracking<br>5. Jika ditemukan, sistem menampilkan:<br>   - Status pengiriman (badge warna)<br>   - Nomor tracking<br>   - Nama pengirim & penerima<br>   - Rute & tipe layanan<br>   - Berat tagihan & estimasi sampai<br>   - Total biaya |
| **Skenario Alternatif** | 5a. Jika nomor tracking tidak ditemukan, sistem menampilkan pesan error "Pengiriman tidak ditemukan" |

---

**UC-09: Mengelola Data Master (Routes, Rates, Banks)**

| Elemen | Deskripsi |
|--------|-----------|
| **Use Case** | Mengelola Data Master |
| **Aktor** | Admin (Super Admin) |
| **Pre-condition** | Admin telah login |
| **Post-condition** | Data master berhasil ditambah/diubah/dihapus |
| **Skenario Normal** | 1. Admin membuka menu Routes/Rates/Banks<br>2. Sistem menampilkan daftar data dalam tabel<br>3. Admin dapat:<br>   - Menambah data baru via tombol "Create"<br>   - Mengedit data via tombol "Edit"<br>   - Menghapus data via tombol "Delete"<br>4. Sistem menyimpan perubahan dan menampilkan notifikasi |
| **Aturan Bisnis** | - Route memiliki unique constraint (origin, destination)<br> - Rate memiliki unique constraint (route_id, type)<br> - Hanya route dengan is_active = true yang muncul di dropdown pemilihan tarif |

---

### 3.2 Class Diagram

#### 3.2.1 Entity Class Diagram (Model)

Berikut adalah class diagram untuk entitas utama sistem:

```
┌─────────────────────────────────────────────────────────────┐
│                         USER                                │
├─────────────────────────────────────────────────────────────┤
│ - id: bigInteger (PK)                                       │
│ - name: string                                              │
│ - email: string (unique)                                    │
│ - email_verified_at: timestamp (nullable)                    │
│ - password: string (hashed)                                 │
│ - remember_token: string (nullable)                         │
│ - created_at: timestamp                                     │
│ - updated_at: timestamp                                     │
├─────────────────────────────────────────────────────────────┤
│ + roles(): MorphToMany (via Spatie HasRoles)                │
│ + permissions(): MorphToMany (via Spatie HasRoles)          │
└─────────────────────────────────────────────────────────────┘
         │ 1                              │ 1
         │                                │
         │ sender                         │ courier
         │                                │
         ▼ 0..*                           ▼ 0..*
┌─────────────────────────────────────────────────────────────┐
│                       SHIPMENT                              │
├─────────────────────────────────────────────────────────────┤
│ - id: bigInteger (PK)                                       │
│ - tracking_number: string (auto-generated: ID-YYYYMMDDXXXX) │
│ - sender_id: bigInteger (FK -> users.id)                    │
│ - courier_id: bigInteger (FK -> users.id, nullable)          │
│ - rate_id: bigInteger (FK -> rates.id)                      │
│ - receiver_name: string                                     │
│ - receiver_phone: string                                    │
│ - receiver_address: text                                    │
│ - actual_weight: decimal(8,2)                               │
│ - length: decimal(8,2)                                      │
│ - width: decimal(8,2)                                       │
│ - height: decimal(8,2)                                      │
│ - chargeable_weight: decimal(8,2)                           │
│ - total_shipping_fee: decimal(12,2)                         │
│ - status: string (enum: pending/picked_up/in_transit/delivered)│
│ - delivery_proof: string (nullable)                          │
│ - created_at: timestamp                                      │
│ - updated_at: timestamp                                      │
├─────────────────────────────────────────────────────────────┤
│ + sender(): BelongsTo(User, sender_id)                      │
│ + courier(): BelongsTo(User, courier_id)                    │
│ + rate(): BelongsTo(Rate)                                   │
│ + payment(): HasOne(Payment)                                │
│ # boot(): static (generates tracking_number on creating)     │
│           (creates payment on created)                       │
└─────────────────────────────────────────────────────────────┘
         │ 0..*
         │
         ▼ 1
┌─────────────────────────────────────────────────────────────┐
│                      PAYMENT                                │
├─────────────────────────────────────────────────────────────┤
│ - id: bigInteger (PK)                                       │
│ - shipment_id: bigInteger (FK -> shipments.id)              │
│ - amount: decimal(12,2)                                     │
│ - proof: string (nullable)                                  │
│ - is_paid: boolean                                          │
│ - created_at: timestamp                                     │
│ - updated_at: timestamp                                     │
├─────────────────────────────────────────────────────────────┤
│ + shipment(): BelongsTo(Shipment)                           │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       RATE                                  │
├─────────────────────────────────────────────────────────────┤
│ - id: bigInteger (PK)                                       │
│ - route_id: bigInteger (FK -> routes.id)                    │
│ - type: string (enum: darat/kapal/pesawat)                  │
│ - price_per_kg: decimal(12,2)                               │
│ - estimated_days: integer                                   │
│ - created_at: timestamp                                     │
│ - updated_at: timestamp                                     │
├─────────────────────────────────────────────────────────────┤
│ + route(): BelongsTo(Route)                                 │
│ + shipments(): HasMany(Shipment)                            │
└─────────────────────────────────────────────────────────────┘
         │ *
         │
         ▼ 1
┌─────────────────────────────────────────────────────────────┐
│                       ROUTE                                 │
├─────────────────────────────────────────────────────────────┤
│ - id: bigInteger (PK)                                       │
│ - origin: string                                            │
│ - destination: string                                       │
│ - is_active: boolean                                        │
│ - created_at: timestamp                                     │
│ - updated_at: timestamp                                     │
├─────────────────────────────────────────────────────────────┤
│ + rates(): HasMany(Rate)                                    │
│ # unique: (origin, destination)                             │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       BANK                                  │
├─────────────────────────────────────────────────────────────┤
│ - id: bigInteger (PK)                                       │
│ - bank_name: string                                         │
│ - bank_logo: string (nullable)                              │
│ - bank_no: string (nullable)                                │
│ - account_name: string (nullable)                           │
│ - qris_image: string (nullable)                             │
│ - is_active: boolean                                        │
│ - created_at: timestamp                                     │
│ - updated_at: timestamp                                     │
├─────────────────────────────────────────────────────────────┤
│ (Standalone — tidak memiliki relasi ke entitas lain)         │
└─────────────────────────────────────────────────────────────┘
```

**Multiplicity Summary:**
- `User (sender)` —1---`0..*`→ `Shipment`
- `User (courier)` —1---`0..*`→ `Shipment`
- `Shipment` —`0..*`---1→ `Rate`
- `Rate` —`*`---1→ `Route`
- `Shipment` —1---`1`→ `Payment` (auto-created via `created` event)
- `Bank` — Standalone (no foreign key relationships)

#### 3.2.2 Controller & Resource Classes (Arsitektur MVC + Filament)

```
┌─────────────────────────────────────────────┐
│         LandingPageController               │
├─────────────────────────────────────────────┤
│ + index(): View (menampilkan stats)         │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│          TrackingController                 │
├─────────────────────────────────────────────┤
│ + index(): View                             │
│ + track(Request): View                      │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│           ProfileController                 │
├─────────────────────────────────────────────┤
│ + edit(Request): View                       │
│ + update(ProfileUpdateRequest): Redirect    │
│ + destroy(Request): Redirect                │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│       Filament Resource Classes             │
├─────────────────────────────────────────────┤
│ ShipmentResource                            │
│  + getEloquentQuery(): Builder (RBAC scope) │
│  + calculateFees(Get, Set): void            │
│  + getPaymentInstructionsHtml(mixed): string│
│  + form(Schema): Schema (3-step wizard)     │
│  + table(Table): Table                      │
│  + infolist(Schema): Schema                 │
│  + getPages(): array                        │
│                                             │
│ UserResource (ManageRecords)                │
│ RouteResource (ManageRecords, super_admin)  │
│ RateResource (ManageRecords, super_admin)   │
│ BankResource (ManageRecords)                │
│ PaymentResource                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│            Policy Classes                   │
├─────────────────────────────────────────────┤
│ ShipmentPolicy, UserPolicy, RoutePolicy     │
│ RatePolicy, PaymentPolicy, BankPolicy,      │
│ RolePolicy                                  │
│ (All use HandlesAuthorization + Filament    │
│  Shield permission checks)                  │
└─────────────────────────────────────────────┘
```

### 3.3 Sequence Diagram

#### 3.3.1 Sequence Diagram — Membuat Permintaan Pengiriman

```
User                    CreateShipment              Shipment            Payment
 │                          │                         │                  │
 │— 1. Buka form create ──→│                         │                  │
 │                          │                         │                  │
 │— 2. Pilih rate, isi     │                         │                  │
 │    detail paket ────────→│                         │                  │
 │                          │— 3. calculateFees()    │                  │
 │                          │    (live, on blur)      │                  │
 │                          │←—— return fee ———————│                  │
 │                          │                         │                  │
 │— 4. Upload bukti bayar ─→│                         │                  │
 │                          │                         │                  │
 │— 5. Submit ────────────→│                         │                  │
 │                          │                         │                  │
 │                          │— 6. mutateFormDataBefore │                  │
 │                          │    Create()             │                  │
 │                          │    (set sender_id,      │                  │
 │                          │     capture proof)      │                  │
 │                          │                         │                  │
 │                          │— 7. Save Shipment ────→│                  │
 │                          │    (creating event)     │                  │
 │                          │    Generate tracking_no │                  │
 │                          │                         │                  │
 │                          │    (created event)      │                  │
 │                          │— 8. Auto-create Pay ────│—→ Create Payment │
 │                          │    ment                 │    (is_paid=false│
 │                          │                         │    amount=fee)   │
 │                          │                         │                  │
 │                          │— 9. afterCreate()      │                  │
 │                          │    Update payment proof │                  │
 │                          │                         │                  │
 │                          │←— return success ─────│                  │
 │                          │                         │                  │
 │←— 10. Notifikasi ──────│                         │                  │
 │    & Redirect ke list    │                         │                  │
```

#### 3.3.2 Sequence Diagram — Verifikasi Pembayaran oleh Admin

```
Admin                   ListShipments              Shipment            Payment
 │                          │                         │                  │
 │— 1. Lihat daftar        │                         │                  │
 │    shipment ───────────→│                         │                  │
 │                          │— 2. Query dengan relasi │                  │
 │                          │    payment ────────────→│—→ Load payment ─→│
 │                          │                         │                  │
 │— 3. Klik "Approve       │                         │                  │
 │    Payment" ───────────→│                         │                  │
 │                          │                         │                  │
 │                          │— 4. DB::transaction     │                  │
 │                          │    {                     │                  │
 │                          │    5. Update             │                  │
 │                          │    payment.is_paid=true ─│────────────────→│
 │                          │    6. Update             │                  │
 │                          │    shipment.status=     ─→│                 │
 │                          │    "picked_up"           │                  │
 │                          │    }                     │                  │
 │                          │                         │                  │
 │                          │←— return success ──────│                  │
 │                          │                         │                  │
 │←— 7. Notifikasi ───────│                         │                  │
 │    sukses                │                         │                  │
```

#### 3.3.3 Sequence Diagram — Kurir Update Status & Upload Bukti

```
Courier                 ListShipments              Shipment
 │                          │                         │
 │— 1. Lihat daftar tugas ─→│                         │
 │                          │— 2. Query where         │
 │                          │    courier_id=auth_id ──→│
 │                          │                         │
 │— 3. Klik "Update Status"→│                         │
 │                          │                         │
 │— 4. Modal: pilih status ─│                         │
 │    (jika "delivered",    │                         │
 │     upload foto) ───────→│                         │
 │                          │                         │
 │— 5. Submit ────────────→│                         │
 │                          │— 6. Update status ─────→│
 │                          │    (dan delivery_proof  │
 │                          │     jika delivered)     │
 │                          │                         │
 │                          │←— return success ──────│
 │                          │                         │
 │←— 7. Notifikasi ───────│                         │
 │    sukses & refresh table│                         │
```

#### 3.3.4 Sequence Diagram — Tracking Publik

```
Guest/Public             TrackingController             Shipment
 │                              │                         │
 │— 1. Buka halaman            │                         │
 │    /tracking ──────────────→│                         │
 │←— 2. Tampilkan form ───────│                         │
 │                              │                         │
 │— 3. Masukkan no. tracking   │                         │
 │    & klik "Lacak" ─────────→│                         │
 │                              │                         │
 │                              │— 4. where('tracking_   │
 │                              │    number',...) ──────→│
 │                              │←— return shipment ────│
 │                              │    (with sender, rate, │
 │                              │     route, payment)    │
 │                              │                         │
 │←— 5. Tampilkan detail ─────│                         │
 │    shipment (status badge,   │                         │
 │    info pengirim/penerima,   │                         │
 │    rute, biaya, dll.)        │                         │
```

---

## BAB 4 — IMPLEMENTASI BACK-END

### 4.1 Lingkungan Pengembangan

| Komponen | Teknologi |
|----------|-----------|
| Bahasa Pemrograman | PHP 8.4 |
| Framework | Laravel 13 |
| Panel Admin | Filament 5 |
| Frontend | Livewire 4, Alpine.js, Tailwind CSS 3 |
| Database | SQLite (dev) / MySQL (prod) |
| RBAC | Spatie Permission + Filament Shield |

### 4.2 Implementasi Model (Entity Classes)

#### 4.2.1 Model User (`app/Models/User.php`)

Menggunakan trait `HasRoles` dari Spatie untuk manajemen role. Atribut fillable mencakup `name`, `email`, `password`. Password secara otomatis di-hash oleh Laravel melalui cast `hashed`.

```php
class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    #[Fillable(['name', 'email', 'password'])]
    #[Hidden(['password', 'remember_token'])]
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

#### 4.2.2 Model Shipment (`app/Models/Shipment.php`)

Model Shipment memiliki event `creating` untuk generate nomor tracking unik dan event `created` untuk auto-create payment record.

```php
class Shipment extends Model
{
    protected $fillable = ['tracking_number', 'sender_id', 'courier_id', 'rate_id', 
        'receiver_name', 'receiver_phone', 'receiver_address', 'actual_weight', 
        'length', 'width', 'height', 'chargeable_weight', 'total_shipping_fee', 
        'status', 'delivery_proof'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            $shipment->tracking_number = 'ID-'.now()->format('Ymd').strtoupper(Str::random(4));
        });

        static::created(function ($shipment) {
            $shipment->payment()->create([
                'amount' => $shipment->total_shipping_fee,
                'proof' => '',
                'is_paid' => false,
            ]);
        });
    }

    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sender_id'); }
    public function courier(): BelongsTo { return $this->belongsTo(User::class, 'courier_id'); }
    public function rate(): BelongsTo { return $this->belongsTo(Rate::class); }
    public function payment(): HasOne { return $this->hasOne(Payment::class); }
}
```

#### 4.2.3 Model Payment (`app/Models/Payment.php`)

```php
class Payment extends Model
{
    protected $fillable = ['shipment_id', 'amount', 'proof', 'is_paid'];
    protected $casts = ['is_paid' => 'boolean'];
    public function shipment(): BelongsTo { return $this->belongsTo(Shipment::class); }
}
```

#### 4.2.4 Model Route (`app/Models/Route.php`)

```php
class Route extends Model
{
    protected $fillable = ['origin', 'destination', 'is_active'];
    public function rates(): HasMany { return $this->hasMany(Rate::class); }
}
```

#### 4.2.5 Model Rate (`app/Models/Rate.php`)

```php
class Rate extends Model
{
    protected $fillable = ['route_id', 'type', 'price_per_kg', 'estimated_days'];
    public function route(): BelongsTo { return $this->belongsTo(Route::class); }
    public function shipments(): HasMany { return $this->hasMany(Shipment::class); }
}
```

#### 4.2.6 Model Bank (`app/Models/Bank.php`)

```php
class Bank extends Model
{
    use HasFactory;
    protected $fillable = ['bank_name', 'bank_logo', 'bank_no', 'account_name', 'qris_image', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
```

### 4.3 Implementasi RBAC (Role-Based Access Control)

#### 4.3.1 Role Definitions

Terdapat tiga role yang didefinisikan:
- **super_admin** — Akses penuh ke seluruh sistem
- **courier** — Akses terbatas ke shipment yang ditugaskan
- **user** — Akses terbatas ke shipment milik sendiri

#### 4.3.2 Global Gate (`app/Providers/AppServiceProvider.php`)

```php
Gate::before(fn ($user, $ability) => $user->hasRole('super_admin') ? true : null);
```

Grant akses penuh ke super_admin untuk semua abilities.

#### 4.3.3 Query Scoping pada Shipment Resource

Setiap user hanya melihat data yang relevan dengan perannya:

```php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user->hasRole('super_admin')) return $query;
    if ($user->hasRole('courier')) return $query->where('courier_id', $user->id);
    if ($user->hasRole('user')) return $query->where('sender_id', $user->id);

    return $query->whereRaw('1 = 0');
}
```

#### 4.3.4 Custom Register Page dengan Assign Role (`app/Filament/Pages/Auth/Register.php`)

```php
class Register extends BaseRegister
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);
        $user->assignRole('user');
        return $user;
    }
}
```

### 4.4 Implementasi Filament Resources

#### 4.4.1 ShipmentResource — Form Wizard 3 Langkah

Menggunakan komponen `Wizard` Filament dengan 3 step:

1. **Shipment & Package Details**: Select rate, input receiver data, input package specs dengan live fee calculation.
2. **Payment & Upload Proof**: Menampilkan instruksi pembayaran dari bank aktif, upload bukti bayar.
3. **Review & Submit**: Konfirmasi akhir.

**Live Fee Calculation** menggunakan callback `afterStateUpdated` pada field weight/dimension:

```php
public static function calculateFees(Get $get, Set $set): void
{
    $volumeWeight = ($length * $width * $height) / 6000;
    $chargeableWeight = max($actualWeight, $volumeWeight);
    $set('chargeable_weight', round($chargeableWeight, 2));
    
    $rate = Rate::find($get('rate_id'));
    if ($rate) {
        $totalFee = $chargeableWeight * $rate->price_per_kg;
        $set('total_shipping_fee', round($totalFee, 2));
    }
}
```

#### 4.4.2 ShipmentResource — Table Actions

**Approve Payment** (super_admin only):
```php
Action::make('approvePayment')
    ->visible(fn ($record): bool => auth()->user()?->hasRole('super_admin') 
        && $record->payment && !$record->payment->is_paid)
    ->action(fn ($record) => DB::transaction(function () use ($record) {
        $record->payment->update(['is_paid' => true]);
        $record->update(['status' => 'picked_up']);
    })),
```

**Update Status** (courier atau super_admin):
```php
Action::make('updateStatus')
    ->form([
        Select::make('status')->options([...])->live()->required(),
        FileUpload::make('delivery_proof')
            ->visible(fn (Get $get): bool => $get('status') === 'delivered')
            ->required(fn (Get $get): bool => $get('status') === 'delivered'),
    ])
    ->action(fn ($record, array $data) => $record->update($data)),
```

#### 4.4.3 ShipmentResource — CreateShipment Page

Pada `mutateFormDataBeforeCreate`, data `payment_proof` dipisahkan dari data shipment untuk mencegah error karena kolom tersebut tidak ada di tabel shipments.

```php
protected function mutateFormDataBeforeCreate(array $data): array
{
    if (!auth()->user()->hasRole('super_admin')) {
        $data['sender_id'] = auth()->id();
    }
    $this->paymentProof = $data['payment_proof'] ?? null;
    unset($data['payment_proof']);
    return $data;
}

protected function afterCreate(): void
{
    if ($this->paymentProof) {
        $this->record->payment->update(['proof' => $this->paymentProof]);
    }
}
```

### 4.5 Implementasi Controller

#### 4.5.1 LandingPageController

Mengumpulkan data statistik untuk landing page:

```php
public function index()
{
    $stats = [
        'shipments' => Shipment::count(),
        'delivered' => Shipment::where('status', 'delivered')->count(),
        'routes' => Route::where('is_active', true)->count(),
        'customers' => User::role('user')->count(),
    ];
    return view('landing', compact('stats'));
}
```

#### 4.5.2 TrackingController

Menerima nomor tracking dan menampilkan detail shipment:

```php
public function track(Request $request)
{
    $validated = $request->validate(['tracking_number' => 'required|string']);
    $shipment = Shipment::with(['sender', 'rate.route', 'payment'])
        ->where('tracking_number', $validated['tracking_number'])
        ->first();
    return view('tracking', compact('shipment'));
}
```

### 4.6 Implementasi Routes

```php
// Web Routes (web.php)
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::get('/tracking/search', [TrackingController::class, 'track'])->name('tracking.track');
Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// Admin Panel (via Filament AdminPanelProvider)
// /admin         → Dashboard Filament
// /admin/login   → Login Filament
// /admin/register → Register Filament (custom)
```

### 4.7 Implementasi Testing

```php
// tests/Feature/ShipmentCalculationTest.php
it('generates tracking number and auto-creates payment', function () {
    $user = User::factory()->create();
    $rate = Rate::factory()->create(['price_per_kg' => 10000]);
    
    $shipment = Shipment::factory()->create([
        'sender_id' => $user->id,
        'rate_id' => $rate->id,
        'actual_weight' => 5,
        'length' => 10, 'width' => 10, 'height' => 10,
        'chargeable_weight' => 5, 'total_shipping_fee' => 50000,
    ]);

    expect($shipment->tracking_number)->toStartWith('ID-');
    expect($shipment->payment)->not->toBeNull();
    expect($shipment->payment->is_paid)->toBeFalse();
    expect($shipment->payment->amount)->toEqual(50000);
});
```

---

## BAB 5 — IMPLEMENTASI LANDING PAGE

### 5.1 Deskripsi

Landing page adalah halaman depan aplikasi LogiTrack yang dapat diakses oleh publik di route `/`. Halaman ini dibangun menggunakan Blade template dengan Tailwind CSS 3 dan Alpine.js untuk interaktivitas.

### 5.2 Arsitektur Halaman

Landing page terdiri dari enam seksi utama:

1. **Header/Navigation Bar** — Fixed top navbar dengan logo, menu navigasi, dan tombol login/register.
2. **Hero Section** — Bagian utama dengan headline, deskripsi, dan CTA buttons.
3. **Stats Section** — Menampilkan statistik (total pengiriman, berhasil dikirim, rute aktif, pelanggan).
4. **Features Section** — Grid 3 kolom fitur unggulan (lacak real-time, harga kompetitif, aman, cakupan luas, estimasi tepat, dukungan 24/7).
5. **CTA Section** — Call-to-action background gradien untuk mendaftar.
6. **How It Works Section** — 3 langkah mudah (daftar akun, buat pengiriman, lacak & terima).
7. **Footer** — Informasi perusahaan, menu layanan, kontak.

### 5.3 Komponen Utama

#### 5.3.1 Navigation dengan Auth Check

Menggunakan Blade directive `@auth` / `@else` untuk conditional rendering:

```blade
@auth
    <a href="/admin">Dashboard</a>
@else
    <a href="/admin/login">Masuk</a>
    <a href="/admin/register">Daftar</a>
@endauth
```

#### 5.3.2 Mobile Responsive Menu

Menggunakan Alpine.js (`x-data`, `x-show`, `@click`) untuk toggle menu mobile:

```blade
<header x-data="{ mobileOpen: false }">
    <button @click="mobileOpen = !mobileOpen">
        <!-- hamburger icon -->
    </button>
    <div x-show="mobileOpen" @click.outside="mobileOpen = false">
        <!-- mobile nav items -->
    </div>
</header>
```

#### 5.3.3 Stats Display (Dinamis dari Database)

Data statistik diambil dari `LandingPageController` dan ditampilkan dengan animasi angka besar:

```blade
<div class="text-3xl sm:text-4xl font-bold text-amber-600">
    {{ number_format($stats['shipments']) }}
</div>
<div class="mt-1 text-sm text-gray-500">Total Pengiriman</div>
```

### 5.4 Halaman Tracking Publik

Halaman `/tracking` memungkinkan publik melacak status pengiriman tanpa login:

```blade
<form method="GET" action="{{ route('tracking.track') }}" class="flex gap-3">
    <input type="text" name="tracking_number" placeholder="Masukkan nomor tracking">
    <button type="submit">Lacak</button>
</form>

@isset($shipment)
    <!-- Tampilkan detail shipment: status badge, info pengirim/penerima, 
         rute, layanan, berat, estimasi, total biaya -->
@endisset
```

Status badge menggunakan class kondisional berdasarkan status:
```blade
@if($shipment->status === 'delivered') bg-green-100 text-green-700
@elseif($shipment->status === 'in_transit') bg-blue-100 text-blue-700
@elseif($shipment->status === 'picked_up') bg-yellow-100 text-yellow-700
@else bg-gray-100 text-gray-700
@endif
```

### 5.5 Teknologi Frontend

| Komponen | Teknologi |
|----------|-----------|
| Template Engine | Blade (Laravel) |
| CSS Framework | Tailwind CSS 3.4 |
| JavaScript | Alpine.js 3 |
| Bundler | Vite |
| Icon | Heroicons (inline SVG) |

### 5.6 Desain Responsif

- **Mobile-first** dengan breakpoint `sm:`, `md:`, `lg:`
- Navigasi berubah menjadi hamburger menu di layar `lg:hidden`
- Grid menyesuaikan kolom: `grid-cols-1` (mobile) → `sm:grid-cols-2` → `lg:grid-cols-3`
- Header menggunakan `fixed top-0 inset-x-0` dengan `backdrop-blur-sm`

### 5.7 Dark Mode

Mendukung dark mode dengan kelas `dark:`:
```blade
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
```

---

## BAB 6 — PENUTUP

### 6.1 Kesimpulan

Berdasarkan analisis, perancangan, dan implementasi yang telah dilakukan, dapat disimpulkan:

1. **Sistem LogiTrack** berhasil dibangun menggunakan pendekatan SDLC Waterfall dengan Laravel 13 sebagai framework back-end dan Filament 5 sebagai panel admin. Sistem mendukung tiga peran pengguna (User, Admin, Kurir) dengan kontrol akses berbasis peran (RBAC) menggunakan Spatie Permission.

2. **Analisis kebutuhan** menghasilkan 14 kebutuhan fungsional yang mencakup seluruh alur bisnis — mulai dari registrasi pengguna, pembuatan permintaan pengiriman dengan perhitungan biaya otomatis, upload bukti pembayaran, verifikasi pembayaran oleh admin, assignment kurir, hingga update status dan tracking publik.

3. **Perancangan UML** meliputi use case diagram dengan 9 narasi use case lengkap, class diagram yang mendefinisikan 6 entitas utama (User, Shipment, Payment, Rate, Route, Bank) beserta relasi dan multiplicitinya, serta 4 sequence diagram yang menggambarkan alur proses kunci (pembuatan pengiriman, verifikasi pembayaran, update status oleh kurir, dan tracking publik).

4. **Implementasi back-end** berhasil menerjemahkan class diagram ke dalam model Eloquent dengan relasi, event, dan boot methods. Implementasi RBAC mencakup global gate untuk super_admin, query scoping pada resource, dan custom register page untuk assign role. Fitur perhitungan biaya pengiriman menggunakan rumus berat volumetrik ((P×L×T)/6000) diimplementasikan secara live pada form wizard.

5. **Landing page** dibangun dengan Blade, Tailwind CSS, dan Alpine.js, menampilkan informasi perusahaan, statistik dinamis, fitur layanan, dan navigasi ke halaman login/register (Filament) serta tracking publik.

### 6.2 Saran

Beberapa saran untuk pengembangan sistem ke depannya:

1. **Integrasi payment gateway** — Mengganti sistem upload bukti manual dengan payment gateway otomatis (Midtrans, Xendit, dll).
2. **Notifikasi real-time** — Menambahkan notifikasi email/WhatsApp untuk perubahan status pengiriman.
3. **Fitur multi-cabang** — Mendukung multiple lokasi/cabang dengan manajemen stok dan pengiriman antar cabang.
4. **Laporan dan analitik** — Dashboard dengan grafik dan laporan export (PDF/Excel) untuk manajemen.
5. **Aplikasi mobile** — Mengembangkan aplikasi mobile untuk kurir agar lebih praktis di lapangan.
6. **Integrasi API** — Menyediakan REST API untuk integrasi dengan sistem eksternal.
7. **Peningkatan testing** — Menambah coverage testing untuk semua skenario bisnis kritis.

---

**Dokumen ini disusun sebagai laporan makalah pengembangan Sistem Informasi Manajemen Pengiriman Logistik — LogiTrack.**

*Teknologi: Laravel 13, Filament 5, Livewire 4, Tailwind CSS 3, Alpine.js, Spatie Permission*  
*Tahun Pengembangan: 2026*
