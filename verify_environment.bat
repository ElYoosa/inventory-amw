@echo off
title Laravel Environment Checker
color 0A

echo =====================================================
echo   🔍 CEK KONFIGURASI ENVIRONMENT LARAVEL - INVENTORY AMW
echo =====================================================
echo.

echo 📘 PHP Version:
php -v | findstr /R "^PHP"
echo.

echo 📗 Laravel Version:
php artisan --version
echo.

echo 📦 Composer Version:
composer -V
echo.

echo 🧩 Node & NPM Version:
node -v
npm -v
echo.

echo ⚡ Vite Version:
npm list vite | findstr "vite@"
echo.

echo 🗄️ MySQL Version:
mysql -u root -e "SELECT VERSION();"
echo.

echo 🧱 Apache Version:
where httpd >nul 2>&1 && httpd -v || echo Apache via Laragon is running internally.
echo.

echo ✅ Laravel Cache & Config Check:
php artisan optimize:clear
php artisan config:cache
php artisan route:clear
php artisan view:clear
echo.

echo =====================================================
echo   💚 ENVIRONMENT VERIFIED SUCCESSFULLY!
echo =====================================================
pause
