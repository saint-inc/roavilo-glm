@extends('layouts.app')
@section('title', '服务器错误')

@section('content')
<section class="panel" style="max-width:600px;margin:60px auto;text-align:center">
    <div style="font-size:72px;font-weight:800;color:var(--primary)">500</div>
    <p style="font-size:16px;margin:16px 0">服务器开小差了，请稍后再试。</p>
    <p><a href="{{ route('home') }}" class="btn">返回首页</a></p>
</section>
@endsection
