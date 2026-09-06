<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 区域模型
 * 城市下辖行政区（如：黄浦区、浦东新区）
 */
class Region extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['city_id', 'name', 'sort'];

    /** 所属城市 */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
