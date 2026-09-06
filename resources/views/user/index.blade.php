@extends('layouts.app')
@section('title', '个人中心')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <div>
        <div class="stat-row">
            <div class="stat-box"><div class="num">{{ $reviewCount }}</div><div class="label">我的点评</div></div>
            <div class="stat-box"><div class="num">{{ $favoriteCount }}</div><div class="label">我的收藏</div></div>
            <div class="stat-box"><div class="num">{{ $orderCount }}</div><div class="label">我的订单</div></div>
        </div>
        <section class="panel" style="margin-top:16px">
            <h2>账号信息</h2>
            <p style="font-size:14px;line-height:2">
                <div class="avatar" style="margin-bottom:10px">{{ mb_substr($user->name, 0, 1) }}</div>
                {{ $user->name }}（{{ $user->email }}）<br>
                注册时间：{{ $user->created_at->format('Y-m-d') }}
            </p>
        </section>
    </div>
</div>
@endsection
