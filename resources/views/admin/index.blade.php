@extends('layouts.admin')
@section('title', '数据总览')

@section('content')
{{-- 全站统计卡片 --}}
<div class="stat-row" style="flex-wrap:wrap">
    <div class="stat-box"><div class="num">{{ $stats['users'] }}</div><div class="label">注册用户</div></div>
    <div class="stat-box"><div class="num">{{ $stats['shops'] }}</div><div class="label">商户总数</div></div>
    <div class="stat-box"><div class="num">{{ $stats['reviews'] }}</div><div class="label">点评总数</div></div>
    <div class="stat-box"><div class="num">{{ $stats['deals'] }}</div><div class="label">团购总数</div></div>
    <div class="stat-box"><div class="num">{{ $stats['orders'] }}</div><div class="label">订单总数</div></div>
    <div class="stat-box"><div class="num" style="color:#c07b1c">{{ $stats['pendingShops'] }}</div><div class="label">待审核店铺</div></div>
</div>

<section class="panel" style="margin-top:16px">
    <h2>待审核店铺（快捷处理）</h2>
    @forelse ($pendingShops as $shop)
        <div class="review-item" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <strong style="font-size:14px">{{ $shop->name }}</strong>
            <span style="font-size:12px;color:var(--muted)">{{ $shop->city?->name }} · {{ $shop->category?->name }} · {{ $shop->address }}</span>
            <span style="margin-left:auto;display:flex;gap:8px">
                {{-- 审核通过：上架 --}}
                <form action="{{ route('admin.shops.approve', $shop) }}" method="POST">@csrf
                    <button class="btn btn-sm">通过</button>
                </form>
                {{-- 驳回：下架 --}}
                <form action="{{ route('admin.shops.reject', $shop) }}" method="POST">@csrf
                    <button class="btn btn-sm btn-outline">驳回</button>
                </form>
            </span>
        </div>
    @empty
        <p>暂无待审核店铺</p>
    @endforelse
    <p style="margin-top:10px"><a href="{{ route('admin.shops', ['status' => 2]) }}" style="color:var(--primary);font-size:13px">查看全部待审核 →</a></p>
</section>
@endsection
