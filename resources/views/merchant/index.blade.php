@extends('layouts.app')
@section('title', '商户中心')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <section class="panel">
        <h2>我的店铺</h2>
        <p style="margin-bottom:14px"><a href="{{ route('merchant.create') }}" class="btn">+ 入驻新店铺</a></p>
        @forelse ($shops as $shop)
            <div class="review-item">
                <div class="review-header">
                    <a href="{{ route('merchant.dashboard', $shop) }}" style="color:var(--primary)">{{ $shop->name }}</a>
                    <span style="font-size:12px;color:var(--muted)">{{ $shop->city?->name }} · {{ $shop->category?->name }}</span>
                    <span style="margin-left:auto;font-size:12px">
                        @if ($shop->status === 1)<span style="color:#1c7c3c">营业中</span>
                        @elseif ($shop->status === 2)<span style="color:#c07b1c">待审核</span>
                        @else<span style="color:#999">已下架</span>@endif
                    </span>
                </div>
                <p class="review-content" style="color:var(--muted);font-size:13px">{{ $shop->address }}</p>
            </div>
        @empty
            <p>你还没有入驻店铺，点击上方按钮入驻。</p>
        @endforelse
    </section>
</div>
@endsection
