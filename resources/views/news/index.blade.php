@extends('layouts.app')
@section('title', '最新资讯')

@section('content')
<section class="panel">
    <h2>最新资讯</h2>
    {{-- 分类筛选 --}}
    <div class="filter-bar">
        <span>分类：</span>
        <a href="{{ route('news.index') }}" @if(!request('category')) class="active" @endif>全部</a>
        @foreach (\App\Models\News::CATEGORY_LABELS as $k => $label)
            <a href="{{ route('news.index', ['category' => $k]) }}" @if(request('category') == $k) class="active" @endif>{{ $label }}</a>
        @endforeach
    </div>
    <div class="news-list">
        @forelse ($news as $item)
            <a href="{{ route('news.show', $item) }}" class="news-item">
                <div>
                    <h3>{{ $item->title }}</h3>
                    <p class="news-summary">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                    <p class="news-meta">
                        <span class="news-tag">{{ \App\Models\News::CATEGORY_LABELS[$item->category] ?? $item->category }}</span>
                        @if ($item->source)<span>来源：{{ $item->source }}</span>@endif
                        <span>{{ $item->created_at->format('Y-m-d') }}</span>
                        <span>👁 {{ $item->view_count }}</span>
                    </p>
                </div>
            </a>
        @empty
            <p>暂无资讯</p>
        @endforelse
    </div>
    {{ $news->links() }}
</section>
@endsection
