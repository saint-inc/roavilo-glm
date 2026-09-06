@extends('layouts.app')
@section('title', '团购管理')

@section('content')
<div class="user-layout">
    @include('merchant._sidebar', ['shop' => $shop])
    <section class="panel">
        <h2>团购管理</h2>
        <p style="margin-bottom:14px"><a href="{{ route('merchant.deals.create', $shop) }}" class="btn">+ 发布新团购</a></p>
        @forelse ($deals as $deal)
            <div class="review-item">
                <div class="review-header">
                    <span style="font-size:14px;font-weight:bold">{{ $deal->title }}</span>
                    <span style="margin-left:auto;font-size:12px">
                        @if ($deal->status === 1)<span style="color:#1c7c3c">上架中</span>
                        @else<span style="color:#999">已下架</span>@endif
                    </span>
                </div>
                <p class="review-content" style="color:var(--muted);font-size:13px">
                    <span class="price">¥{{ $deal->price }}</span>
                    <span style="text-decoration:line-through;margin-left:6px">¥{{ $deal->original_price }}</span>
                    · 库存 {{ $deal->stock }} · 已售 {{ $deal->sold_count }}
                    @if ($deal->ends_at) · 至 {{ $deal->ends_at->format('Y-m-d') }}@endif
                </p>
                <div style="margin-top:8px;display:flex;gap:10px">
                    <a href="{{ route('merchant.deals.edit', [$shop, $deal]) }}" class="btn btn-outline" style="padding:4px 12px;font-size:13px">编辑</a>
                    {{-- 上架/下架切换 --}}
                    <form action="{{ route('merchant.deals.toggle', [$shop, $deal]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $deal->status === 1 ? 'btn-outline' : '' }}" style="padding:4px 12px;font-size:13px">
                            {{ $deal->status === 1 ? '下架' : '上架' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p>还没有发布团购</p>
        @endforelse
        {{ $deals->links() }}
    </section>
</div>
@endsection
