@extends('layouts.app')
@section('title', '团购')

@section('content')
<section class="panel">
    <h2>{{ $city->name }} · 热门团购</h2>
    <div class="grid grid-3">
        @forelse ($deals as $deal)
            <a href="{{ route('deals.show', $deal) }}" class="shop-card">
                <div class="body">
                    <h3>{{ $deal->title }}</h3>
                    <p style="font-size:12px;color:var(--muted);margin:4px 0">{{ $deal->shop?->name }}</p>
                    <div class="meta">
                        <span class="price">¥{{ $deal->price }}</span>
                        <span style="text-decoration:line-through">¥{{ $deal->original_price }}</span>
                        <span>已售 {{ $deal->sold_count }}</span>
                    </div>
                </div>
            </a>
        @empty
            <p>暂无团购</p>
        @endforelse
    </div>
    {{ $deals->links() }}
</section>
@endsection
