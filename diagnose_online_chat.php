<?php

/**
 * 在线部署聊天问题诊断脚本
 */

echo "=== 在线部署聊天问题诊断 ===\n\n";

// 1. 检查Laravel环境
echo "1. 检查Laravel环境...\n";
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✗ vendor目录不存在\n";
    exit(1);
}

if (!file_exists(__DIR__ . '/bootstrap/app.php')) {
    echo "   ✗ bootstrap目录不存在\n";
    exit(1);
}

echo "   ✓ Laravel环境正常\n";

// 2. 检查路由列表
echo "\n2. 检查路由列表...\n";
$output = shell_exec("php artisan route:list --name=chat 2>&1");
if (strpos($output, 'chat.messages') !== false) {
    echo "   ✓ 聊天路由已注册\n";
    echo "   路由列表:\n";
    echo $output;
} else {
    echo "   ✗ 聊天路由未找到\n";
    echo "   完整路由列表:\n";
    $allRoutes = shell_exec("php artisan route:list 2>&1");
    echo $allRoutes;
}

// 3. 检查控制器方法
echo "\n3. 检查控制器方法...\n";
$controllerFile = __DIR__ . '/app/Http/Controllers/ChatController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    $methods = ['index', 'show', 'send', 'getMessages', 'getUnreadCount'];
    
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "   ✓ 方法存在: $method\n";
        } else {
            echo "   ✗ 方法缺失: $method\n";
        }
    }
} else {
    echo "   ✗ 控制器文件不存在\n";
}

// 4. 检查数据库连接
echo "\n4. 检查数据库连接...\n";
try {
    $output = shell_exec("php artisan migrate:status 2>&1");
    if (strpos($output, 'chat_messages') !== false) {
        echo "   ✓ chat_messages 表存在\n";
    } else {
        echo "   ✗ chat_messages 表不存在\n";
        echo "   运行迁移...\n";
        $migrateOutput = shell_exec("php artisan migrate --force 2>&1");
        echo $migrateOutput;
    }
} catch (Exception $e) {
    echo "   ✗ 数据库连接失败: " . $e->getMessage() . "\n";
}

// 5. 检查存储链接
echo "\n5. 检查存储链接...\n";
$storageLink = __DIR__ . '/public/storage';
if (is_link($storageLink)) {
    $target = readlink($storageLink);
    echo "   ✓ 存储链接存在，指向: $target\n";
} else {
    echo "   ✗ 存储链接不存在\n";
}

// 6. 检查文件权限
echo "\n6. 检查文件权限...\n";
$paths = [
    __DIR__ . '/storage' => 'storage',
    __DIR__ . '/public/storage' => 'public/storage',
    __DIR__ . '/bootstrap/cache' => 'bootstrap/cache'
];

foreach ($paths as $path => $name) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "   - $name: $perms\n";
        
        if ($perms < '0755') {
            echo "   ✗ 权限过低: $name\n";
        }
    } else {
        echo "   ✗ 目录不存在: $name\n";
    }
}

// 7. 检查 .htaccess 文件
echo "\n7. 检查 .htaccess 文件...\n";
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
}

// 8. 检查PHP配置
echo "\n8. 检查PHP配置...\n";
$configs = [
    'upload_max_filesize' => '512M',
    'post_max_size' => '512M',
    'max_execution_time' => '300',
    'memory_limit' => '512M'
];

foreach ($configs as $config => $recommended) {
    $current = ini_get($config);
    echo "   - $config: $current (推荐: $recommended)\n";
}

// 9. 测试路由响应
echo "\n9. 测试路由响应...\n";
try {
    // 模拟请求
    $request = \Illuminate\Http\Request::create('/chat/messages/1', 'GET');
    $request->setUserResolver(function () {
        return \App\Models\User::first();
    });
    
    $response = app()->handle($request);
    echo "   响应状态码: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "   ✓ 路由响应正常\n";
    } else {
        echo "   ✗ 路由响应异常\n";
    }
} catch (Exception $e) {
    echo "   ✗ 测试路由失败: " . $e->getMessage() . "\n";
}

// 10. 检查环境变量
echo "\n10. 检查环境变量...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $requiredVars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION'];
    
    foreach ($requiredVars as $var) {
        if (strpos($envContent, $var . '=') !== false) {
            echo "   ✓ $var 已配置\n";
        } else {
            echo "   ✗ $var 未配置\n";
        }
    }
} else {
    echo "   ✗ .env 文件不存在\n";
}

echo "\n=== 诊断完成 ===\n";
echo "请根据上述检查结果修复相应问题。\n";
