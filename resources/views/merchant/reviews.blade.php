@extends('layouts.app')
@section('title', '点评管理')

@section('content')
<div class="user-layout">
    @include('merchant._sidebar', ['shop' => $shop])
    <section class="panel">
        <h2>点评管理（{{ $shop->rating_count }} 条）</h2>
        @forelse ($reviews as $review)
            <div class="review-item">
                <div class="review-header">
                    <div class="avatar">{{ mb_substr($review->user?->name ?? '匿', 0, 1) }}</div>
                    <div>
                        <div style="font-size:14px">{{ $review->user?->name }}</div>
                        <div class="stars">{{ str_repeat('★', (int) $review->rating) }}</div>
                    </div>
                    <span style="margin-left:auto;font-size:12px;color:var(--muted)">{{ $review->created_at->format('Y-m-d') }}</span>
                </div>
                <p class="review-content">{{ $review->content }}</p>
                {{-- 已有回复展示 --}}
                @foreach ($review->replies as $reply)
                    <div style="background:var(--bg);border-radius:6px;padding:8px 12px;margin-top:8px;font-size:13px">
                        <strong>{{ $reply->user_id === $shop->owner_id ? '本店' : $reply->user?->name }}</strong>：{{ $reply->content }}
                    </div>
                @endforeach
                {{-- 商户回复表单 --}}
                <form action="{{ route('merchant.review.reply', [$shop, $review]) }}" method="POST" style="margin-top:8px">
                    @csrf
                    <input type="text" name="content" placeholder="以商家身份回复…" style="max-width:400px">
                    <button type="submit" class="btn" style="padding:6px 14px">回复</button>
                </form>
            </div>
        @empty
            <p>暂无点评</p>
        @endforelse
        {{ $reviews->links() }}
    </section>
</div>
@endsection
