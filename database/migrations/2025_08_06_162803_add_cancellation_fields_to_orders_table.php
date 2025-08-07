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
            // 取消申请相关字段
            $table->text('cancellation_reason')->nullable()->after('notes');
            $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_reason');
            $table->text('admin_response')->nullable()->after('cancellation_requested_at');
            $table->timestamp('admin_responded_at')->nullable()->after('admin_response');
            $table->foreignId('responded_by')->nullable()->after('admin_responded_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['responded_by']);
            $table->dropColumn([
                'cancellation_reason',
                'cancellation_requested_at',
                'admin_response',
                'admin_responded_at',
                'responded_by'
            ]);
        });
    }
};
