<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 收藏模型
 * 用户对商户的收藏（每用户对每商户限一次，唯一索引保证）
 */
class Favorite extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['user_id', 'shop_id'];

    /** 收藏用户 */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 被收藏的商户 */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
