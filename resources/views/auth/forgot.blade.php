@extends('layouts.app')
@section('title', '找回密码')

@section('content')
<section class="panel" style="max-width:480px;margin:40px auto">
    <h2>找回密码</h2>
    {{-- 输入注册邮箱生成重置链接 --}}
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">注册邮箱</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required style="max-width:100%">
        </div>
        <button type="submit" class="btn" style="width:100%;max-width:100%">发送重置链接</button>
    </form>
    <p style="margin-top:12px;font-size:13px;color:var(--muted)"><a href="/login" style="color:var(--primary)">返回登录</a></p>
</section>
@endsection
