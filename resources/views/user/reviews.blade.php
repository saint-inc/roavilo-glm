@extends('layouts.app')
@section('title', '我的点评')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <section class="panel">
        <h2>我的点评</h2>
        @forelse ($reviews as $review)
            <div class="review-item">
                <div class="review-header">
                    <a href="{{ route('shops.show', $review->shop_id) }}" style="color:var(--primary);font-size:14px">{{ $review->shop?->name }}</a>
                    <span class="stars">{{ str_repeat('★', (int) $review->rating) }}</span>
                    <span style="margin-left:auto;font-size:12px;color:var(--muted)">{{ $review->created_at->format('Y-m-d') }}</span>
                </div>
                <p class="review-content">{{ $review->content }}</p>
            </div>
        @empty
            <p>你还没有写过点评，去逛逛吧！</p>
        @endforelse
        {{ $reviews->links() }}
    </section>
</div>
@endsection
