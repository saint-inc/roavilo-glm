@extends('layouts.app')
@section('title', '编辑店铺')

@section('content')
<div class="user-layout">
    @include('merchant._sidebar', ['shop' => $shop])
    <section class="panel">
        <h2>编辑店铺信息</h2>
        {{-- 编辑表单：分类与城市不可改，其余信息可修改 --}}
        <form action="{{ route('merchant.update', $shop) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">店铺名称</label>
                <input type="text" name="name" id="name" value="{{ old('name', $shop->name) }}" required>
            </div>
            <div class="form-group">
                <label for="address">店铺地址</label>
                <input type="text" name="address" id="address" value="{{ old('address', $shop->address) }}" required>
            </div>
            <div class="form-group">
                <label for="phone">联系电话</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $shop->phone) }}">
            </div>
            <div class="form-group">
                <label for="avg_price">人均消费（元）</label>
                <input type="number" step="0.01" name="avg_price" id="avg_price" value="{{ old('avg_price', $shop->avg_price) }}">
            </div>
            <div class="form-group">
                <label for="business_hours">营业时间</label>
                <input type="text" name="business_hours" id="business_hours" value="{{ old('business_hours', $shop->business_hours) }}">
            </div>
            <div class="form-group">
                <label for="description">店铺介绍</label>
                <textarea name="description" id="description">{{ old('description', $shop->description) }}</textarea>
            </div>
            <button type="submit" class="btn">保存修改</button>
        </form>
    </section>
</div>
@endsection
