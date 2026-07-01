# Deployment

## Prerequisites

- PHP 8.4+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD (for image processing)
- Composer 2.x
- Node.js 20+ and npm
- Database (SQLite/MySQL/PostgreSQL)
- Web server (Nginx / Apache / Laravel Cloud)

## Production Build

```bash
# Install dependencies (no dev)
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Environment
cp .env.example .env
php artisan key:generate --force
```

## Environment Configuration

Key `.env` variables to configure:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=logitrack
DB_USERNAME=logitrack
DB_PASSWORD=secure-password

SESSION_DRIVER=file   # or redis/memcached in production
CACHE_DRIVER=file     # or redis/memcached
QUEUE_CONNECTION=sync # or database/redis
```

## Laravel Optimizations

```bash
# Cache routes, config, and views
php artisan route:cache
php artisan config:cache
php artisan view:cache

# Storage link for uploaded files
php artisan storage:link
```

## Database

```bash
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force
```

## File Permissions

Ensure the web server has write access to:
- `storage/`
- `bootstrap/cache/`
- `public/storage/` (symlink target)

## Web Server Configuration

### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/logitrack/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Laravel Cloud

The recommended deployment platform for Laravel applications is [Laravel Cloud](https://cloud.laravel.com/). It provides zero-downtime deployments, automatic scaling, and built-in observability.

## Post-Deployment Checklist

- [ ] Environment variables configured correctly
- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Storage link created (`php artisan storage:link`)
- [ ] Routes, config, and views cached
- [ ] Database migrated and seeded
- [ ] File permissions set correctly
- [ ] Queue worker running (if using queue)
- [ ] SSL certificate installed
- [ ] Regular backups configured
