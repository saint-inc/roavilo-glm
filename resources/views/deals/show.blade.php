@extends('layouts.app')
@section('title', $deal->title)

@section('content')
<div class="shop-detail">
    <div>
        <section class="panel">
            <h2>{{ $deal->title }}</h2>
            <p style="margin:10px 0;font-size:15px">
                <span class="price" style="font-size:26px">¥{{ $deal->price }}</span>
                <span style="color:var(--muted);text-decoration:line-through;margin-left:10px">¥{{ $deal->original_price }}</span>
                <span style="margin-left:14px;font-size:13px;color:var(--muted)">已售 {{ $deal->sold_count }} 份</span>
            </p>
            <p style="line-height:1.8;color:#555">{{ $deal->description }}</p>
            @if ($deal->ends_at)
                <p style="margin-top:10px;font-size:13px;color:var(--primary)">距结束：{{ $deal->ends_at->diffForHumans() }}</p>
            @endif
        </section>
        <section class="panel">
            <h2>购买</h2>
            @auth
                <form action="{{ route('orders.store', $deal) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="quantity">数量</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="99" style="max-width:120px">
                    </div>
                    <button type="submit" class="btn">立即抢购</button>
                </form>
            @else
                <p><a href="/login" class="btn">登录后购买</a></p>
            @endauth
        </section>
    </div>
    <div>
        <section class="panel">
            <h2>商户信息</h2>
            <p style="font-size:14px;line-height:2">
                🏪 <a href="{{ route('shops.show', $deal->shop) }}" style="color:var(--primary)">{{ $deal->shop?->name }}</a><br>
                📍 {{ $deal->shop?->address }}
            </p>
        </section>
    </div>
</div>
@endsection
