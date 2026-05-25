# Ecommerce1 — Botble CMS Ecommerce Store

A full-featured ecommerce website built on **Laravel 9** and **Botble CMS**, using the **Shopwise (Evaly)** theme. Includes product catalog, cart, checkout, blog, multi-language support, payment gateways, and an admin dashboard.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 9, PHP 8.0+ |
| CMS | Botble CMS (platform + plugins) |
| Frontend Theme | Shopwise (Evaly) |
| Database | MySQL / MariaDB |
| Server | Apache (mod_rewrite) or Nginx |
| Assets | Laravel Mix, Webpack |

### Included Plugins

Ecommerce, Blog, Payment (PayPal, Stripe, Razorpay, Paystack, Mollie, SSLCommerz), Language, FAQ, Newsletter, Social Login, Analytics, Ads, and more.

---

## Requirements

- PHP **8.0 – 8.2** with extensions: `curl`, `gd`, `json`, `zip`, `mbstring`, `pdo_mysql`, `openssl`, `tokenizer`, `xml`
- Composer 2.x
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 16+ & npm (for frontend assets)
- Apache with `mod_rewrite` enabled **or** Nginx

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/ecommerce1.git
cd ecommerce1
```

### 2. Install PHP dependencies

```bash
composer install
```

> **Note:** Include dev dependencies when seeding the database locally. `fakerphp/faker` is required for seeders.

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and app settings:

```env
APP_NAME="Ecommerce"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce1
DB_USERNAME=root
DB_PASSWORD=your_password

ADMIN_DIR=admin
```

### 4. Create the database

```sql
CREATE DATABASE ecommerce1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations and seed demo data

```bash
php artisan migrate
php artisan db:seed
```

### 6. Storage link and permissions

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache public/storage
```

If you ran commands as `root`, fix ownership:

```bash
chown -R $USER:$USER storage bootstrap/cache public/storage
```

### 7. Publish CMS assets (optional)

```bash
php artisan cms:publish:assets
```

### 8. Build frontend assets (optional)

```bash
npm install
npm run dev
```

---

## Apache Setup (Project Root — No `/public` in URL)

This project supports running Apache with the **document root at the project root**, so URLs stay clean (e.g. `http://localhost/` instead of `http://localhost/public/`).

### Virtual host example

```apache
<VirtualHost *:80>
    ServerName ecommerce1.local
    DocumentRoot /path/to/ecommerce1

    <Directory /path/to/ecommerce1>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### How it works

| File | Purpose |
|---|---|
| `index.php` (root) | Laravel front controller; sets public path to `public/` |
| `.htaccess` (root) | Serves static assets from `public/` and routes requests to `index.php` |
| `public/index.php` | Still works if Apache document root is set to `public/` |

Enable `mod_rewrite` and restart Apache:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## Nginx Setup (Alternative)

Point the web root to the `public/` directory:

```nginx
server {
    listen 80;
    server_name ecommerce1.local;
    root /path/to/ecommerce1/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Default Login Credentials (After Seeding)

| Role | Email | Username | Password |
|---|---|---|---|
| Super Admin | admin@botble.com | botble | 159357 |
| Admin | user@botble.com | admin | 12345678 |

Admin panel URL: `http://your-domain/admin`

Demo customer (after seeding): `john.smith@botble.com` / `12345678`

---

## Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Re-run all seeders
php artisan db:seed

# Run a single seeder
php artisan db:seed --class=CustomerSeeder

# Create storage symlink
php artisan storage:link
```

---

## Project Structure

```
ecommerce1/
├── app/                  # Laravel application code
├── bootstrap/            # Framework bootstrap
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Demo data seeders
├── index.php             # Root front controller (Apache root deploy)
├── platform/
│   ├── core/             # Botble CMS core
│   ├── packages/         # Botble packages
│   ├── plugins/          # CMS plugins (ecommerce, blog, payment, etc.)
│   └── themes/shopwise/  # Evaly ecommerce theme
├── public/               # Web-accessible assets
├── resources/            # Views and frontend resources
├── routes/               # Route definitions
└── storage/              # Logs, cache, uploads
```

---

## Fixes & Improvements Applied

### 1. Apache root deployment

- Added root `index.php` so the app runs without pointing Apache at the `public/` folder
- Updated root `.htaccess` to serve CSS, JS, and images from `public/` while routing PHP requests to the front controller

### 2. HTTP 500 error on boot

- **Cause:** Missing `.env` file and `APP_KEY`
- **Fix:** Added `.env.example` template and proper Laravel request lifecycle in `index.php` / `public/index.php` (separated `handle()`, `send()`, and `terminate()`)

### 3. Database seeder `fake()` error

- **Cause:** `fakerphp/faker` was not installed, and seeders called the global `fake()` helper which was unavailable
- **Fix:**
  - Installed `fakerphp/faker` as a dev dependency
  - Added `$this->faker()` helper method to `BaseSeeder`
  - Updated `CustomerSeeder`, `BlogSeeder`, `ProductSeeder`, and `PageSeeder` to use `$this->faker()` instead of `fake()`

### 4. Environment template

- Added `.env.example` with all required variables for local setup and the Botble installer

---

## Troubleshooting

### HTTP 500 on first load

1. Confirm `.env` exists and `APP_KEY` is set: `php artisan key:generate`
2. Check database credentials in `.env`
3. Review logs: `storage/logs/laravel.log`

### Seeder fails with `Call to undefined function fake()`

```bash
composer install          # installs fakerphp/faker (dev dependency)
php artisan db:seed
```

### Permission denied on storage or bootstrap/cache

```bash
chmod -R 775 storage bootstrap/cache public/storage
chown -R $USER:$USER storage bootstrap/cache public/storage
```

### CSS/JS not loading (Apache root setup)

- Ensure `mod_rewrite` is enabled
- Confirm `AllowOverride All` is set in your Apache virtual host
- Verify root `.htaccess` and root `index.php` exist

### Database connection refused

- Confirm MySQL is running
- Match `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

Botble CMS and its plugins are subject to their respective licenses.
