@extends('layouts.app')
@section('title', '订单与核销')

@section('content')
<div class="user-layout">
    @include('merchant._sidebar', ['shop' => $shop])
    <section class="panel">
        <h2>核销团购券</h2>
        {{-- 核销表单：输入买家出示的 12 位核销码 --}}
        <form action="{{ route('merchant.orders.verify', $shop) }}" method="POST" style="display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap">
            @csrf
            <div>
                <input type="text" name="verify_code" placeholder="输入 12 位核销码" maxlength="12" style="max-width:260px;text-transform:uppercase" required>
                @error('verify_code')<p style="color:#c0392b;font-size:12px">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn">核销</button>
        </form>
    </section>
    <section class="panel">
        <h2>团购订单</h2>
        @forelse ($orders as $order)
            <div class="review-item">
                <div class="review-header">
                    <span style="font-size:13px;font-weight:bold">{{ $order->order_no }}</span>
                    <span style="margin-left:auto;font-size:12px;color:var(--muted)">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <p class="review-content" style="font-size:13px">
                    {{ $order->deal->title }} × {{ $order->quantity }} · ¥{{ $order->total_amount }} ·
                    买家 {{ $order->user?->name }} ·
                    <strong>[{{ ['待支付', '已支付', '已使用', '已退款', '已取消'][$order->status] }}]</strong>
                    @if ($order->verify_code)
                        <span style="margin-left:8px;font-family:monospace">核销码：{{ $order->verify_code }}</span>
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
