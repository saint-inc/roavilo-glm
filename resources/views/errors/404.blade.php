@extends('layouts.app')
@section('title', '页面不存在')

@section('content')
<section class="panel" style="max-width:600px;margin:60px auto;text-align:center">
    <div style="font-size:72px;font-weight:800;color:var(--primary)">404</div>
    <p style="font-size:16px;margin:16px 0">抱歉，您访问的页面不存在或已被移除。</p>
    <p><a href="{{ route('home') }}" class="btn">返回首页</a></p>
</section>
@endsection
