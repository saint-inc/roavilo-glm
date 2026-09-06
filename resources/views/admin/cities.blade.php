@extends('layouts.admin')
@section('title', '城市管理')

@section('content')
<section class="panel">
    <h2>新增城市</h2>
    {{-- 创建城市表单：名称/拼音 slug/是否热门 --}}
    <form action="{{ route('admin.cities.store') }}" method="POST" class="filter-bar">
        @csrf
        <input type="text" name="name" placeholder="城市名（如：苏州）" required style="max-width:140px;padding:6px 10px;border:1px solid var(--border);border-radius:4px">
        <input type="text" name="slug" placeholder="拼音（如：suzhou）" required style="max-width:140px;padding:6px 10px;border:1px solid var(--border);border-radius:4px">
        <label style="display:flex;align-items:center;gap:4px;font-size:13px">
            <input type="checkbox" name="is_hot" value="1" style="width:auto"> 热门城市
        </label>
        <button type="submit" class="btn btn-sm">添加</button>
    </form>
</section>

<section class="panel">
    <h2>城市列表</h2>
    <table class="admin-table">
        <tr><th>ID</th><th>城市名</th><th>slug</th><th>热门</th><th>操作</th></tr>
        @foreach ($cities as $city)
            <tr>
                <td>{{ $city->id }}</td>
                <td>{{ $city->name }}</td>
                <td>{{ $city->slug }}</td>
                <td>@if ($city->is_hot)<span class="badge badge-orange">热门</span>@else<span class="badge badge-gray">否</span>@endif</td>
                <td>
                    {{-- 删除城市（有商户时后端会拒绝） --}}
                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="inline-form"
                          onsubmit="return confirm('确定删除该城市？')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline">删除</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</section>
@endsection
