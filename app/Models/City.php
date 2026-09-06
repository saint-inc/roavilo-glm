<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 城市模型
 * 平台覆盖的城市（支持热门城市标记，用于首页城市切换）
 */
class City extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['name', 'slug', 'is_hot', 'sort'];

    /** 城市下的区域 */
    public function regions()
    {
        return $this->hasMany(Region::class);
    }

/** 城市下的商户 */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
