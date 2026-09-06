@extends('layouts.app')
@section('title', '我的订单')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <section class="panel">
        <h2>我的订单</h2>
        @forelse ($orders as $order)
            <div class="review-item">
                <div class="review-header">
                    <a href="{{ route('orders.show', $order) }}" style="color:var(--primary)">{{ $order->order_no }}</a>
                    <span style="margin-left:auto;font-size:12px;color:var(--muted)">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <p class="review-content">
                    {{ $order->deal->title }} × {{ $order->quantity }}
                    <span class="price" style="margin-left:8px">¥{{ $order->total_amount }}</span>
                    <span style="margin-left:12px;font-size:13px">[{{ ['待支付', '已支付', '已使用', '已退款', '已取消'][$order->status] }}]</span>
                    {{-- 已支付订单展示核销码（到店出示给商户） --}}
                    @if ($order->verify_code)
                        <span style="margin-left:12px;font-size:13px">核销码：<strong style="font-family:monospace;letter-spacing:2px">{{ $order->verify_code }}</strong></span>
                    @endif
                </p>
            </div>
        @empty
            <p>暂无订单</p>
        @endforelse
        {{ $orders->links() }}
    </section>
</div>
@endsection
