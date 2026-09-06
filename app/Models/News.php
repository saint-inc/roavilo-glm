<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 资讯模型
 * 平台动态 / 媒体报道 / 消费指南（对标点评平台"最新资讯"栏目）
 */
class News extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['title', 'category', 'content', 'source', 'cover', 'view_count', 'is_published'];

    /** 分类中文标签 */
    public const CATEGORY_LABELS = [
        'news' => '平台动态',
        'media' => '媒体报道',
        'guide' => '消费指南',
    ];

    /** 查询作用域：仅已发布 */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
