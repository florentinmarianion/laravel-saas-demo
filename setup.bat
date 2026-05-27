@echo off
echo.
echo  Laravel SaaS Admin Panel - Setup
echo  ==================================
echo.

:: Check composer
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] composer not found. Install from https://getcomposer.org
    pause & exit /b 1
)

:: Check npm
where npm >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] npm not found. Install Node.js from https://nodejs.org
    pause & exit /b 1
)

:: Check php
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] php not found. Make sure WAMP/XAMPP is running and PHP is in PATH.
    pause & exit /b 1
)

echo [1/7] Installing PHP dependencies...
call composer install
if %errorlevel% neq 0 ( echo [ERROR] composer install failed & pause & exit /b 1 )

echo [2/7] Creating .env file...
if not exist .env copy .env.example .env

echo [3/7] Generating app key...
php artisan key:generate
if %errorlevel% neq 0 ( echo [ERROR] key:generate failed & pause & exit /b 1 )

echo [4/7] Running migrations...
php artisan migrate
if %errorlevel% neq 0 ( echo [ERROR] migrate failed & pause & exit /b 1 )

echo [5/7] Seeding database...
php artisan db:seed
if %errorlevel% neq 0 ( echo [ERROR] db:seed failed & pause & exit /b 1 )

echo [6/7] Installing and building core frontend...
call npm install
call npm run build
if %errorlevel% neq 0 ( echo [ERROR] core npm build failed & pause & exit /b 1 )

echo [7/7] Installing and building CurrencyExchange module...
cd Modules\CurrencyExchange
call npm install
call npm run build
if %errorlevel% neq 0 ( echo [ERROR] module npm build failed & pause & exit /b 1 )
cd ..\..

echo.
echo  Setup complete!
echo  Admin login: admin@demo.com / password
echo.
pause
