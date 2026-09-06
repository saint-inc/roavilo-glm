<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 分类模型
 * 商户经营类别，支持父子层级（一级分类 + 二级分类）
 */
class Category extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['name', 'icon', 'parent_id', 'sort'];

    /** 父级分类（自关联） */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** 子级分类（按 sort 排序） */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort');
    }

    /** 该分类下的商户 */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
