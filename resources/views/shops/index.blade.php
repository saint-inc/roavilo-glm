@extends('layouts.app')
@section('title', ($keyword ?: ($category?->name ?: '商户')).' - '.$city->name)

@section('content')
<section class="panel">
    <h2>{{ $city->name }}{{ $keyword ? ' · 搜索「'.$keyword.'」' : ' · '.($category?->name ?? '全部商户') }}{{ $region?->name ? ' · '.$region->name : '' }}</h2>

    {{-- 分类筛选：对标 /{城市拼音}/ch{分类ID} 结构 --}}
    <div class="filter-bar">
        <span>分类：</span>
        <a href="{{ url('/'.$city->slug) }}" @if(! $category) class="active" @endif>全部</a>
        @foreach ($categories as $cat)
            <a href="{{ url('/'.$city->slug.'/ch'.$cat->id) }}" @if($category?->id === $cat->id) class="active" @endif>{{ $cat->name }}</a>
        @endforeach
    </div>

    {{-- 区域筛选：对标点评平台 /{城市}/ch{分类}/g{区域ID} 路径结构 --}}
    <div class="filter-bar">
        <span>区域：</span>
        <a href="{{ url('/'.$city->slug.'/ch'.$category?->id) }}" @if(! $region) class="active" @endif>全部</a>
        @foreach ($regions as $r)
            <a href="{{ url('/'.$city->slug.'/ch'.$category?->id.'/g'.$r->id) }}" @if($region?->id === $r->id) class="active" @endif>{{ $r->name }}</a>
        @endforeach
    </div>

    {{-- 排序：保留 query 参数 --}}
    <div class="filter-bar">
        <span>排序：</span>
        @foreach (['default' => '默认', 'rating' => '评分最高', 'price_asc' => '人均最低', 'price_desc' => '人均最高'] as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}" @if(request('sort', 'default') == $key) class="active" @endif>{{ $label }}</a>
        @endforeach
    </div>

    <div class="grid grid-3">
        @forelse ($shops as $shop)
            {{-- 商户卡片：详情页对标 /shop/{id} --}}
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
                        <span>{{ $shop->region?->name }}</span>
                    </div>
                </div>
            </a>
        @empty
            <p>没有找到符合条件的商户</p>
        @endforelse
    </div>

    {{ $shops->links() }}
</section>
@endsection
