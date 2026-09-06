@extends('layouts.app')
@section('title', $shop->name)

@section('content')
<div class="shop-detail">
    <div>
        <section class="panel">
            <h2>{{ $shop->name }}
                <span class="rating" style="font-size:14px;margin-left:10px">{{ $shop->rating }} 分</span>
                <span style="font-size:13px;color:var(--muted);margin-left:8px">{{ $shop->rating_count }} 条点评</span>
            </h2>
            <p style="color:var(--muted);font-size:13px;margin-bottom:10px">
                {{ $shop->category?->name }} · 人均 ¥{{ $shop->avg_price ?? '-' }} · {{ $shop->business_hours }}
            </p>
            <p>{{ $shop->description }}</p>
            @if ($shop->tags)
                <div class="category-bar" style="margin-top:10px">
                    @foreach ($shop->tags as $tag)<span class="btn-outline" style="padding:2px 10px;font-size:12px">{{ $tag }}</span>@endforeach
                </div>
            @endif
        </section>

        <section class="panel">
            {{-- 点评标题 + 评分分布（对标点评平台详情页评分模块） --}}
            <h2>网友点评（{{ $shop->rating_count }}）</h2>
            @if ($shop->rating_count > 0)
                <div style="background:var(--bg);border-radius:8px;padding:12px;margin-bottom:12px">
                    @foreach ([5, 4, 3, 2, 1] as $star)
                        @php $cnt = $distribution[$star]; $pct = round($cnt / max(1, $shop->rating_count) * 100); @endphp
                        <div class="rating-dist">
                            <span style="min-width:30px">{{ $star }} 星</span>
                            <span class="bar"><i style="width: {{ $pct }}%"></i></span>
                            <span class="cnt">{{ $cnt }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 点评筛选与排序：好评/中评/差评 + 最新/最早/最热 --}}
            <div class="filter-bar">
                <span>筛选：</span>
                @foreach (['all' => '全部', 'good' => '好评(4-5星)', 'mid' => '中评(3星)', 'bad' => '差评(1-2星)'] as $k => $label)
                    <a href="{{ request()->fullUrlWithQuery(['rf' => $k === 'all' ? null : $k, 'page' => null]) }}"
                       @if(request('rf', 'all') == $k) class="active" @endif>{{ $label }}</a>
                @endforeach
            </div>
            <div class="filter-bar">
                <span>排序：</span>
                @foreach (['new' => '最新', 'old' => '最早', 'hot' => '最热'] as $k => $label)
                    <a href="{{ request()->fullUrlWithQuery(['r_sort' => $k, 'page' => null]) }}"
                       @if(request('r_sort', 'new') == $k) class="active" @endif>{{ $label }}</a>
                @endforeach
            </div>

            @forelse ($reviews as $review)
                <div class="review-item">
                    <div class="review-header">
                        <div class="avatar">{{ mb_substr($review->user?->name ?? '匿', 0, 1) }}</div>
                        <div>
                            <div style="font-size:14px">{{ $review->user?->name }}</div>
                            <div class="stars">{{ str_repeat('★', (int) $review->rating) }}<span style="color:#ddd">{{ str_repeat('★', 5 - (int) $review->rating) }}</span></div>
                        </div>
                        @if ($review->cost)<span style="margin-left:auto;font-size:12px;color:var(--muted)">人均 ¥{{ $review->cost }}</span>@endif
                    </div>
                    <p class="review-content">{{ $review->content }}</p>
                    <div class="review-meta">
                        <span>{{ $review->created_at->format('Y-m-d') }}</span>
                        @auth
                            <form action="{{ route('reviews.like', $review) }}" method="POST" class="inline-form">@csrf
                                <button type="submit" class="link-btn">👍 有用 ({{ $review->like_count }})</button>
                            </form>
                        @else
                            <span>👍 {{ $review->like_count }}</span>
                        @endauth
                    </div>
                    @foreach ($review->replies as $reply)
                        <div style="background:var(--bg);border-radius:6px;padding:8px 12px;margin-top:8px;font-size:13px">
                            <strong>{{ $reply->user?->name }}</strong>：{{ $reply->content }}
                        </div>
                    @endforeach
                    @auth
                        <form action="{{ route('reviews.reply', $review) }}" method="POST" style="margin-top:8px">
                            @csrf
                            <input type="text" name="content" placeholder="回复这条点评…" style="max-width:400px">
                            <button type="submit" class="btn" style="padding:6px 14px">回复</button>
                        </form>
                    @endauth
                </div>
            @empty
                <p>暂无点评，快来抢沙发吧！</p>
            @endforelse
            {{ $reviews->links() }}
        </section>

        @auth
        <section class="panel">
            <h2>写点评</h2>
            <form action="{{ route('reviews.store', $shop) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rating">评分（1-5）</label>
                    <select name="rating" id="rating">
                        @for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }} 星</option>@endfor
                    </select>
                </div>
                <div class="form-group">
                    <label for="content">点评内容</label>
                    <textarea name="content" id="content" placeholder="分享你的消费体验（至少 10 个字）">{{ old('content') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="cost">本次人均消费（元，选填）</label>
                    <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost') }}">
                </div>
                <button type="submit" class="btn">发布点评</button>
            </form>
        </section>
        @endauth
    </div>

    <div>
        <section class="panel">
            <h2>商户信息</h2>
            <p style="font-size:14px;line-height:2">
                🏙 <a href="{{ url('/'.$shop->city->slug) }}" style="color:var(--primary)">{{ $shop->city->name }}</a>
                @if ($shop->region)
                    · <a href="{{ url('/'.$shop->city->slug.'?region='.$shop->region->id) }}" style="color:var(--primary)">{{ $shop->region->name }}</a>
                @endif<br>
                📍 {{ $shop->address }}<br>
                📞 {{ $shop->phone ?? '暂无' }}<br>
                👀 {{ $shop->view_count }} 次浏览
            </p>
            {{-- 同区域其他商户（地址/区域联动，对标点评"附近商户"） --}}
            @php
                $nearbyShops = $shop->region
                    ? \App\Models\Shop::active()
                        ->where('region_id', $shop->region_id)
                        ->where('id', '!=', $shop->id)
                        ->take(3)->get()
                    : collect();
            @endphp
            @if ($nearbyShops->isNotEmpty())
                <div style="border-top:1px solid var(--border);margin-top:12px;padding-top:12px">
                    <p style="font-size:13px;color:var(--muted);margin-bottom:8px">📍 {{ $shop->region->name }}附近商户</p>
                    @foreach ($nearbyShops as $nearby)
                        <a href="{{ route('shops.show', $nearby) }}" style="display:block;font-size:13px;padding:4px 0">
                            {{ $nearby->name }} <span style="color:var(--primary)">{{ $nearby->rating }}分</span>
                        </a>
                    @endforeach
                </div>
            @endif
            @auth
                {{-- 收藏表单：对标 /shop/{id}/fav --}}
                <form action="{{ route('favorites.toggle', $shop) }}" method="POST" style="margin-top:12px">
                    @csrf
                    <button type="submit" class="btn {{ $isFavorited ? 'btn-outline' : '' }}">
                        {{ $isFavorited ? '★ 已收藏' : '☆ 收藏商户' }}
                    </button>
                </form>
            @endauth
        </section>

        @if ($deals->isNotEmpty())
        <section class="panel">
            <h2>团购优惠</h2>
            @foreach ($deals as $deal)
                <div style="border-bottom:1px solid var(--border);padding:12px 0">
                    <a href="{{ route('deals.show', $deal) }}" style="font-size:14px">{{ $deal->title }}</a>
                    <div style="margin-top:6px">
                        <span class="price">¥{{ $deal->price }}</span>
                        <span style="color:var(--muted);text-decoration:line-through;font-size:12px;margin-left:6px">¥{{ $deal->original_price }}</span>
                    </div>
                </div>
            @endforeach
        </section>
        @endif
    </div>
</div>
@endsection
