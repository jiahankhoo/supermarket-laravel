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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('receiver_name')->nullable()->after('status');
            $table->string('receiver_phone')->nullable()->after('receiver_name');
            $table->text('shipping_address')->nullable()->after('receiver_phone');
            $table->string('city')->nullable()->after('shipping_address');
            $table->string('postal_code')->nullable()->after('city');
            $table->text('notes')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'receiver_name',
                'receiver_phone', 
                'shipping_address',
                'city',
                'postal_code',
                'notes'
            ]);
        });
    }
}; 