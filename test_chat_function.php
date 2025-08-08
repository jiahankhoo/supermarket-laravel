<?php

/**
 * 聊天功能测试脚本
 * 用于验证在线部署后聊天功能是否正常工作
 */

require_once __DIR__ . '/vendor/autoload.php';

// 加载Laravel应用
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 聊天功能测试 ===\n\n";

// 1. 测试数据库连接
echo "1. 测试数据库连接...\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "   ✓ 数据库连接成功\n";
} catch (Exception $e) {
    echo "   ✗ 数据库连接失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. 检查chat_messages表
echo "\n2. 检查chat_messages表...\n";
try {
    $tableExists = Schema::hasTable('chat_messages');
    if ($tableExists) {
        echo "   ✓ chat_messages表存在\n";
        
        // 检查表结构
        $columns = Schema::getColumnListing('chat_messages');
        $requiredColumns = ['id', 'sender_id', 'receiver_id', 'message', 'created_at'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "   ✓ 表结构完整\n";
        } else {
            echo "   ✗ 缺少列: " . implode(', ', $missingColumns) . "\n";
        }
    } else {
        echo "   ✗ chat_messages表不存在\n";
    }
} catch (Exception $e) {
    echo "   ✗ 检查表失败: " . $e->getMessage() . "\n";
}

// 3. 检查存储目录
echo "\n3. 检查存储目录...\n";
$storageDirs = [
    storage_path('app/public'),
    storage_path('app/public/chat_files'),
    public_path('storage'),
    public_path('storage/chat_files')
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "   ✓ 目录存在: $dir (权限: $perms)\n";
    } else {
        echo "   ✗ 目录不存在: $dir\n";
    }
}

// 4. 检查存储链接
echo "\n4. 检查存储链接...\n";
$storageLink = public_path('storage');
if (is_link($storageLink)) {
    $target = readlink($storageLink);
    echo "   ✓ 存储链接存在，指向: $target\n";
} else {
    echo "   ✗ 存储链接不存在\n";
}

// 5. 检查PHP配置
echo "\n5. 检查PHP配置...\n";
$configs = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit')
];

foreach ($configs as $key => $value) {
    echo "   - $key: $value\n";
}

// 6. 测试文件写入权限
echo "\n6. 测试文件写入权限...\n";
$testFile = storage_path('app/public/chat_files/test.txt');
try {
    file_put_contents($testFile, 'test');
    if (file_exists($testFile)) {
        echo "   ✓ 文件写入测试成功\n";
        unlink($testFile);
    } else {
        echo "   ✗ 文件写入测试失败\n";
    }
} catch (Exception $e) {
    echo "   ✗ 文件写入测试失败: " . $e->getMessage() . "\n";
}

// 7. 检查路由
echo "\n7. 检查聊天路由...\n";
$routes = [
    'chat.index' => '/chat',
    'chat.show' => '/chat/{user}',
    'chat.send' => '/chat/send/{user}',
    'chat.messages' => '/chat/messages/{user}'
];

foreach ($routes as $name => $path) {
    try {
        $route = Route::getRoutes()->getByName($name);
        if ($route) {
            echo "   ✓ 路由存在: $name ($path)\n";
        } else {
            echo "   ✗ 路由不存在: $name\n";
        }
    } catch (Exception $e) {
        echo "   ✗ 检查路由失败: $name\n";
    }
}

// 8. 检查用户数据
echo "\n8. 检查用户数据...\n";
try {
    $users = \App\Models\User::all();
    $adminUsers = $users->where('is_admin', true);
    $customerUsers = $users->where('is_admin', false);
    
    echo "   - 总用户数: " . $users->count() . "\n";
    echo "   - 管理员数: " . $adminUsers->count() . "\n";
    echo "   - 客户数: " . $customerUsers->count() . "\n";
    
    if ($adminUsers->count() > 0 && $customerUsers->count() > 0) {
        echo "   ✓ 有管理员和客户用户，可以测试聊天功能\n";
    } else {
        echo "   ✗ 缺少管理员或客户用户，无法测试聊天功能\n";
    }
} catch (Exception $e) {
    echo "   ✗ 检查用户数据失败: " . $e->getMessage() . "\n";
}

// 9. 检查聊天消息
echo "\n9. 检查聊天消息...\n";
try {
    $messageCount = \App\Models\ChatMessage::count();
    echo "   - 聊天消息总数: $messageCount\n";
    
    if ($messageCount > 0) {
        $latestMessage = \App\Models\ChatMessage::latest()->first();
        echo "   - 最新消息时间: " . $latestMessage->created_at . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ 检查聊天消息失败: " . $e->getMessage() . "\n";
}

echo "\n=== 测试完成 ===\n";
echo "如果所有项目都显示 ✓，说明聊天功能应该正常工作。\n";
echo "如果有 ✗ 的项目，请参考故障排除指南进行修复。\n";
