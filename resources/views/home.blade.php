@extends('layouts.app')
@section('title', '首页')

@section('content')
<section class="panel">
    <h2>{{ $city->name }} · 分类导航</h2>
    <div class="category-bar">
        {{-- 全部分类：指向城市首页 --}}
        <a href="{{ url('/'.$city->slug) }}" @if(!request()->routeIs('shops.category')) class="active" @endif>全部</a>
        {{-- 各分类：对标 /{城市拼音}/ch{分类ID} 结构 --}}
        @foreach ($categories as $cat)
            <a href="{{ url('/'.$city->slug.'/ch'.$cat->id) }}">{{ $cat->name }}</a>
        @endforeach
    </div>
</section>

<section class="panel">
    <h2>热门商户</h2>
    <div class="grid grid-4">
        @forelse ($hotShops as $shop)
            <a href="{{ route('shops.show', $shop) }}" class="shop-card">
                <div class="cover">{{ mb_substr($shop->name, 0, 1) }}</div>
                <div class="body">
                    <h3>{{ $shop->name }}</h3>
                    <div class="meta">
                        <span class="rating">{{ $shop->rating }} 分</span>
                        <span>{{ $shop->category?->name }}</span>
                    </div>
                    <div class="meta" style="margin-top:4px">
                        <span>¥{{ $shop->avg_price ?? '-' }}/人</span>
                        <span>{{ $shop->rating_count }} 条点评</span>
                    </div>
                </div>
            </a>
        @empty
            <p>暂无商户数据</p>
        @endforelse
    </div>
</section>

<section class="panel">
    <h2>热门团购</h2>
    <div class="grid grid-3">
        @forelse ($hotDeals as $deal)
            <a href="{{ route('deals.show', $deal) }}" class="shop-card">
                <div class="body">
                    <h3>{{ $deal->title }}</h3>
                    <div class="meta">
                        <span class="price">¥{{ $deal->price }}</span>
                        <span style="text-decoration:line-through">¥{{ $deal->original_price }}</span>
                    </div>
                    <div class="meta" style="margin-top:4px">
                        <span>{{ $deal->shop?->name }}</span>
                        <span>已售 {{ $deal->sold_count }}</span>
                    </div>
                </div>
            </a>
        @empty
            <p>暂无团购</p>
        @endforelse
    </div>
</section>

<section class="panel">
    <h2>最新点评</h2>
    @forelse ($newReviews as $review)
        <div class="review-item">
            <div class="review-header">
                <div class="avatar">{{ mb_substr($review->user?->name ?? '匿', 0, 1) }}</div>
                <div>
                    <div>{{ $review->user?->name }}</div>
                    <div class="stars">{{ str_repeat('★', (int) $review->rating) }}<span style="color:#ddd">{{ str_repeat('★', 5 - (int) $review->rating) }}</span></div>
                </div>
                <a href="{{ route('shops.show', $review->shop_id) }}" style="margin-left:auto;color:var(--primary);font-size:13px">{{ $review->shop?->name }} →</a>
            </div>
            <p class="review-content">{{ Str::limit($review->content, 120) }}</p>
        </div>
    @empty
        <p>暂无点评</p>
    @endforelse
</section>

<section class="panel">
    <h2>热门城市</h2>
    <div class="category-bar">
        @foreach ($hotCities as $c)
            {{-- 城市切换：直接访问 /{城市拼音} --}}
            <a href="{{ url('/'.$c->slug) }}" @if($c->id === $city->id) class="active" @endif>{{ $c->name }}</a>
        @endforeach
    </div>
</section>
@endsection
