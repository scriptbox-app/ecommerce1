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

## Production Deployment (Nginx / Shared Hosting)

Your server at **scriptbox.app** uses **nginx**, which ignores `.htaccess`. If the document root is the **project root** (not `public/`), CSS/JS/images return **404** because files live under `public/` but URLs point to `/themes/...`, `/vendor/...`, etc.

### Automatic fix (included)

`AppServiceProvider` detects when the document root equals the project root and automatically prefixes asset URLs with `/public`:

- `/themes/shopwise/css/style.css` → `/public/themes/shopwise/css/style.css`
- `/storage/general/logo.png` → `/public/storage/general/logo.png`

After uploading the fix, run on the server:

```bash
php artisan config:clear
php artisan cache:clear
php artisan storage:link
php artisan cms:publish:assets
```

### Production `.env` settings

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ecommerce1.scriptbox.app
```

> Set `APP_DEBUG=false` on production to avoid Laravel Debugbar 404 errors.

### Recommended server setup

Point the **document root to `public/`** (best practice). See `nginx.conf.example` in the project root.

If you cannot change the document root, the automatic `/public` prefix handles CSS, JS, and uploaded images.

### Only homepage works — all other pages show 404

**Symptom:** `/` loads fine, but `/admin`, `/products`, `/cart`, etc. return nginx 404.

**Cause:** nginx only runs `index.php` for the homepage. It does not route other URLs to Laravel (`.htaccess` is ignored on nginx).

**Quick test:** If `https://your-domain.com/index.php/admin` works but `https://your-domain.com/admin` does not, this is the issue.

**Fix (choose one):**

**Option A — Recommended (aaPanel / BT Panel):** Change site directory to `public/`:

1. aaPanel → **Website** → your site → **Site Directory**
2. Set to: `/www/wwwroot/ecommerce1.scriptbox.app/public`
3. **Pseudo-static** → select **laravel** (or paste rewrite from `scriptbox.nginx.conf`)
4. Remove `ASSET_URL` and `STORAGE_URL` from `.env`
5. Run `php artisan config:clear`

**Option B — Keep project root as document root:** Edit nginx config and add:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Full rules (storage + assets + routes) are in **`scriptbox.nginx.conf`**.

After saving, reload nginx and test `/admin` and `/products`.

### Deploy checklist

1. Upload all files including `public/vendor/`, `public/themes/`, and `vendor/`
2. Run `composer install --no-dev --optimize-autoloader` on the server
3. Copy `.env`, set `APP_URL`, database credentials, `APP_DEBUG=false`
4. Run `php artisan key:generate` (if needed)
5. Run `php artisan migrate --force`
6. Run `php artisan storage:link`
7. Run `php artisan cms:publish:assets`
8. Run `php artisan config:clear && php artisan cache:clear`
9. Set permissions: `chmod -R 775 storage bootstrap/cache`

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

### 5. Production CSS/JS 404 on nginx (scriptbox.app)

- **Cause:** nginx ignores `.htaccess`; document root is project root, so `/themes/...` and `/vendor/...` are not found (files are under `public/`)
- **Fix:** Auto-detect root document root in `AppServiceProvider` and prefix `ASSET_URL` and storage URLs with `/public`

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

### CSS/JS 404 on nginx / shared hosting (production)

- **Cause:** nginx does not read `.htaccess`; static files are in `public/` but URLs omit `/public`
- **Fix:** Deploy latest code (includes auto `/public` prefix), then run:
  ```bash
  php artisan config:clear
  php artisan storage:link
  php artisan cms:publish:assets
  ```
- Add to `.env` on the server:
  ```env
  ASSET_URL=https://ecommerce1.scriptbox.app/public
  STORAGE_URL=https://ecommerce1.scriptbox.app/public/storage
  ```
- **Required nginx rules** (aaPanel / BT Panel): paste contents of `scriptbox.nginx.conf` into your site nginx config. This fixes:
  - `/storage/*` image 404s
  - `/ajax/*` route 404s
  - `/admin` and other Laravel routes
- Confirm `public/vendor/`, `public/themes/`, and `public/storage/` exist on the server
- Set `APP_DEBUG=false` in production `.env`
- **Best fix:** change document root to `public/` (see `nginx.conf.example`)

> `ERR_BLOCKED_BY_CLIENT` on cookie-consent.js is your browser ad-blocker — not a server error.

### Database connection refused

- Confirm MySQL is running
- Match `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

Botble CMS and its plugins are subject to their respective licenses.
