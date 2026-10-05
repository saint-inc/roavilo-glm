@extends('layouts.app')
@section('title', '联系我们')

@section('content')
<section class="panel">
    <h2>联系我们</h2>
    <div class="grid grid-2">
        <div class="contact-card">
            <h3>🏢 商务合作</h3>
            <p class="hotline">bd@roavilo-glm.com</p>
            <p class="muted">商户入驻、团购上线、广告推广等业务合作</p>
        </div>
        <div class="contact-card">
            <h3>🛠️ 技术与开发者</h3>
            <p class="hotline">dev@roavilo-glm.com</p>
            <p class="muted">API 接入、数据合作、技术问题反馈</p>
        </div>
        <div class="contact-card">
            <h3>📰 媒体采访</h3>
            <p class="hotline">press@roavilo-glm.com</p>
            <p class="muted">媒体报道、品牌合作、活动采访</p>
        </div>
        <div class="contact-card">
            <h3>👤 用户支持</h3>
            <p class="hotline"><a href="{{ route('pages.kf') }}" style="color:var(--primary)">客服中心 →</a></p>
            <p class="muted">账号、订单、退款等用户问题请前往客服中心</p>
        </div>
    </div>
</section>
@endsection
