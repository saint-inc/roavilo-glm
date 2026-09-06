<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 商户图片模型
 * 商户详情页的相册图片
 */
class ShopImage extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = ['shop_id', 'path', 'caption', 'sort'];

    /** 所属商户 */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
