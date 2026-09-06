<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 点评点赞模型
 * 记录用户对点评的点赞（每用户对每点评限一次，唯一索引保证）
 */
class ReviewLike extends Model
{
    use HasFactory;

    /** 所属点评 */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /** 点赞用户 */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
