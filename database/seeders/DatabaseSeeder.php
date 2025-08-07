<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 创建默认管理员
        User::create([
            'name' => 'Admin',
            'email' => 'admin@supermarket.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // 运行分类种子
        $this->call(CategorySeeder::class);

        // 创建一些示例商品
        $foodCategory = Category::where('name', '食品')->first();
        $otherCategory = Category::where('name', '其他')->first();

        Product::create([
            'name' => '苹果',
            'description' => '新鲜红苹果，营养丰富',
            'price' => 5.99,
            'stock' => 100,
            'category_id' => $foodCategory->id,
            'image_url' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=400&h=300&fit=crop',
            'admin_id' => 1,
        ]);

        Product::create([
            'name' => '牛奶',
            'description' => '纯天然牛奶，富含蛋白质',
            'price' => 8.50,
            'stock' => 50,
            'category_id' => $foodCategory->id,
            'image_url' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&h=300&fit=crop',
            'admin_id' => 1,
        ]);

        Product::create([
            'name' => '面包',
            'description' => '新鲜出炉的面包',
            'price' => 3.99,
            'stock' => 30,
            'category_id' => $foodCategory->id,
            'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=300&fit=crop',
            'admin_id' => 1,
        ]);

        Product::create([
            'name' => '茄子',
            'description' => '茄子是拿来吃的噢不是拿来用的',
            'price' => 6.50,
            'stock' => 50,
            'category_id' => $foodCategory->id,
            'image_url' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&h=300&fit=crop',
            'admin_id' => 1,
        ]);

        echo "默认管理员账户已创建:\n";
        echo "用户名: admin@supermarket.com\n";
        echo "密码: admin123\n";
        echo "请及时修改默认密码！\n";
    }
} 