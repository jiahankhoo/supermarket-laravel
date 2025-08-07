<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 检查数据库类型
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite 方式：重新创建表
            DB::statement('PRAGMA foreign_keys=off');
            
            // 创建新表
            DB::statement("
                CREATE TABLE orders_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    total_amount DECIMAL(10,2) NOT NULL,
                    status TEXT CHECK(status IN ('pending', 'processing', 'completed', 'cancelled', 'cancellation_requested')) DEFAULT 'pending',
                    receiver_name TEXT,
                    receiver_phone TEXT,
                    shipping_address TEXT,
                    city TEXT,
                    postal_code TEXT,
                    notes TEXT,
                    cancellation_reason TEXT,
                    cancellation_requested_at TIMESTAMP,
                    admin_response TEXT,
                    admin_responded_at TIMESTAMP,
                    responded_by INTEGER,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (responded_by) REFERENCES users(id) ON DELETE SET NULL
                )
            ");
            
            // 复制数据
            DB::statement("
                INSERT INTO orders_new (id, user_id, total_amount, status, receiver_name, receiver_phone, shipping_address, city, postal_code, notes, created_at, updated_at)
                SELECT id, user_id, total_amount, 
                       CASE 
                           WHEN status IN ('pending', 'processing', 'completed', 'cancelled', 'cancellation_requested') THEN status
                           ELSE 'pending'
                       END as status,
                       receiver_name, receiver_phone, shipping_address, city, postal_code, notes, created_at, updated_at
                FROM orders
            ");
            
            // 删除旧表
            DB::statement('DROP TABLE orders');
            
            // 重命名新表
            DB::statement('ALTER TABLE orders_new RENAME TO orders');
            
            DB::statement('PRAGMA foreign_keys=on');
            
        } else {
            // MySQL 方式
            // 首先检查当前状态值
            $currentStatuses = DB::select("SHOW COLUMNS FROM orders LIKE 'status'");
            $enumValues = [];
            
            if (!empty($currentStatuses)) {
                $type = $currentStatuses[0]->Type;
                if (preg_match("/^enum\((.*)\)$/", $type, $matches)) {
                    $values = explode(',', $matches[1]);
                    foreach ($values as $value) {
                        $enumValues[] = trim($value, "'");
                    }
                }
            }
            
            // 如果cancellation_requested不在当前枚举值中，添加它
            if (!in_array('cancellation_requested', $enumValues)) {
                // 临时修改为VARCHAR
                DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(30) DEFAULT 'pending'");
                
                // 更新任何无效的状态值
                DB::statement("UPDATE orders SET status = 'pending' WHERE status NOT IN ('pending', 'processing', 'completed', 'cancelled', 'cancellation_requested')");
                
                // 重新创建ENUM
                DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled', 'cancellation_requested') DEFAULT 'pending'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite 回滚：重新创建表
            DB::statement('PRAGMA foreign_keys=off');
            
            // 创建旧表结构
            DB::statement("
                CREATE TABLE orders_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    total_amount DECIMAL(10,2) NOT NULL,
                    status TEXT CHECK(status IN ('pending', 'processing', 'completed', 'cancelled')) DEFAULT 'pending',
                    receiver_name TEXT,
                    receiver_phone TEXT,
                    shipping_address TEXT,
                    city TEXT,
                    postal_code TEXT,
                    notes TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ");
            
            // 复制数据，将cancellation_requested状态改为pending
            DB::statement("
                INSERT INTO orders_old (id, user_id, total_amount, status, receiver_name, receiver_phone, shipping_address, city, postal_code, notes, created_at, updated_at)
                SELECT id, user_id, total_amount, 
                       CASE 
                           WHEN status = 'cancellation_requested' THEN 'pending'
                           WHEN status IN ('pending', 'processing', 'completed', 'cancelled') THEN status
                           ELSE 'pending'
                       END as status,
                       receiver_name, receiver_phone, shipping_address, city, postal_code, notes, created_at, updated_at
                FROM orders
            ");
            
            // 删除新表
            DB::statement('DROP TABLE orders');
            
            // 重命名旧表
            DB::statement('ALTER TABLE orders_old RENAME TO orders');
            
            DB::statement('PRAGMA foreign_keys=on');
            
        } else {
            // MySQL 回滚
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }
};
