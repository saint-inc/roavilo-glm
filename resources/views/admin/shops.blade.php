@extends('layouts.admin')
@section('title', '店铺管理')

@section('content')
<section class="panel">
    <h2>店铺管理</h2>
    {{-- 状态筛选 --}}
    <div class="filter-bar">
        <span>状态：</span>
        <a href="{{ route('admin.shops') }}" @if (request('status') === null) class="active" @endif>全部</a>
        <a href="{{ route('admin.shops', ['status' => 2]) }}" @if (request('status') == '2') class="active" @endif>待审核</a>
        <a href="{{ route('admin.shops', ['status' => 1]) }}" @if (request('status') == '1') class="active" @endif>营业中</a>
        <a href="{{ route('admin.shops', ['status' => 0]) }}" @if (request('status') == '0') class="active" @endif>已下架</a>
    </div>
    <table class="admin-table">
        <tr><th>店铺</th><th>城市/分类</th><th>店主</th><th>评分</th><th>状态</th><th>操作</th></tr>
        @foreach ($shops as $shop)
            <tr>
                <td><a href="{{ route('shops.show', $shop) }}" style="color:var(--primary)">{{ $shop->name }}</a></td>
                <td>{{ $shop->city?->name }} / {{ $shop->category?->name }}</td>
                <td>{{ $shop->owner?->name ?? '-' }}</td>
                <td>{{ $shop->rating }}（{{ $shop->rating_count }}）</td>
                <td>
                    @if ($shop->status === 1)<span class="badge badge-green">营业中</span>
                    @elseif ($shop->status === 2)<span class="badge badge-orange">待审核</span>
                    @else<span class="badge badge-gray">已下架</span>@endif
                </td>
                <td style="white-space:nowrap">
                    @if ($shop->status !== 1)
                        {{-- 上架（审核通过） --}}
                        <form action="{{ route('admin.shops.approve', $shop) }}" method="POST" class="inline-form">@csrf
                            <button class="btn btn-sm">上架</button>
                        </form>
                    @else
                        {{-- 下架（驳回） --}}
                        <form action="{{ route('admin.shops.reject', $shop) }}" method="POST" class="inline-form">@csrf
                            <button class="btn btn-sm btn-outline">下架</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    {{ $shops->links() }}
</section>
@endsection
