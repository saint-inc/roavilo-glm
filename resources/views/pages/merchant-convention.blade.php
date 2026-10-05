@extends('layouts.app')
@section('title', '商户诚信公约')

@section('content')
<section class="panel">
    <h2>roavilo-glm 商户诚信公约</h2>
    <div class="about-content">
        <p>为营造真实可信的本地生活消费环境，入驻 roavilo-glm 的商户须遵守以下公约：</p>
        <h3>一、信息真实</h3>
        <ul>
            <li>店铺名称、地址、电话、营业时间等基本信息必须真实准确</li>
            <li>团购套餐的内容、价格、有效期须如实标注，不得虚假宣传</li>
        </ul>
        <h3>二、诚信经营</h3>
        <ul>
            <li>不得刷单、刷评、雇佣水军操纵评分</li>
            <li>不得诱导用户修改或删除真实差评</li>
            <li>按承诺提供服务，不得到店加价或降低套餐标准</li>
        </ul>
        <h3>三、用户至上</h3>
        <ul>
            <li>及时核销用户团购券，不得无故拒收</li>
            <li>积极回应用户点评与投诉，妥善处理消费纠纷</li>
        </ul>
        <h3>四、违规处理</h3>
        <p>违反公约的商户，平台将视情节采取警告、下架团购、封禁店铺等措施；情节严重并造成用户损失的，平台将配合用户依法维权。</p>
        <p>举报违规商户：<a href="{{ route('pages.kf') }}" style="color:var(--primary)">客服中心 → 意见反馈 → 投诉举报</a></p>
    </div>
</section>
@endsection
