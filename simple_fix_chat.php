<?php

/**
 * 简单可靠的聊天功能修复脚本
 */

echo "=== 聊天功能修复脚本 ===\n\n";

// 1. 清除缓存
echo "1. 清除缓存...\n";
$commands = [
    'config:clear',
    'route:clear', 
    'cache:clear',
    'view:clear'
];

foreach ($commands as $command) {
    $output = shell_exec("php artisan $command 2>&1");
    if (strpos($output, 'error') === false) {
        echo "   ✓ $command 成功\n";
    } else {
        echo "   ✗ $command 失败: $output\n";
    }
}

// 2. 检查存储链接
echo "\n2. 检查存储链接...\n";
$storageLink = __DIR__ . '/public/storage';
if (!is_link($storageLink) && !is_dir($storageLink)) {
    $output = shell_exec("php artisan storage:link 2>&1");
    if (strpos($output, 'error') === false) {
        echo "   ✓ 存储链接创建成功\n";
    } else {
        echo "   ✗ 存储链接创建失败: $output\n";
    }
} else {
    echo "   ✓ 存储链接已存在\n";
}

// 3. 检查聊天文件目录
echo "\n3. 检查聊天文件目录...\n";
$chatDirs = [
    __DIR__ . '/storage/app/public/chat_files',
    __DIR__ . '/public/storage/chat_files'
];

foreach ($chatDirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "   ✓ 创建目录: $dir\n";
        } else {
            echo "   ✗ 创建目录失败: $dir\n";
        }
    } else {
        echo "   ✓ 目录存在: $dir\n";
    }
}

// 4. 设置文件权限
echo "\n4. 设置文件权限...\n";
$paths = [
    __DIR__ . '/storage',
    __DIR__ . '/storage/app',
    __DIR__ . '/storage/app/public',
    __DIR__ . '/storage/app/public/chat_files',
    __DIR__ . '/public/storage',
    __DIR__ . '/public/storage/chat_files',
    __DIR__ . '/bootstrap/cache'
];

foreach ($paths as $path) {
    if (is_dir($path)) {
        if (chmod($path, 0755)) {
            echo "   ✓ 设置权限 755: $path\n";
        } else {
            echo "   ✗ 设置权限失败: $path\n";
        }
    }
}

// 5. 检查路由文件
echo "\n5. 检查路由配置...\n";
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    $requiredRoutes = [
        'chat.index',
        'chat.show', 
        'chat.send',
        'chat.messages',
        'chat.unread-count'
    ];
    
    $missingRoutes = [];
    foreach ($requiredRoutes as $route) {
        if (strpos($content, $route) === false) {
            $missingRoutes[] = $route;
        }
    }
    
    if (empty($missingRoutes)) {
        echo "   ✓ 所有聊天路由都已配置\n";
    } else {
        echo "   ✗ 缺少路由: " . implode(', ', $missingRoutes) . "\n";
    }
} else {
    echo "   ✗ 路由文件不存在\n";
}

// 6. 检查控制器
echo "\n6. 检查控制器...\n";
$controllerFile = __DIR__ . '/app/Http/Controllers/ChatController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    $requiredMethods = [
        'index',
        'show',
        'send', 
        'getMessages',
        'getUnreadCount'
    ];
    
    $missingMethods = [];
    foreach ($requiredMethods as $method) {
        if (strpos($content, "function $method") === false) {
            $missingMethods[] = $method;
        }
    }
    
    if (empty($missingMethods)) {
        echo "   ✓ 所有控制器方法都存在\n";
    } else {
        echo "   ✗ 缺少方法: " . implode(', ', $missingMethods) . "\n";
    }
} else {
    echo "   ✗ 控制器文件不存在\n";
}

// 7. 检查数据库表
echo "\n7. 检查数据库表...\n";
try {
    $output = shell_exec("php artisan migrate:status 2>&1");
    if (strpos($output, 'chat_messages') !== false) {
        echo "   ✓ chat_messages 表存在\n";
    } else {
        echo "   ✗ chat_messages 表不存在，运行迁移...\n";
        $migrateOutput = shell_exec("php artisan migrate 2>&1");
        echo "   迁移结果: $migrateOutput\n";
    }
} catch (Exception $e) {
    echo "   ✗ 检查数据库失败: " . $e->getMessage() . "\n";
}

// 8. 重新生成缓存
echo "\n8. 重新生成缓存...\n";
$cacheCommands = [
    'config:cache',
    'route:cache'
];

foreach ($cacheCommands as $command) {
    $output = shell_exec("php artisan $command 2>&1");
    if (strpos($output, 'error') === false) {
        echo "   ✓ $command 成功\n";
    } else {
        echo "   ✗ $command 失败: $output\n";
    }
}

// 9. 检查 .htaccess 文件
echo "\n9. 检查 .htaccess 文件...\n";
$htaccessFile = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessFile)) {
    $content = file_get_contents($htaccessFile);
    if (strpos($content, 'RewriteEngine On') !== false) {
        echo "   ✓ .htaccess 配置正确\n";
    } else {
        echo "   ✗ .htaccess 缺少 RewriteEngine 配置\n";
    }
} else {
    echo "   ✗ .htaccess 文件不存在\n";
    
    // 创建 .htaccess 文件
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
    
    if (file_put_contents($htaccessFile, $htaccessContent)) {
        echo "   ✓ 已创建 .htaccess 文件\n";
    } else {
        echo "   ✗ 创建 .htaccess 文件失败\n";
    }
}

echo "\n=== 修复完成 ===\n";
echo "请刷新浏览器页面测试聊天功能。\n";
echo "如果问题仍然存在，请检查：\n";
echo "1. Web服务器是否支持 URL 重写\n";
echo "2. PHP 配置是否正确\n";
echo "3. 数据库连接是否正常\n";
