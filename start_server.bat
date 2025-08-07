@echo off
echo ========================================
echo Laravel 超市系统启动脚本
echo ========================================
echo.

echo 正在启动服务器...
echo.
echo 您可以通过以下地址访问系统：
echo.
echo 方式1: http://localhost:8000
echo 方式2: http://127.0.0.1:8000
echo.
echo 按 Ctrl+C 停止服务器
echo ========================================
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause 