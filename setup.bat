@echo off
echo 正在设置 Laravel 超市系统...

echo 1. 复制环境配置文件...
if not exist .env (
    copy .env.example .env
    echo 环境配置文件已创建
) else (
    echo 环境配置文件已存在
)

echo 2. 生成应用密钥...
php artisan key:generate

echo 3. 创建SQLite数据库文件...
if not exist database\database.sqlite (
    echo. > database\database.sqlite
    echo SQLite数据库文件已创建
) else (
    echo SQLite数据库文件已存在
)

echo 4. 运行数据库迁移...
php artisan migrate

echo 5. 运行数据库种子...
php artisan db:seed

echo.
echo ========================================
echo 设置完成！
echo ========================================
echo.
echo 默认管理员账户：
echo 邮箱: admin@supermarket.com
echo 密码: admin123
echo.
echo 请及时修改默认密码！
echo.
echo 启动服务器：
echo 方式1: 双击 start_server.bat
echo 方式2: 运行 php artisan serve --host=0.0.0.0 --port=8000
echo.
echo 访问地址：
echo http://localhost:8000
echo http://127.0.0.1:8000
echo ========================================
pause 