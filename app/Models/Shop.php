<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 商户模型
 * 平台核心实体：一家本地生活服务商户
 */
class Shop extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = [
        'city_id', 'category_id', 'region_id', 'owner_id', 'name', 'slug', 'cover',
        'address', 'longitude', 'latitude', 'phone', 'avg_price', 'rating',
        'rating_count', 'description', 'business_hours', 'tags', 'view_count', 'status',
    ];

    /** 字段类型转换：tags 转 PHP 数组，金额/经纬度保留小数 */
    protected $casts = [
        'tags' => 'array',
        'avg_price' => 'decimal:2',
        'longitude' => 'decimal:6',
        'latitude' => 'decimal:6',
    ];

    /** 所属城市 */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /** 所属分类 */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** 所在区域 */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /** 绑定的商户主（入驻申请人） */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** 商户图片（按 sort 排序） */
    public function images()
    {
        return $this->hasMany(ShopImage::class)->orderBy('sort');
    }

    /** 商户点评（按时间倒序） */
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    /** 收藏记录 */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /** 商户团购 */
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    /** 查询作用域：仅上架（status=1）的商户 */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
