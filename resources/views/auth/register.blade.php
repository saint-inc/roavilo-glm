@extends('layouts.app')
@section('title', '注册')

@section('content')
<section class="panel" style="max-width:480px;margin:40px auto">
    <h2>注册 roavilo-glm 账号</h2>
    <form action="/register" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">昵称</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required style="max-width:100%">
        </div>
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required style="max-width:100%">
        </div>
        <div class="form-group">
            <label for="password">密码（至少 8 位）</label>
            <input type="password" name="password" id="password" required style="max-width:100%">
        </div>
        <div class="form-group">
            <label for="password_confirmation">确认密码</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required style="max-width:100%">
        </div>
        <button type="submit" class="btn" style="width:100%;max-width:100%">注册</button>
        <p style="margin-top:12px;font-size:13px;color:var(--muted)">已有账号？<a href="/login" style="color:var(--primary)">去登录</a></p>
    </form>
</section>
@endsection
