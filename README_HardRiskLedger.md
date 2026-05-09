# HardRiskLedger Setup (XAMPP)

## 1) Requirements
- PHP 8.2+ (8.3 recommended for Laravel 13)
- Composer
- Node.js + npm
- MySQL via XAMPP

## 2) Database
Create DB in phpMyAdmin: `hardriskledger`

## 3) .env example
```
APP_NAME=HardRiskLedger
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost/hardriskledger/hardriskledger-app/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hardriskledger
DB_USERNAME=root
DB_PASSWORD=
```

## 4) Install
```
cd C:\xampp\htdocs\hardriskledger\hardriskledger-app
C:\xampp\php\php.exe artisan key:generate
C:\xampp\php\php.exe artisan migrate --seed
npm install
npm run build
```

## 5) Run
```
C:\xampp\php\php.exe artisan serve
```
Open: http://127.0.0.1:8000

Demo login after seeding:
- email: demo@hardriskledger.test
- password: password
