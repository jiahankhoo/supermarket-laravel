<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 分类名称
            $table->text('description')->nullable(); // 分类描述
            $table->string('icon')->nullable(); // 分类图标
            $table->integer('sort_order')->default(0); // 排序
            $table->boolean('is_active')->default(true); // 是否启用
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
