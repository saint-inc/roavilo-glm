<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 用户模型
 * 平台注册用户（普通用户 + 入驻商户主复用同一账号体系）
 */
#[Fillable(['name', 'email', 'password', 'avatar', 'bio', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** 用户发布的点评 */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /** 用户的收藏记录 */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /** 用户的团购订单 */
    public function orders()
    {
        return $this->hasMany(DealOrder::class);
    }

    /** 用户入驻的店铺（商户主身份） */
    public function shops()
    {
        return $this->hasMany(Shop::class, 'owner_id');
    }

    /** 字段类型转换：邮箱验证时间转 Carbon，密码自动哈希 */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
