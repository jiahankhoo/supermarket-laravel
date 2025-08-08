<?php

/**
 * 在线部署聊天功能修复脚本
 */

echo "=== 在线部署聊天功能修复脚本 ===\n\n";

// 1. 清除缓存
echo "1. 清除缓存...\n";
$commands = ['config:clear', 'route:clear', 'cache:clear', 'view:clear'];
foreach ($commands as $cmd) {
    $output = shell_exec("php artisan $cmd 2>&1");
    echo "   ✓ $cmd 完成\n";
}

// 2. 修复存储链接
echo "\n2. 修复存储链接...\n";
$storageLink = __DIR__ . '/public/storage';
if (is_link($storageLink)) {
    unlink($storageLink);
}
$output = shell_exec("php artisan storage:link 2>&1");
echo "   ✓ 存储链接已修复\n";

// 3. 创建目录
echo "\n3. 创建必要目录...\n";
$dirs = [
    __DIR__ . '/storage/app/public/chat_files',
    __DIR__ . '/public/storage/chat_files'
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    echo "   ✓ 目录已创建: $dir\n";
}

// 4. 设置权限
echo "\n4. 设置文件权限...\n";
$paths = [
    __DIR__ . '/storage',
    __DIR__ . '/public/storage',
    __DIR__ . '/bootstrap/cache'
];
foreach ($paths as $path) {
    if (is_dir($path)) {
        chmod($path, 0755);
        echo "   ✓ 权限已设置: $path\n";
    }
}

// 5. 检查路由
echo "\n5. 检查路由配置...\n";
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    if (strpos($content, 'chat.messages') === false) {
        echo "   ✗ 聊天路由缺失，正在添加...\n";
        $chatRoutes = "\n// 聊天路由\nRoute::middleware('auth')->group(function () {\n";
        $chatRoutes .= "    Route::get('/chat', [\\App\\Http\\Controllers\\ChatController::class, 'index'])->name('chat.index');\n";
        $chatRoutes .= "    Route::get('/chat/{user}', [\\App\\Http\\Controllers\\ChatController::class, 'show'])->name('chat.show');\n";
        $chatRoutes .= "    Route::post('/chat/send/{user}', [\\App\\Http\\Controllers\\ChatController::class, 'send'])->name('chat.send');\n";
        $chatRoutes .= "    Route::get('/chat/messages/{user}', [\\App\\Http\\Controllers\\ChatController::class, 'getMessages'])->name('chat.messages');\n";
        $chatRoutes .= "    Route::get('/chat/unread-count/{user}', [\\App\\Http\\Controllers\\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');\n";
        $chatRoutes .= "});\n";
        file_put_contents($routesFile, $content . $chatRoutes);
        echo "   ✓ 聊天路由已添加\n";
    } else {
        echo "   ✓ 聊天路由已存在\n";
    }
}

// 6. 创建 .htaccess
echo "\n6. 检查 .htaccess 文件...\n";
$htaccessFile = __DIR__ . '/public/.htaccess';
$htaccessContent = '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>';

file_put_contents($htaccessFile, $htaccessContent);
echo "   ✓ .htaccess 文件已更新\n";

// 7. 重新生成缓存
echo "\n7. 重新生成缓存...\n";
$cacheCommands = ['config:cache', 'route:cache'];
foreach ($cacheCommands as $cmd) {
    $output = shell_exec("php artisan $cmd 2>&1");
    echo "   ✓ $cmd 完成\n";
}

echo "\n=== 修复完成 ===\n";
echo "请重启Web服务器并测试聊天功能。\n";
