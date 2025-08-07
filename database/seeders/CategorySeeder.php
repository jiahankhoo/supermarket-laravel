<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => '食品',
                'description' => '各种食品类商品',
                'icon' => 'fas fa-utensils',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => '饮料',
                'description' => '各种饮料类商品',
                'icon' => 'fas fa-wine-bottle',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => '日用品',
                'description' => '日常生活用品',
                'icon' => 'fas fa-home',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => '电子产品',
                'description' => '各种电子设备',
                'icon' => 'fas fa-mobile-alt',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => '服装',
                'description' => '各种服装类商品',
                'icon' => 'fas fa-tshirt',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => '其他',
                'description' => '其他类商品',
                'icon' => 'fas fa-gift',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 