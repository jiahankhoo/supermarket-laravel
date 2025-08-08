<?php

/**
 * 修复聊天功能问题的脚本
 * 解决在线部署后聊天发送失败的问题
 */

echo "开始修复聊天功能问题...\n\n";

// 1. 检查并创建存储链接
echo "1. 检查存储链接...\n";
$storageLink = public_path('storage');
if (!is_link($storageLink)) {
    echo "   - 存储链接不存在，正在创建...\n";
    if (is_dir($storageLink)) {
        rmdir($storageLink);
    }
    symlink(storage_path('app/public'), $storageLink);
    echo "   - 存储链接创建成功\n";
} else {
    echo "   - 存储链接已存在\n";
}

// 2. 确保聊天文件目录存在
echo "\n2. 检查聊天文件目录...\n";
$chatFilesDir = storage_path('app/public/chat_files');
$publicChatFilesDir = public_path('storage/chat_files');

if (!is_dir($chatFilesDir)) {
    mkdir($chatFilesDir, 0755, true);
    echo "   - 创建聊天文件目录: $chatFilesDir\n";
}

if (!is_dir($publicChatFilesDir)) {
    mkdir($publicChatFilesDir, 0755, true);
    echo "   - 创建公共聊天文件目录: $publicChatFilesDir\n";
}

// 3. 设置文件权限
echo "\n3. 设置文件权限...\n";
$dirs = [
    storage_path(),
    storage_path('app'),
    storage_path('app/public'),
    storage_path('app/public/chat_files'),
    public_path('storage'),
    public_path('storage/chat_files'),
    bootstrap_path('cache')
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "   - 设置权限 755: $dir\n";
    }
}

// 4. 同步现有文件
echo "\n4. 同步现有聊天文件...\n";
$files = glob($chatFilesDir . '/*');
foreach ($files as $file) {
    $filename = basename($file);
    $targetFile = $publicChatFilesDir . '/' . $filename;
    
    if (!file_exists($targetFile) || filemtime($file) > filemtime($targetFile)) {
        if (copy($file, $targetFile)) {
            chmod($targetFile, 0644);
            echo "   - 同步文件: $filename\n";
        } else {
            echo "   - 同步失败: $filename\n";
        }
    }
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

// 6. 检查数据库连接
echo "\n6. 检查数据库连接...\n";
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST', 'localhost') . 
        ';dbname=' . env('DB_DATABASE', 'laravel'),
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
    echo "   - 数据库连接成功\n";
    
    // 检查chat_messages表
    $stmt = $pdo->query("SHOW TABLES LIKE 'chat_messages'");
    if ($stmt->rowCount() > 0) {
        echo "   - chat_messages表存在\n";
    } else {
        echo "   - 警告: chat_messages表不存在\n";
    }
} catch (Exception $e) {
    echo "   - 数据库连接失败: " . $e->getMessage() . "\n";
}

// 7. 清除缓存
echo "\n7. 清除缓存...\n";
$cacheDirs = [
    storage_path('framework/cache'),
    storage_path('framework/views'),
    storage_path('framework/sessions')
];

foreach ($cacheDirs as $cacheDir) {
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   - 清除缓存: $cacheDir\n";
    }
}

echo "\n修复完成！\n";
echo "请重启Web服务器以确保所有更改生效。\n";
