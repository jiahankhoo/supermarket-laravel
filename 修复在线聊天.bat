@echo off
chcp 65001 >nul
echo ========================================
echo        在线部署聊天功能修复工具
echo ========================================
echo.

echo 正在修复在线部署聊天问题...
echo.

echo [1/5] 运行在线修复脚本...
php fix_online_chat.php
echo.

echo [2/5] 清除所有缓存...
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
echo.

echo [3/5] 重新生成缓存...
php artisan config:cache
php artisan route:cache
echo.

echo [4/5] 检查存储链接...
php artisan storage:link
echo.

echo [5/5] 设置文件权限...
icacls "storage" /grant "Everyone:(OI)(CI)F" /T
icacls "public\storage" /grant "Everyone:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T
echo.

echo ========================================
echo           修复完成！
echo ========================================
echo.
echo 重要提示：
echo 1. 请重启Web服务器 (Apache/Nginx)
echo 2. 清除浏览器缓存和Cookie
echo 3. 重新登录系统
echo 4. 测试聊天功能
echo.
echo 如果问题仍然存在，请检查：
echo - Web服务器是否支持 mod_rewrite
echo - PHP配置 (upload_max_filesize, post_max_size)
echo - 数据库连接是否正常
echo - 文件权限是否正确
echo.
pause
