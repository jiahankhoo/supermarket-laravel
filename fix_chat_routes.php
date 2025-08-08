<?php

/**
 * 修复聊天路由问题的脚本
 * 解决 /chat/messages/{user} 返回 404 的问题
 */

require_once __DIR__ . '/vendor/autoload.php';

// 加载Laravel应用
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 修复聊天路由问题 ===\n\n";

// 1. 清除所有缓存
echo "1. 清除缓存...\n";
try {
    Artisan::call('config:clear');
    echo "   ✓ 配置缓存已清除\n";
    
    Artisan::call('route:clear');
    echo "   ✓ 路由缓存已清除\n";
    
    Artisan::call('cache:clear');
    echo "   ✓ 应用缓存已清除\n";
    
    Artisan::call('view:clear');
    echo "   ✓ 视图缓存已清除\n";
} catch (Exception $e) {
    echo "   ✗ 清除缓存失败: " . $e->getMessage() . "\n";
}

// 2. 检查路由是否正确注册
echo "\n2. 检查聊天路由...\n";
try {
    $routes = Route::getRoutes();
    $chatRoutes = [];
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'chat') !== false) {
            $chatRoutes[] = [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName()
            ];
        }
    }
    
    echo "   找到 " . count($chatRoutes) . " 个聊天相关路由:\n";
    foreach ($chatRoutes as $route) {
        echo "   - " . implode('|', $route['methods']) . " " . $route['uri'] . " (" . $route['name'] . ")\n";
    }
    
    // 检查关键路由是否存在
    $requiredRoutes = [
        'chat.index' => 'GET /chat',
        'chat.show' => 'GET /chat/{user}',
        'chat.send' => 'POST /chat/send/{user}',
        'chat.messages' => 'GET /chat/messages/{user}',
        'chat.unread-count' => 'GET /chat/unread-count/{user}'
    ];
    
    foreach ($requiredRoutes as $name => $expected) {
        $route = Route::getRoutes()->getByName($name);
        if ($route) {
            echo "   ✓ 路由存在: $name\n";
        } else {
            echo "   ✗ 路由缺失: $name\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ✗ 检查路由失败: " . $e->getMessage() . "\n";
}

// 3. 重新注册路由
echo "\n3. 重新注册路由...\n";
try {
    // 手动注册聊天路由
    Route::middleware('auth')->group(function () {
        Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{user}', [\App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/send/{user}', [\App\Http\Controllers\ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/messages/{user}', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::get('/chat/unread-count/{user}', [\App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    });
    
    echo "   ✓ 聊天路由已重新注册\n";
} catch (Exception $e) {
    echo "   ✗ 重新注册路由失败: " . $e->getMessage() . "\n";
}

// 4. 检查控制器方法是否存在
echo "\n4. 检查控制器方法...\n";
try {
    $controller = new \App\Http\Controllers\ChatController();
    $methods = ['index', 'show', 'send', 'getMessages', 'getUnreadCount'];
    
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "   ✓ 方法存在: $method\n";
        } else {
            echo "   ✗ 方法缺失: $method\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ 检查控制器失败: " . $e->getMessage() . "\n";
}

// 5. 测试路由响应
echo "\n5. 测试路由响应...\n";
try {
    // 获取第一个用户作为测试
    $user = \App\Models\User::first();
    if ($user) {
        echo "   使用用户 ID: " . $user->id . " 进行测试\n";
        
        // 模拟请求
        $request = \Illuminate\Http\Request::create("/chat/messages/{$user->id}", 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        $response = app()->handle($request);
        echo "   响应状态码: " . $response->getStatusCode() . "\n";
        
        if ($response->getStatusCode() === 200) {
            echo "   ✓ 路由响应正常\n";
        } else {
            echo "   ✗ 路由响应异常\n";
        }
    } else {
        echo "   ✗ 没有找到用户进行测试\n";
    }
} catch (Exception $e) {
    echo "   ✗ 测试路由失败: " . $e->getMessage() . "\n";
}

// 6. 检查 .htaccess 文件
echo "\n6. 检查 .htaccess 文件...\n";
$htaccessPath = public_path('.htaccess');
if (file_exists($htaccessPath)) {
    echo "   ✓ .htaccess 文件存在\n";
    $content = file_get_contents($htaccessPath);
    if (strpos($content, 'RewriteEngine On') !== false) {
        echo "   ✓ RewriteEngine 已启用\n";
    } else {
        echo "   ✗ RewriteEngine 未启用\n";
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
    
    file_put_contents($htaccessPath, $htaccessContent);
    echo "   ✓ 已创建 .htaccess 文件\n";
}

// 7. 检查文件权限
echo "\n7. 检查文件权限...\n";
$paths = [
    storage_path(),
    storage_path('framework'),
    storage_path('framework/cache'),
    storage_path('framework/views'),
    storage_path('framework/sessions'),
    __DIR__ . '/bootstrap/cache'
];

foreach ($paths as $path) {
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "   - $path: $perms\n";
        
        if ($perms < '0755') {
            chmod($path, 0755);
            echo "   ✓ 已修复权限: $path\n";
        }
    }
}

// 8. 重新生成配置缓存
echo "\n8. 重新生成配置...\n";
try {
    Artisan::call('config:cache');
    echo "   ✓ 配置缓存已重新生成\n";
    
    Artisan::call('route:cache');
    echo "   ✓ 路由缓存已重新生成\n";
} catch (Exception $e) {
    echo "   ✗ 重新生成缓存失败: " . $e->getMessage() . "\n";
}

echo "\n=== 修复完成 ===\n";
echo "请重启Web服务器以确保所有更改生效。\n";
echo "如果问题仍然存在，请检查：\n";
echo "1. Web服务器配置（Apache/Nginx）\n";
echo "2. PHP配置\n";
echo "3. 数据库连接\n";
