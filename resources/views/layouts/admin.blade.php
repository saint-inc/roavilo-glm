{{-- 管理后台布局（独立于前台） --}}
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '后台') - roavilo 管理后台</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* 后台专属样式：深色侧边栏布局 */
        .admin-layout { display: grid; grid-template-columns: 200px 1fr; min-height: calc(100vh - 60px); }
        .admin-side { background: #2b2b2b; padding: 16px 10px; }
        .admin-side a { display: block; color: #ccc; padding: 10px 14px; border-radius: 6px; font-size: 14px; margin-bottom: 2px; }
        .admin-side a.active, .admin-side a:hover { background: var(--primary); color: #fff; }
        .admin-main { padding: 20px; }
        .admin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; font-size: 14px; }
        .admin-table th, .admin-table td { padding: 10px 14px; border-bottom: 1px solid var(--border); text-align: left; }
        .admin-table th { background: #fafafa; font-size: 13px; color: var(--muted); }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; }
        .badge-green { background: #e8f8ee; color: #1c7c3c; }
        .badge-orange { background: #fff4e5; color: #c07b1c; }
        .badge-gray { background: #f0f0f0; color: #999; }
        .btn-sm { padding: 4px 12px; font-size: 13px; }
        @media (max-width: 768px) { .admin-layout { grid-template-columns: 1fr; } .admin-side { display: flex; flex-wrap: wrap; } }
    </style>
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo">roavilo</a>
        <span style="color:var(--muted);font-size:13px">管理后台</span>
        <div class="header-right">
            <span style="font-size:13px">{{ auth()->user()->name }}</span>
            <form action="/logout" method="POST" class="inline-form">@csrf<button type="submit" class="link-btn">退出</button></form>
        </div>
    </div>
</header>
<div class="admin-layout">
    <aside class="admin-side">
        <a href="{{ route('admin.index') }}">数据总览</a>
        <a href="{{ route('admin.shops') }}">店铺管理</a>
        <a href="{{ route('admin.users') }}">用户管理</a>
        <a href="{{ route('admin.reviews') }}">点评管理</a>
        <a href="{{ route('admin.categories') }}">分类管理</a>
        <a href="{{ route('admin.cities') }}">城市管理</a>
    </aside>
    <main class="admin-main">
        @if (session('success'))
            <div class="alert alert-success">{!! session('success') !!}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">@foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
