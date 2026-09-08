@echo off
title CG News Express - Live Public Share (Cloudflare Tunnel)
echo =======================================================
echo   CG News Express - Free Public Client Review Sharing
echo =======================================================
echo.

cd /d "%~dp0"

echo [1/3] Building production assets...
call npm.cmd run build

echo [2/3] Checking if Laravel server is running...
netstat -ano | findstr :8000 >nul 2>&1
if %errorlevel% neq 0 (
    echo Starting Laravel server on port 8000...
    start /b php artisan serve --port=8000
    timeout /t 3 /nobreak >nul
) else (
    echo Laravel server is already active on port 8000.
)

echo [3/3] Starting Cloudflare Public Tunnel...
echo.
echo ======================================================================
echo Copy the trycloudflare.com URL shown below and send it to your client!
echo Press Ctrl+C in this window when you wish to stop sharing.
echo ======================================================================
echo.

.\cloudflared.exe tunnel --url http://127.0.0.1:8000

pause
