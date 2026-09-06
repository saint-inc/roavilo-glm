@extends('layouts.app')
@section('title', 'App 下载')

@section('content')
<section class="panel app-hero">
    <div class="app-info">
        <h2>roavilo App</h2>
        <p style="color:var(--muted);margin:10px 0">发现身边好店 · 随时随地</p>
        <ul style="font-size:14px;line-height:2;color:#555;list-style:none">
            <li>📍 基于位置的周边好店推荐</li>
            <li>🎟️ 团购券掌上管理，扫码核销</li>
            <li>✍️ 随手拍、随手评，分享消费体验</li>
            <li>🔔 优惠券到期、订单状态实时提醒</li>
        </ul>
        <div class="app-btns">
            <span class="btn" style="opacity:.6;cursor:not-allowed">🍎 App Store（即将上线）</span>
            <span class="btn btn-outline" style="opacity:.6;cursor:not-allowed">🤖 Android 下载（即将上线）</span>
        </div>
        <p style="font-size:12px;color:var(--muted);margin-top:12px">App 正在开发中，当前可使用手机浏览器访问 m.roavilo.com 获得完整移动端体验。</p>
    </div>
</section>
@endsection
