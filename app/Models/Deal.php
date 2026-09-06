<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 团购模型
 * 商户发布的团购套餐，支持原价/团购价、库存、销量、有效期
 */
class Deal extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = [
        'shop_id', 'title', 'description', 'cover', 'original_price', 'price',
        'stock', 'sold_count', 'starts_at', 'ends_at', 'status',
    ];

    /** 字段类型转换：时间转 Carbon 对象，金额保留 2 位小数 */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'original_price' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    /** 所属商户 */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /** 该团购的订单 */
    public function orders()
    {
        return $this->hasMany(DealOrder::class);
    }

    /** 查询作用域：仅上架（status=1）的团购 */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
