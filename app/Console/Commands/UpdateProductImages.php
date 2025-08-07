<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class UpdateProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新商品图片URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('开始更新商品图片...');

        // 更新苹果图片
        $apple = Product::where('name', '苹果')->first();
        if ($apple) {
            $apple->update([
                'image_url' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=400&h=300&fit=crop'
            ]);
            $this->info('苹果图片已更新');
        }

        // 更新牛奶图片
        $milk = Product::where('name', '牛奶')->first();
        if ($milk) {
            $milk->update([
                'image_url' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&h=300&fit=crop'
            ]);
            $this->info('牛奶图片已更新');
        }

        // 更新面包图片
        $bread = Product::where('name', '面包')->first();
        if ($bread) {
            $bread->update([
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=300&fit=crop'
            ]);
            $this->info('面包图片已更新');
        }

        // 更新或创建茄子
        $eggplant = Product::where('name', '茄子')->first();
        if ($eggplant) {
            $eggplant->update([
                'image_url' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&h=300&fit=crop'
            ]);
            $this->info('茄子图片已更新');
        } else {
            Product::create([
                'name' => '茄子',
                'description' => '茄子是拿来吃的噢不是拿来用的',
                'price' => 6.50,
                'stock' => 50,
                'category' => '食品',
                'image_url' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&h=300&fit=crop',
                'admin_id' => 1,
            ]);
            $this->info('茄子商品已创建并添加图片');
        }

        $this->info('所有商品图片更新完成！');
    }
} 