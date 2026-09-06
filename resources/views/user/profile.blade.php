@extends('layouts.app')
@section('title', '个人资料')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <section class="panel">
        <h2>个人资料</h2>
        {{-- 资料编辑表单：enctype 支持头像文件上传 --}}
        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">昵称</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}">
            </div>
            <div class="form-group">
                <label for="avatar">上传头像（图片，最大 2MB）</label>
                <input type="file" name="avatar" id="avatar" accept="image/*" style="max-width:100%">
                @if ($user->avatar)
                    {{-- 已有头像预览 --}}
                    <p style="margin-top:8px"><img src="{{ $user->avatar }}" alt="头像" style="width:64px;height:64px;border-radius:50%;object-fit:cover"></p>
                @endif
            </div>
            <div class="form-group">
                <label for="avatar_url">或填写头像 URL（选填）</label>
                <input type="text" name="avatar_url" id="avatar_url" value="{{ old('avatar_url') }}" placeholder="https://…">
            </div>
            <div class="form-group">
                <label for="bio">个性签名（选填）</label>
                <textarea name="bio" id="bio" style="min-height:80px">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <button type="submit" class="btn">保存</button>
        </form>
    </section>
</div>
@endsection
