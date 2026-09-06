<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 团购订单模型
 * 用户购买团购产生的订单，状态流转：待支付→已支付→已使用（或退款/取消）
 */
class DealOrder extends Model
{
    use HasFactory;

    /** 允许批量赋值的字段 */
    protected $fillable = [
        'order_no', 'user_id', 'deal_id', 'quantity', 'total_amount',
        'status', 'paid_at', 'used_at', 'verify_code',
    ];

    /** 字段类型转换：时间转 Carbon，金额保留 2 位小数 */
    protected $casts = [
        'paid_at' => 'datetime',
        'used_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /** 下单用户 */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 关联的团购 */
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
