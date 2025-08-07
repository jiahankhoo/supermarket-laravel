Write-Host "========================================" -ForegroundColor Green
Write-Host "Laravel 超市系统启动脚本" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "正在启动服务器..." -ForegroundColor Yellow
Write-Host ""
Write-Host "您可以通过以下地址访问系统：" -ForegroundColor Cyan
Write-Host ""
Write-Host "方式1: http://localhost:8000" -ForegroundColor White
Write-Host "方式2: http://127.0.0.1:8000" -ForegroundColor White
Write-Host ""
Write-Host "按 Ctrl+C 停止服务器" -ForegroundColor Red
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

php artisan serve --host=0.0.0.0 --port=8000 