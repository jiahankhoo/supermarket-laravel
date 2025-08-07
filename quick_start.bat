@echo off
echo ========================================
echo Laravel 超市系统 - 快速启动
echo ========================================
echo.

echo 正在检查系统设置...
echo.

REM 检查.env文件
if not exist .env (
    echo 创建环境配置文件...
    copy .env.example .env
    php artisan key:generate
)

REM 检查数据库
if not exist database\database.sqlite (
    echo 创建数据库文件...
    echo. > database\database.sqlite
    php artisan migrate
    php artisan db:seed
    echo 数据库已初始化！
    echo.
    echo 默认管理员账户：
    echo 邮箱: admin@supermarket.com
    echo 密码: admin123
    echo.
)

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