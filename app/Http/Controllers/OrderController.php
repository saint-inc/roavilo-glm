<?php

namespace App\Http\Controllers;

use App\Models\DealOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * 订单控制器
 * 负责团购订单的下单、列表、详情、支付（需登录）
 */
class OrderController extends Controller
{

    /**
     * 创建团购订单
     * 生成唯一订单号（R + 时间 + 随机串），状态为 0（待支付）
     */
    public function store(Request $request, \App\Models\Deal $deal)
    {
        // 数量校验：1-99 份
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $quantity = $validated['quantity'];

        // 创建订单：订单号 = R + 年月日时分秒 + 6 位随机码，总金额 = 单价 × 数量
        $order = DealOrder::create([
            'order_no' => 'R'.now()->format('YmdHis').Str::upper(Str::random(6)),
            'user_id' => Auth::id(),
            'deal_id' => $deal->id,
            'quantity' => $quantity,
            'total_amount' => $deal->price * $quantity,
            'status' => 0, // 0 = 待支付
        ]);

        // 跳转到订单详情页进行支付
        return redirect()->route('orders.show', $order)->with('success', '订单已创建，请完成支付');
    }

    /**
     * 我的订单列表（按下单时间倒序，每页 10 条）
     */
    public function index(Request $request)
    {
        // 预加载团购及所属商户，避免 N+1 查询
        $orders = $request->user()->orders()->with('deal.shop')->latest()->paginate(10);

        return view('user.orders', compact('orders'));
    }

    /**
     * 订单详情页
     * 仅订单所属用户可见（403 越权保护）
     */
    public function show(DealOrder $order)
    {
        // 越权检查：非本人订单直接 403
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('deal.shop');

        return view('orders.show', compact('order'));
    }

    /**
     * 模拟支付
     * 仅待支付（status=0）订单可支付；支付成功后更新状态并累加团购销量
     */
    public function pay(Request $request, DealOrder $order)
    {
        // 越权检查：非本人订单直接 403
        abort_unless($order->user_id === Auth::id(), 403);
        // 状态检查：仅待支付订单可支付
        abort_unless($order->status === 0, 400, '订单状态不允许支付');

        // 更新订单为已支付，记录支付时间，并生成 12 位核销码（商户核销凭证）
        $order->update([
            'status' => 1,
            'paid_at' => now(),
            'verify_code' => strtoupper(\Illuminate\Support\Str::random(12)),
        ]);
        // 团购销量累加（按购买数量）
        $order->deal->increment('sold_count', $order->quantity);

        return redirect()->route('orders.show', $order)->with('success', '支付成功');
    }
}
