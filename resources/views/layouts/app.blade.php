<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '首页') - roavilo-glm</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo">roavilo-glm</a>
        {{-- 当前城市 + 切换城市入口（对标点评平台顶栏城市切换） --}}
        <a href="{{ route('city.list') }}" class="city-switch">📍 {{ $currentCity->name ?? '上海' }} ▾</a>
        <nav class="main-nav">
            <a href="{{ route('home') }}">首页</a>
            {{-- 商户导航：指向当前城市首页（分类在首页/城市页切换） --}}
            <a href="{{ url('/'.($currentCity->slug ?? 'shanghai')) }}">商户</a>
            <a href="{{ route('deals.index') }}">团购</a>
        </nav>
        <div class="header-right">
            {{-- 搜索表单：对标 /search/keyword/{城市ID}/0_关键词 结构 --}}
            <form action="{{ url('/search/keyword/'.($currentCity->id ?? 1).'/0_') }}" method="get" class="search-form">
                <input type="text" name="keyword" placeholder="搜索商户、美食…" value="{{ request('keyword') }}">
                <button type="submit">搜索</button>
            </form>
            @auth
                {{-- 登录态：用户头像 + 下拉菜单（个人中心/收藏/订单/商户中心/退出） --}}
                <div class="user-menu" tabindex="0">
                    <span class="user-menu-trigger">
                        <span class="avatar avatar-sm">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        {{ auth()->user()->name }} ▾
                    </span>
                    <div class="user-dropdown">
                        <a href="{{ route('user.index') }}">我的主页</a>
                        <a href="{{ route('user.reviews') }}">我的点评</a>
                        <a href="{{ route('user.favorites') }}">我的收藏</a>
                        <a href="{{ route('user.orders') }}">我的订单</a>
                        <a href="{{ route('user.profile') }}">个人资料</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('merchant.index') }}">商户中心</a>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.index') }}" style="color:var(--primary)">管理后台</a>
                        @endif
                        <form action="/logout" method="POST">@csrf<button type="submit" class="dropdown-logout">退出登录</button></form>
                    </div>
                </div>
            @else
                {{-- 非登录态：登录/注册按钮 --}}
                <a href="/login">登录</a>
                <a href="/register" class="btn-primary">注册</a>
            @endauth
        </div>
    </div>
</header>
@auth
    {{-- 登录态：欢迎条（对标点评平台登录后的提示条） --}}
    <div class="welcome-bar">
        <div class="container">
            你好，{{ auth()->user()->name }}！欢迎来到 roavilo-glm，发现身边好店 ✨
            <a href="{{ route('user.reviews') }}" style="color:var(--primary);margin-left:auto;font-size:13px">查看我的点评 →</a>
        </div>
    </div>
@else
    {{-- 非登录态：引导登录条 --}}
    <div class="welcome-bar welcome-bar-guest">
        <div class="container">
            登录 roavilo-glm，收藏好店、发布点评、抢购团购 🎁
            <span style="margin-left:auto;display:flex;gap:8px">
                <a href="/login" class="btn" style="padding:4px 16px">立即登录</a>
                <a href="/register" class="btn btn-outline" style="padding:4px 16px">免费注册</a>
            </span>
        </div>
    </div>
@endauth
<main class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container">
        <p>© {{ date('Y') }} roavilo-glm.com — 本地生活服务平台</p>
        <p>
            <a href="{{ route('pages.about') }}">关于我们</a> ·
            <a href="{{ route('pages.contact') }}">联系我们</a> ·
            <a href="{{ route('pages.help') }}">帮助中心</a> ·
            <a href="{{ route('pages.kf') }}">客服中心</a> ·
            <a href="{{ route('merchant.create') }}">商户入驻</a> ·
            <a href="{{ route('pages.promote') }}">推广服务</a> ·
            <a href="{{ route('pages.merchant-convention') }}">商户诚信公约</a> ·
            <a href="{{ route('news.index') }}">最新资讯</a> ·
            <a href="{{ route('pages.jobs') }}">人才招聘</a> ·
            <a href="{{ route('pages.agreement') }}">用户协议</a> ·
            <a href="{{ route('pages.privacy') }}">隐私政策</a> ·
            <a href="{{ route('pages.app') }}">应用下载</a>
        </p>
    </div>
</footer>

{{-- 移动端底部导航（仅手机端显示，对标 m 站 Tab 栏交互） --}}
<nav class="mobile-tabbar">
    <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home') || request()->routeIs('city.home')])>
        <span class="tab-icon">🏠</span><span>首页</span>
    </a>
    <a href="{{ url('/'.($currentCity->slug ?? 'shanghai').'/ch1') }}" @class(['active' => request()->routeIs('shops.category')])>
        <span class="tab-icon">🏪</span><span>商户</span>
    </a>
    <a href="{{ route('deals.index') }}" @class(['active' => request()->routeIs('deals.*')])>
        <span class="tab-icon">🎟️</span><span>团购</span>
    </a>
    @auth
        {{-- 登录态：进入个人中心 --}}
        <a href="{{ route('user.index') }}" @class(['active' => request()->routeIs('user.*')])>
            <span class="tab-icon">👤</span><span>我的</span>
        </a>
    @else
        {{-- 非登录态：引导登录 --}}
        <a href="/login" @class(['active' => request()->routeIs('login')])>
            <span class="tab-icon">🔑</span><span>登录</span>
        </a>
    @endauth
</nav>

{{-- 回到顶部按钮（滚动超过一屏后出现） --}}
<button id="back-top" title="回到顶部" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>
{{-- 搜索联想脚本：输入 2 字以上触发下拉提示 --}}
<script>
(function () {
    var input = document.querySelector('.search-form input');
    if (!input) return;
    var box = document.createElement('div');
    box.style.cssText = 'position:absolute;background:#fff;border:1px solid #e5e5e5;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.1);display:none;z-index:99;min-width:220px';
    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(box);
    var timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        var kw = this.value.trim();
        if (kw.length < 1) { box.style.display = 'none'; return; }
        // 300ms 防抖后请求联想接口
        timer = setTimeout(function () {
            fetch('{{ route("search.suggest") }}?keyword=' + encodeURIComponent(kw))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.suggestions.length) { box.style.display = 'none'; return; }
                    box.innerHTML = data.suggestions.map(function (s) {
                        return '<a href="' + s.url + '" style="display:block;padding:8px 12px;font-size:13px">' + s.name + '</a>';
                    }).join('');
                    box.style.display = 'block';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!box.contains(e.target) && e.target !== input) box.style.display = 'none';
    });
})();

// 回到顶部按钮：滚动超过 400px 显示
(function () {
    var btn = document.getElementById('back-top');
    if (!btn) return;
    window.addEventListener('scroll', function () {
        btn.style.display = window.scrollY > 400 ? 'block' : 'none';
    });
})();
</script>
</body>
</html>
