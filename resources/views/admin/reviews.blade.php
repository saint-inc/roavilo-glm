@extends('layouts.admin')
@section('title', '点评管理')

@section('content')
<section class="panel">
    <h2>点评管理</h2>
    {{-- 内容关键词筛选 --}}
    <form method="GET" class="filter-bar">
        <input type="text" name="keyword" placeholder="搜索点评内容" value="{{ request('keyword') }}" style="max-width:240px;padding:6px 10px;border:1px solid var(--border);border-radius:4px">
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>
    <table class="admin-table">
        <tr><th>ID</th><th>用户</th><th>商户</th><th>评分</th><th>内容</th><th>时间</th><th>操作</th></tr>
        @foreach ($reviews as $review)
            <tr>
                <td>{{ $review->id }}</td>
                <td>{{ $review->user?->name }}</td>
                <td><a href="{{ route('shops.show', $review->shop_id) }}" style="color:var(--primary)">{{ $review->shop?->name }}</a></td>
                <td>{{ $review->rating }}★</td>
                <td style="max-width:300px">{{ Str::limit($review->content, 50) }}</td>
                <td>{{ $review->created_at->format('Y-m-d') }}</td>
                <td>
                    {{-- 删除违规点评 --}}
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline-form"
                          onsubmit="return confirm('确定删除该点评？')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline">删除</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    {{ $reviews->links() }}
</section>
@endsection
