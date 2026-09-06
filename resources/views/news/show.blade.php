@extends('layouts.app')
@section('title', $news->title)

@section('content')
<div class="shop-detail">
    <div>
        <section class="panel">
            <h2>{{ $news->title }}</h2>
            <p style="font-size:12px;color:var(--muted);margin-bottom:16px">
                <span class="news-tag">{{ \App\Models\News::CATEGORY_LABELS[$news->category] ?? $news->category }}</span>
                @if ($news->source)来源：{{ $news->source }} · @endif
                {{ $news->created_at->format('Y-m-d H:i') }} · 👁 {{ $news->view_count }} 次阅读
            </p>
            {{-- 正文按段落渲染 --}}
            <div class="news-content">
                @foreach (explode("\n", $news->content) as $para)
                    @if (trim($para) !== '')<p>{{ $para }}</p>@endif
                @endforeach
            </div>
        </section>
    </div>
    <div>
        <section class="panel">
            <h2>相关阅读</h2>
            @forelse ($related as $item)
                <div style="border-bottom:1px solid var(--border);padding:10px 0">
                    <a href="{{ route('news.show', $item) }}" style="font-size:14px">{{ $item->title }}</a>
                    <p style="font-size:12px;color:var(--muted);margin-top:4px">{{ $item->created_at->format('Y-m-d') }}</p>
                </div>
            @empty
                <p>暂无相关内容</p>
            @endforelse
            <p style="margin-top:12px"><a href="{{ route('news.index') }}" style="color:var(--primary);font-size:13px">← 返回资讯列表</a></p>
        </section>
    </div>
</div>
@endsection
