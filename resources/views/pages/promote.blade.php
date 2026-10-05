@extends('layouts.app')
@section('title', '推广服务')

@section('content')
<section class="panel">
    <h2>推广服务</h2>
    <div class="about-content">
        <p>roavilo-glm 为商户提供多种推广产品，帮助好店被更多潜在顾客发现：</p>

        <h3>🚀 站内推广</h3>
        <ul>
            <li><strong>搜索排名提升</strong>：让您的店铺在关键词搜索结果中获得优先展示</li>
            <li><strong>首页推荐位</strong>：城市首页热门商户位，海量曝光</li>
        </ul>

        <h3>🎟️ 团购运营支持</h3>
        <ul>
            <li>团购套餐策划建议与定价指导</li>
            <li>营销节点（节假日/店庆）联合活动</li>
        </ul>

        <h3>📊 数据服务</h3>
        <ul>
            <li>商户中心数据看板：浏览量、评分趋势、团购销量、订单核销一目了然</li>
        </ul>

        <h3>合作联系</h3>
        <p>商务热线：400-100-1101（9:00-21:00）｜ 邮箱：bd@roavilo-glm.com</p>
        <p style="margin-top:14px"><a href="{{ route('merchant.create') }}" class="btn">先去入驻店铺</a></p>
    </div>
</section>
@endsection
