<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 点评模型
 * 用户对商户的评价（每用户对每商户限一条，由数据库唯一索引保证）
 */
class Review extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['shop_id', 'user_id', 'rating', 'content', 'cost', 'images', 'like_count', 'reply_count'];

    /** 字段类型转换：图片 JSON 转数组，评分转浮点 */
    protected $casts = [
        'images' => 'array',
        'rating' => 'float',
    ];

    /** 所属商户 */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /** 点评作者 */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 点评下的回复 */
    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    /** 点赞记录 */
    public function likes()
    {
        return $this->hasMany(ReviewLike::class);
    }

    /** 判断指定用户是否已点赞该点评 */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
