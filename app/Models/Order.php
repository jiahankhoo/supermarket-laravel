<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'receiver_name',
        'receiver_phone',
        'shipping_address',
        'city',
        'postal_code',
        'notes',
        'cancellation_reason',
        'cancellation_requested_at',
        'admin_response',
        'admin_responded_at',
        'responded_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cancellation_requested_at' => 'datetime',
        'admin_responded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function adminResponder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // 检查是否可以申请取消
    public function canRequestCancellation()
    {
        return in_array($this->status, ['pending', 'processing']) && !$this->cancellation_requested_at;
    }

    // 检查是否可以取消（已批准）
    public function canBeCancelled()
    {
        return $this->status === 'cancellation_requested' && $this->admin_response;
    }

    // 获取状态显示文本
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => '待处理',
            'processing' => '处理中',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'cancellation_requested' => '申请取消中',
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    // 获取状态徽章颜色
    public function getStatusBadgeAttribute()
    {
        $badgeMap = [
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'cancellation_requested' => 'bg-warning',
        ];

        return $badgeMap[$this->status] ?? 'bg-secondary';
    }
} 