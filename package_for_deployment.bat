@echo off
chcp 65001 >nul
echo 🚀 Laravel 超市系统部署打包工具
echo ===================================
echo.

echo 📦 正在打包项目文件...
echo.

REM 创建临时目录
if exist "deploy_temp" rmdir /s /q "deploy_temp"
mkdir "deploy_temp"

REM 复制项目文件（排除不需要的文件）
echo 正在复制项目文件...
xcopy /E /I /Y /EXCLUDE:deploy_exclude.txt . "deploy_temp\"

REM 创建排除文件列表
echo 创建排除文件列表...
(
echo node_modules\
echo vendor\
echo .git\
echo .gitignore
echo .env
echo storage\logs\*
echo storage\framework\cache\*
echo storage\framework\sessions\*
echo storage\framework\views\*
echo bootstrap\cache\*
echo .vscode\
echo .idea\
echo *.log
echo *.tmp
echo *.cache
echo Thumbs.db
echo .DS_Store
) > deploy_exclude.txt

REM 创建压缩包
echo 正在创建压缩包...
powershell -command "Compress-Archive -Path 'deploy_temp\*' -DestinationPath 'supermarket-laravel-deploy.zip' -Force"

REM 清理临时文件
echo 清理临时文件...
rmdir /s /q "deploy_temp"
del "deploy_exclude.txt"

echo.
echo ✅ 打包完成！
echo.
echo 📋 部署文件: supermarket-laravel-deploy.zip
echo 📁 文件大小: 
powershell -command "$size = (Get-Item 'supermarket-laravel-deploy.zip').Length; if ($size -gt 1GB) { Write-Host ('{0:N2} GB' -f ($size/1GB)) } elseif ($size -gt 1MB) { Write-Host ('{0:N2} MB' -f ($size/1MB)) } else { Write-Host ('{0:N2} KB' -f ($size/1KB)) }"
echo.
echo 📤 上传步骤:
echo    1. 将 supermarket-laravel-deploy.zip 上传到服务器
echo    2. 在服务器上解压: unzip supermarket-laravel-deploy.zip
echo    3. 运行部署脚本: ./deploy.sh
echo.
echo 🔗 相关文件:
echo    - deploy_to_server.md (详细部署指南)
echo    - deploy.sh (自动部署脚本)
echo.
pause 