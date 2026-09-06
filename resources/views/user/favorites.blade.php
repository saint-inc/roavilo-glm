@extends('layouts.app')
@section('title', '我的收藏')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <section class="panel">
        <h2>我的收藏</h2>
        <div class="grid grid-3">
            @forelse ($shops as $favorite)
                <a href="{{ route('shops.show', $favorite->shop) }}" class="shop-card">
                    <div class="body">
                        <h3>{{ $favorite->shop->name }}</h3>
                        <div class="meta">
                            <span class="rating">{{ $favorite->shop->rating }} 分</span>
                            <span>{{ $favorite->shop->category?->name }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <p>暂无收藏</p>
            @endforelse
        </div>
        {{ $shops->links() }}
    </section>
</div>
@endsection
