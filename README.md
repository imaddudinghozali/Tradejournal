# HardRiskLedger

Trading journal + risk management ledger berbasis Laravel untuk trader XAU/USD dan prop firm challenge.

Tag rilis saat ini: `v1.0.0`

## Fitur

- Authentication (Laravel Breeze)
- Dashboard metrik trading (filter akun + periode)
- Trading Account Management
- Trade Journal CRUD + upload screenshot
- Risk Ledger (drawdown usage, status safe/warning/danger, violation log)
- Prop Firm Challenge Tracker
- AMDX Journal
- Statistics (Chart.js) + export CSV
- Settings risk

## Stack

- Laravel 12
- MySQL
- Blade + Tailwind CSS
- Chart.js

## Setup Lokal (XAMPP)

1. Clone repo lalu masuk folder project.
2. Install dependency:
```bash
composer install
npm install
```
3. Salin env:
```bash
copy .env.example .env
```
4. Atur database di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hardriskledger
DB_USERNAME=root
DB_PASSWORD=
```
5. Generate key, migrate, seeding, dan link storage:
```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```
6. Jalankan aplikasi:
```bash
npm run dev
php artisan serve
```

## Export CSV

- `Trade Journal` -> `Export CSV`
- `Risk Ledger` -> `Export CSV`
- `Statistics` -> `Export CSV`

Semua export mengikuti filter aktif (akun/periode/tanggal).

## Testing

```bash
php artisan test
```

## Production Checklist

Set minimum berikut di `.env`:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
```

Cache config untuk performa:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Security

- Security headers middleware aktif
- HSTS aktif saat environment production
- Validasi upload screenshot diperketat (mime/size/dimensi)
- Validasi ownership akun pada endpoint sensitif
