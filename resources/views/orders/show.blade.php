@extends('layouts.app')
@section('title', '订单详情')

@section('content')
<section class="panel">
    <h2>订单 {{ $order->order_no }}</h2>
    <p style="font-size:14px;line-height:2.2">
        团购：{{ $order->deal->title }}<br>
        商户：<a href="{{ route('shops.show', $order->deal->shop) }}" style="color:var(--primary)">{{ $order->deal->shop?->name }}</a><br>
        数量：{{ $order->quantity }} 份<br>
        总金额：<span class="price">¥{{ $order->total_amount }}</span><br>
        状态：{{ ['待支付', '已支付', '已使用', '已退款', '已取消'][$order->status] }}<br>
        下单时间：{{ $order->created_at->format('Y-m-d H:i') }}
        @if ($order->paid_at)<br>支付时间：{{ $order->paid_at->format('Y-m-d H:i') }}@endif
    </p>
    @if ($order->status === 0)
        <form action="{{ route('orders.pay', $order) }}" method="POST" style="margin-top:14px">
            @csrf
            <button type="submit" class="btn">模拟支付 ¥{{ $order->total_amount }}</button>
        </form>
    @endif
</section>
@endsection
