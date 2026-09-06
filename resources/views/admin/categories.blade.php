@extends('layouts.admin')
@section('title', '分类管理')

@section('content')
<section class="panel">
    <h2>新增分类</h2>
    {{-- 创建分类表单：可选父级（二级分类） --}}
    <form action="{{ route('admin.categories.store') }}" method="POST" class="filter-bar">
        @csrf
        <input type="text" name="name" placeholder="分类名称" required style="max-width:160px;padding:6px 10px;border:1px solid var(--border);border-radius:4px">
        <select name="parent_id" style="padding:6px;border:1px solid var(--border);border-radius:4px">
            <option value="">作为一级分类</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">作为「{{ $cat->name }}」的子分类</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm">添加</button>
    </form>
</section>

<section class="panel">
    <h2>分类列表</h2>
    <table class="admin-table">
        <tr><th>ID</th><th>名称</th><th>子分类</th><th>操作</th></tr>
        @foreach ($categories as $cat)
            <tr>
                <td>{{ $cat->id }}</td>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->children->pluck('name')->implode('、') ?: '-' }}</td>
                <td>
                    {{-- 删除分类（有子分类/商户时后端会拒绝） --}}
                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline-form"
                          onsubmit="return confirm('确定删除该分类？')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline">删除</button>
                    </form>
                </td>
            </tr>
            @foreach ($cat->children as $child)
                <tr>
                    <td>{{ $child->id }}</td>
                    <td style="padding-left:30px">└ {{ $child->name }}</td>
                    <td>-</td>
                    <td>
                        <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="inline-form"
                              onsubmit="return confirm('确定删除该分类？')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline">删除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>
</section>
@endsection
