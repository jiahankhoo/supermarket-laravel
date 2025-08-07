<?php

/**
 * 同步存储文件到public目录
 * 这个脚本确保chat_files目录中的文件能够正确访问
 */

$sourceDir = __DIR__ . '/storage/app/public/chat_files';
$targetDir = __DIR__ . '/public/storage/chat_files';

// 确保目标目录存在
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "创建目录: $targetDir\n";
}

// 获取源目录中的所有文件
$files = glob($sourceDir . '/*');

foreach ($files as $file) {
    $filename = basename($file);
    $targetFile = $targetDir . '/' . $filename;
    
    // 如果目标文件不存在或源文件更新，则复制
    if (!file_exists($targetFile) || filemtime($file) > filemtime($targetFile)) {
        if (copy($file, $targetFile)) {
            echo "同步文件: $filename\n";
        } else {
            echo "同步失败: $filename\n";
        }
    }
}

echo "文件同步完成！\n"; 