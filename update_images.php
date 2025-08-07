<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// 更新商品图片URL
DB::table('products')->where('name', '苹果')->update([
    'image_url' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=400&h=300&fit=crop'
]);

DB::table('products')->where('name', '牛奶')->update([
    'image_url' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&h=300&fit=crop'
]);

DB::table('products')->where('name', '面包')->update([
    'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=300&fit=crop'
]);

// 如果茄子不存在，则创建
if (!DB::table('products')->where('name', '茄子')->exists()) {
    DB::table('products')->insert([
        'name' => '茄子',
        'description' => '茄子是拿来吃的噢不是拿来用的',
        'price' => 6.50,
        'stock' => 50,
        'category' => '食品',
        'image_url' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&h=300&fit=crop',
        'admin_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
} else {
    DB::table('products')->where('name', '茄子')->update([
        'image_url' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&h=300&fit=crop'
    ]);
}

echo "商品图片URL已更新完成！\n"; 