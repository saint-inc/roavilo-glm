@extends('layouts.app')
@section('title', '登录')

@section('content')
<section class="panel" style="max-width:480px;margin:40px auto">
    <h2>登录 roavilo</h2>
    <form action="/login" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required style="max-width:100%">
        </div>
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" name="password" id="password" required style="max-width:100%">
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:8px">
            <input type="checkbox" name="remember" id="remember" style="width:auto">
            <label for="remember" style="margin:0">记住我</label>
        </div>
        <button type="submit" class="btn" style="width:100%;max-width:100%">登录</button>
        <p style="margin-top:12px;font-size:13px;color:var(--muted)">还没有账号？<a href="/register" style="color:var(--primary)">立即注册</a> · <a href="/forgot-password" style="color:var(--primary)">忘记密码？</a></p>
    </form>
</section>
@endsection
