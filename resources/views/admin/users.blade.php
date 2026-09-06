@extends('layouts.admin')
@section('title', '用户管理')

@section('content')
<section class="panel">
    <h2>用户管理</h2>
    {{-- 关键词搜索 --}}
    <form method="GET" class="filter-bar">
        <input type="text" name="keyword" placeholder="搜索昵称/邮箱" value="{{ request('keyword') }}" style="max-width:240px;padding:6px 10px;border:1px solid var(--border);border-radius:4px">
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>
    <table class="admin-table">
        <tr><th>ID</th><th>昵称</th><th>邮箱</th><th>注册时间</th><th>角色</th><th>操作</th></tr>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                <td>
                    @if ($user->is_admin)<span class="badge badge-green">管理员</span>
                    @else<span class="badge badge-gray">普通用户</span>@endif
                </td>
                <td>
                    {{-- 切换管理员身份 --}}
                    <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline-form">@csrf
                        <button class="btn btn-sm {{ $user->is_admin ? 'btn-outline' : '' }}">
                            {{ $user->is_admin ? '取消管理员' : '设为管理员' }}
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    {{ $users->links() }}
</section>
@endsection
