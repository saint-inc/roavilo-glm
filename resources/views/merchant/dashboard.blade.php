@extends('layouts.app')
@section('title', $shop->name.' - 数据看板')

@section('content')
<div class="user-layout">
    @include('merchant._sidebar', ['shop' => $shop])
    <div>
        {{-- 顶部统计卡片：浏览量/评分/点评/团购销量/订单 --}}
        <div class="stat-row" style="flex-wrap:wrap">
            <div class="stat-box"><div class="num">{{ $shop->view_count }}</div><div class="label">累计浏览</div></div>
            <div class="stat-box"><div class="num">{{ $shop->rating }}</div><div class="label">综合评分</div></div>
            <div class="stat-box"><div class="num">{{ $shop->rating_count }}</div><div class="label">点评总数</div></div>
            <div class="stat-box"><div class="num">{{ $dealStats->sold }}</div><div class="label">团购销量</div></div>
            <div class="stat-box"><div class="num">{{ $paidOrders }}</div><div class="label">有效订单</div></div>
            <div class="stat-box"><div class="num">{{ $recentReviews }}</div><div class="label">近7天新点评</div></div>
        </div>
        <section class="panel" style="margin-top:16px">
            <h2>{{ $shop->name }}
                @if ($shop->status === 1)<span style="color:#1c7c3c;font-size:13px">营业中</span>
                @elseif ($shop->status === 2)<span style="color:#c07b1c;font-size:13px">待审核</span>
                @else<span style="color:#999;font-size:13px">已下架</span>@endif
            </h2>
            <p style="font-size:14px;line-height:2;color:#555">
                📍 {{ $shop->address }}<br>
                📞 {{ $shop->phone ?? '暂无' }} · 💰 人均 ¥{{ $shop->avg_price ?? '-' }} · 🕐 {{ $shop->business_hours ?? '未设置' }}
            </p>
            <p style="margin-top:12px">
                <a href="{{ route('merchant.edit', $shop) }}" class="btn">编辑店铺信息</a>
                <a href="{{ route('shops.show', $shop) }}" class="btn btn-outline">查看前台页面</a>
            </p>
        </section>
    </div>
</div>
@endsection
