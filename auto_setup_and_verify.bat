@echo off
title 🚀 Laravel Auto Setup & Verify - Inventory AMW
color 0A

echo =====================================================
echo    🧩 LARAVEL AUTO SETUP & VERIFY - INVENTORY AMW
echo =====================================================
echo.

:: Cek PHP
echo 🔍 Checking PHP version...
php -v | findstr /R "^PHP"
if errorlevel 1 (
    echo ❌ PHP tidak ditemukan! Pastikan Laragon aktif.
    pause
    exit /b
)
echo.

:: Cek Composer
echo 🔍 Checking Composer version...
composer -V >nul 2>&1
if errorlevel 1 (
    echo ❌ Composer belum terinstall atau tidak ada di PATH.
    pause
    exit /b
)
composer -V
echo.

:: Cek Node & NPM
echo 🔍 Checking Node & NPM...
node -v >nul 2>&1
if errorlevel 1 (
    echo ❌ Node.js belum terinstall! Silakan install dari nodejs.org.
    pause
    exit /b
)
npm -v
echo.

:: Cek Laravel
echo 🔍 Checking Laravel...
php artisan --version
if errorlevel 1 (
    echo ❌ Laravel belum terinstall dengan benar.
    pause
    exit /b
)
echo.

:: Cek .env file
echo 🔍 Checking .env file...
if not exist ".env" (
    echo ⚠️  File .env tidak ditemukan, membuat dari .env.example...
    copy .env.example .env >nul
    echo ✅ File .env berhasil dibuat dari template.
)
echo.

:: Jalankan composer install/update jika vendor belum ada
if not exist "vendor" (
    echo 🧩 Menjalankan composer install...
    composer install
) else (
    echo 🧩 Folder vendor sudah ada, memverifikasi dependencies...
    composer update --lock
)
echo.

:: Jalankan npm install jika node_modules belum ada
if not exist "node_modules" (
    echo ⚙️  Menjalankan npm install (harap tunggu)...
    npm install
) else (
    echo ⚙️  Dependencies Node sudah terpasang.
)
echo.

:: Baca APP_ENV dari .env
for /f "tokens=2 delims==" %%A in ('findstr "APP_ENV=" ".env"') do set APP_ENV=%%A
set APP_ENV=%APP_ENV:"=%

echo 🌍 Environment terdeteksi: %APP_ENV%
echo.

:: Jalankan npm run build jika production, atau npm run dev --open jika lokal
if /I "%APP_ENV%"=="production" (
    echo 🚀 Mode production terdeteksi, membangun aset dengan Vite...
    npm run build
    echo ✅ Build selesai, file tersedia di folder public/build
) else (
    echo 🧑‍💻 Mode development, menyiapkan server Vite lokal...
    echo (Lewati build otomatis untuk development)
)
echo.

:: Cek koneksi database
echo 🧭 Mengecek koneksi database...
php artisan migrate:status >nul 2>&1
if errorlevel 1 (
    echo ⚠️ Database belum tersedia atau belum dikonfigurasi.
    echo 👉 Pastikan nama DB di .env sudah benar lalu buat manual di phpMyAdmin.
) else (
    echo ✅ Database terhubung dengan baik.
)
echo.

:: Bersihkan dan cache konfigurasi Laravel
echo 🧹 Membersihkan cache konfigurasi Laravel...
php artisan optimize:clear
php artisan config:cache
php artisan route:clear
php artisan view:clear
echo ✅ Cache berhasil direset.
echo.

echo =====================================================
echo   ✅ SELURUH PROSES AUTO SETUP & VERIFIKASI SELESAI
echo =====================================================
echo.
echo 💡 Kamu bisa menjalankan server sekarang dengan:
echo     php artisan serve
echo     atau menggunakan menu Laragon > www > inventory-amw
echo.
pause
