# HardRiskLedger

HardRiskLedger adalah aplikasi jurnal trading + risk management berbasis Laravel untuk scalper/prop trader.

## Menjalankan Lokal (XAMPP)

1. `composer install`
2. `copy .env.example .env`
3. Atur DB di `.env` (MySQL XAMPP)
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan storage:link`
7. `npm install`
8. `npm run dev`
9. `php artisan serve`

## Export Data (CSV)

- Trade Journal: tombol `Export CSV` di halaman `Trade Journal`
- Risk Ledger: tombol `Export CSV` di halaman `Risk Ledger`
- Statistics: tombol `Export CSV` di halaman `Statistics`

Export mengikuti filter aktif (akun + periode + tanggal).

## Test Otomatis

Jalankan:

```bash
php artisan test
```

Test utama yang ditambahkan:
- CRUD trade (create/update/delete)
- Filter akun + export trade
- Risk ledger warning/lock state
- Export statistics

## Hardening Deploy (Production)

Pastikan nilai `.env`:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
```

Lalu jalankan:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Tambahan keamanan yang sudah aktif:
- Security headers middleware (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`)
- HSTS otomatis saat `APP_ENV=production`
- Validasi upload screenshot diperketat (tipe, ukuran, dimensi)
- Validasi ownership akun pada input sensitif

