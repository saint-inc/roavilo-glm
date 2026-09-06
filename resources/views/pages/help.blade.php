@extends('layouts.app')
@section('title', '帮助中心')

@section('content')
<section class="panel">
    <h2>帮助中心</h2>
    <p style="color:var(--muted);font-size:13px;margin-bottom:14px">找不到答案？<a href="{{ route('pages.kf') }}" style="color:var(--primary)">联系客服中心</a></p>
    @foreach ($faqs as $category => $items)
        <h3 class="faq-cat">{{ $category }}</h3>
        @foreach ($items as [$q, $a])
            <details class="faq-item">
                <summary>{{ $q }}</summary>
                <p>{{ $a }}</p>
            </details>
        @endforeach
    @endforeach
</section>
@endsection
