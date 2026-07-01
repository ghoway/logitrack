# Setup Guide

## Prerequisites

- PHP 8.4+
- Composer 2.x
- Node.js 20+ and npm
- SQLite (default) or any supported database

## Installation

### 1. Clone & Install Dependencies

```bash
git clone <repository-url> logitrack
cd logitrack
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for your database preference. Default uses SQLite:

```
DB_CONNECTION=sqlite
# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=logitrack
# DB_USERNAME=root
# DB_PASSWORD=
```

### 3. Database Migration & Seeding

```bash
php artisan migrate --seed
```

This creates all tables and seeds:

- **3 Roles**: `super_admin`, `courier`, `user`
- **8 Permissions**: ViewAny/View for Route, Rate, Shipment, Payment; Create/Update for Shipment
- **3 Users**: admin@mail.com, kurir@mail.com, user@mail.com (all with password `password`)
- **2 Routes**: Jakarta ↔ Surabaya, Bali ↔ Jakarta
- **4 Rates**: Air/Sea pricing for each route
- **2 Banks**: BCA & Mandiri (from `BankSeeder`)

### 4. Build Frontend Assets

```bash
npm run build
```

For development with hot-reload:

```bash
npm run dev
# or
composer run dev
```

### 5. Storage Link (for file uploads)

```bash
php artisan storage:link
```

This creates the `public/storage` symlink needed for payment proof and delivery proof image access.

### 6. Start Development Server

```bash
php artisan serve
```

Access the application at `http://localhost:8000` and the admin panel at `http://localhost:8000/admin`.

## Default Credentials

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@mail.com | password |
| Courier | kurir@mail.com | password |
| User/Customer | user@mail.com | password |
