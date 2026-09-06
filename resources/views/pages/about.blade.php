@extends('layouts.app')
@section('title', '关于我们')

@section('content')
<section class="panel">
    <h2>关于 roavilo</h2>
    <div class="about-content">
        <p><strong>roavilo.com</strong> 是一家本地生活服务平台，致力于帮助用户发现身边的好店。</p>
        <p>我们提供<strong>商户信息浏览、真实用户点评、团购优惠、城市生活指南</strong>等服务，覆盖美食、休闲娱乐、丽人、酒店、亲子等生活全品类。</p>

        <h3>我们的使命</h3>
        <p>让每一次消费决策都有据可依 —— 帮 3 亿城市用户找到值得信赖的本地好店。</p>

        <h3>平台特色</h3>
        <ul>
            <li><strong>真实点评</strong>：来自真实消费者的消费体验分享，一人一店一评，杜绝水军</li>
            <li><strong>团购优惠</strong>：商户直供套餐，凭核销码到店使用，便捷安心</li>
            <li><strong>全城覆盖</strong>：全国主要城市陆续开通，本地生活一网打尽</li>
            <li><strong>三端体验</strong>：电脑、iPad、手机全端适配，随时随地发现好店</li>
        </ul>

        <h3>联系我们</h3>
        <p>商务合作：bd@roavilo.com ｜ 用户支持：<a href="{{ route('pages.kf') }}" style="color:var(--primary)">客服中心</a></p>
    </div>
</section>
@endsection
