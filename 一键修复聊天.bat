@echo off
chcp 65001 >nul
echo ========================================
echo           聊天功能一键修复工具
echo ========================================
echo.

echo 正在修复聊天功能问题...
echo.

echo [1/4] 运行修复脚本...
php simple_fix_chat.php
echo.

echo [2/4] 清除缓存...
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
echo.

echo [3/4] 重新生成缓存...
php artisan config:cache
php artisan route:cache
echo.

echo [4/4] 检查存储链接...
php artisan storage:link
echo.

echo ========================================
echo           修复完成！
echo ========================================
echo.
echo 请按照以下步骤测试：
echo 1. 刷新浏览器页面
echo 2. 尝试发送聊天消息
echo 3. 检查是否还有错误提示
echo.
echo 如果问题仍然存在，请检查：
echo - Web服务器配置（Apache/Nginx）
echo - PHP配置
echo - 数据库连接
echo.
pause
