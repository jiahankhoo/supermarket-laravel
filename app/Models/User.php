<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * 检查用户是否为管理员
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * 检查用户是否为普通用户
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * 获取用户管理的商品
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'admin_id');
    }

    /**
     * 获取用户的购物车项目
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * 获取用户的订单
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
