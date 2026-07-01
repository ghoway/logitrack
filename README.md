<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# LogiTrack

A shipment tracking and logistics management application built with Laravel 13 and Filament 5.

## System Requirements

- PHP 8.4 or higher
- Composer 2.x
- Node.js 20+ & npm 10+
- Database: MySQL 8.0+ / MariaDB 10.4+ / PostgreSQL 15+ / SQLite 3.8+
- GD or Imagick PHP extension (for image uploads)
- Web server: Nginx / Apache / Laravel Herd / Laravel Sail

## Installation

```bash
# 1. Clone the repository
git clone <repo-url> logitrack
cd logitrack

# 2. Install PHP dependencies
composer install --no-interaction

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate --no-interaction

# 5. Configure database in .env (DB_* variables)
#    Example for MySQL:
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=logitrack
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Install Node dependencies & build assets
npm install && npm run build

# 7. Run migrations and seeders
php artisan migrate:fresh --seed --no-interaction

# 8. Create storage symlink (for uploaded files)
php artisan storage:link --no-interaction

# 9. Start development server
php artisan serve
```

## Default Users

| Name   | Email            | Password  | Role         |
|--------|------------------|-----------|--------------|
| Wahyu  | wahyu@mail.com   | password  | super_admin  |
| Humam  | humam@mail.com   | password  | courier      |
| Rafi   | rafi@mail.com    | password  | courier      |
| Usamah | usamah@mail.com  | password  | user         |
| Ellen  | ellen@mail.com   | password  | user         |

## Features

- **Role-based access** — Super admin, courier, and user roles with appropriate permissions
- **Shipment management** — Create, edit, view, and manage shipments with status tracking
- **Wizard creation flow** — Multi-step form for creating shipments with fee calculation
- **Shipping calculations** — Automatic fee calculation based on weight, dimensions, route, and shipping type
- **Delivery proof** — Upload and view delivery proof images for completed shipments
- **Dashboard widgets** — Stats overview, charts (status distribution, revenue), and recent shipments table
- **Courier assignment** — Assign couriers to shipments for delivery

## Tech Stack

- **Laravel 13** — Backend framework
- **Filament 5** — Admin panel UI
- **Livewire 4** — Reactive components
- **Tailwind CSS 4** — Styling
- **Spatie Permission** — Role & permission management
