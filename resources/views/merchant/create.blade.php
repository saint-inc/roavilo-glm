@extends('layouts.app')
@section('title', '商户入驻')

@section('content')
<div class="user-layout">
    @include('user._sidebar')
    <section class="panel">
        <h2>商户入驻</h2>
        <form action="{{ route('merchant.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">店铺名称</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="category_id">经营类别</label>
                <select name="category_id" id="category_id" required>
                    @foreach (\App\Models\Category::whereNull('parent_id')->orderBy('sort')->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="address">店铺地址</label>
                <input type="text" name="address" id="address" value="{{ old('address') }}" required>
            </div>
            <div class="form-group">
                <label for="phone">联系电话</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
                <label for="avg_price">人均消费（元）</label>
                <input type="number" step="0.01" name="avg_price" id="avg_price" value="{{ old('avg_price') }}">
            </div>
            <div class="form-group">
                <label for="business_hours">营业时间</label>
                <input type="text" name="business_hours" id="business_hours" placeholder="如 10:00 - 22:00" value="{{ old('business_hours') }}">
            </div>
            <div class="form-group">
                <label for="description">店铺介绍</label>
                <textarea name="description" id="description">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn">提交入驻申请</button>
        </form>
    </section>
</div>
@endsection
