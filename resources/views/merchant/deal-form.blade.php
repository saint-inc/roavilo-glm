@extends('layouts.app')
@section('title', $deal ? '编辑团购' : '发布团购')

@section('content')
<div class="user-layout">
    @include('merchant._sidebar', ['shop' => $shop])
    <section class="panel">
        <h2>{{ $deal ? '编辑团购' : '发布新团购' }}</h2>
        {{-- 团购创建/编辑复用同一表单（$deal 为 null 时为创建） --}}
        <form action="{{ $deal ? route('merchant.deals.update', [$shop, $deal]) : route('merchant.deals.store', $shop) }}" method="POST">
            @csrf
            @if ($deal) @method('PUT') @endif
            <div class="form-group">
                <label for="title">团购标题</label>
                <input type="text" name="title" id="title" value="{{ old('title', $deal?->title) }}" required>
            </div>
            <div class="form-group">
                <label for="original_price">原价（元）</label>
                <input type="number" step="0.01" name="original_price" id="original_price" value="{{ old('original_price', $deal?->original_price) }}" required>
            </div>
            <div class="form-group">
                <label for="price">团购价（元，不得高于原价）</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $deal?->price) }}" required>
            </div>
            <div class="form-group">
                <label for="stock">库存（份）</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $deal?->stock ?? 100) }}" required>
            </div>
            <div class="form-group">
                <label for="starts_at">开始时间（选填）</label>
                <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $deal?->starts_at?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label for="ends_at">结束时间（选填）</label>
                <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at', $deal?->ends_at?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label for="description">团购说明</label>
                <textarea name="description" id="description">{{ old('description', $deal?->description) }}</textarea>
            </div>
            <button type="submit" class="btn">{{ $deal ? '保存修改' : '发布团购' }}</button>
        </form>
    </section>
</div>
@endsection
