<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // 获取两个用户之间的聊天记录
    public static function getConversation($user1Id, $user2Id, $limit = 50)
    {
        return self::where(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user1Id)
                  ->where('receiver_id', $user2Id);
        })->orWhere(function ($query) use ($user1Id, $user2Id) {
            $query->where('sender_id', $user2Id)
                  ->where('receiver_id', $user1Id);
        })->orderBy('created_at', 'asc')
          ->limit($limit)
          ->get();
    }

    // 标记消息为已读
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    // 获取未读消息数量
    public static function getUnreadCount($userId)
    {
        return self::where('receiver_id', $userId)
                   ->where('is_read', false)
                   ->count();
    }
} 