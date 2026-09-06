@extends('layouts.app')
@section('title', '重置密码')

@section('content')
<section class="panel" style="max-width:480px;margin:40px auto">
    <h2>设置新密码</h2>
    {{-- 重置密码表单：token 与 email 由 URL 携带 --}}
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required style="max-width:100%">
        </div>
        <div class="form-group">
            <label for="password">新密码（至少 8 位）</label>
            <input type="password" name="password" id="password" required style="max-width:100%">
        </div>
        <div class="form-group">
            <label for="password_confirmation">确认新密码</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required style="max-width:100%">
        </div>
        <button type="submit" class="btn" style="width:100%;max-width:100%">重置密码</button>
    </form>
</section>
@endsection
