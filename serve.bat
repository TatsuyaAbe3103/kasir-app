@echo off
setlocal enabledelayedexpansion
title Server Nusa POS Kasir
echo ========================================================
echo   MENJALANKAN SERVER NUSA POS (CAFE & RESTO SYSTEM)
echo ========================================================
echo.
echo   * Akses dari Komputer Kasir : http://127.0.0.1:8000
echo   * Akses dari HP Kasir / Tamu: Gunakan IP WiFi komputer ini
echo.
echo   (Jangan tutup jendela ini selama kasir beroperasi)
echo   Untuk berhenti, tekan [Ctrl + C]
echo ========================================================
echo.

if exist "C:\php83\php.exe" (
    "C:\php83\php.exe" "%~dp0artisan" serve --host=0.0.0.0 --port=8000
) else if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" "%~dp0artisan" serve --host=0.0.0.0 --port=8000
) else (
    php "%~dp0artisan" serve --host=0.0.0.0 --port=8000
)
