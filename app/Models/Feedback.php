<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 意见反馈模型
 * 用户/游客通过客服中心提交的反馈（建议/投诉/问题）
 */
class Feedback extends Model
{
    use HasFactory;

    /** 指定表名（feedback 单复数同形，需显式声明避免 Laravel 推断为 feedback） */
    protected $table = 'feedbacks';

    /** 允许批量赋值的字段 */
    protected $fillable = ['user_id', 'name', 'contact', 'type', 'title', 'content', 'status', 'reply'];

    /** 反馈类型中文标签 */
    public const TYPE_LABELS = [
        'suggest' => '功能建议',
        'complaint' => '投诉举报',
        'bug' => '问题反馈',
        'other' => '其他',
    ];

    /** 提交用户（游客可为空） */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
