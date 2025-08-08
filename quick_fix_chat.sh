#!/bin/bash

echo "正在修复聊天功能问题..."
echo

echo "1. 运行聊天修复脚本..."
php simple_fix_chat.php
echo

echo "2. 清除缓存..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
echo

echo "3. 重新生成缓存..."
php artisan config:cache
php artisan route:cache
echo

echo "4. 检查存储链接..."
php artisan storage:link
echo

echo "5. 设置文件权限..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
echo

echo "修复完成！"
echo "请刷新浏览器页面测试聊天功能。"
