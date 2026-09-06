<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 点评回复模型
 * 用户（含商家）对点评的回复
 */
class ReviewReply extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['review_id', 'user_id', 'content'];

    /** 所属点评 */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /** 回复人 */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
