@extends('layouts.app')
@section('title', '客服中心')

@section('content')
{{-- 联系方式（对标点评客服中心：会员/商户双热线结构） --}}
<section class="panel">
    <h2>服务中心 · 联系我们</h2>
    <div class="grid grid-2">
        <div class="contact-card">
            <h3>👤 会员服务热线</h3>
            <p class="hotline">400-100-1100</p>
            <p class="muted">工作时间：周一至周日 8:00 - 22:00</p>
            <p class="muted">服务范围：账号问题、点评咨询、订单退款、意见受理</p>
        </div>
        <div class="contact-card">
            <h3>🏪 商户服务热线</h3>
            <p class="hotline">400-100-1101</p>
            <p class="muted">工作时间：周一至周日 9:00 - 21:00</p>
            <p class="muted">服务范围：入驻咨询、团购上线、核销问题、推广合作</p>
        </div>
    </div>
</section>

{{-- 常见问题 --}}
<section class="panel">
    <h2>常见问题</h2>
    @foreach ($faqs as $category => $items)
        <h3 class="faq-cat">{{ $category }}</h3>
        @foreach ($items as [$q, $a])
            <details class="faq-item">
                <summary>{{ $q }}</summary>
                <p>{{ $a }}</p>
            </details>
        @endforeach
    @endforeach
    <p style="margin-top:10px;font-size:13px">更多问题请查看 <a href="{{ route('pages.help') }}" style="color:var(--primary)">帮助中心</a></p>
</section>

{{-- 意见反馈表单 --}}
<section class="panel">
    <h2>意见反馈</h2>
    <form action="{{ route('pages.feedback') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="type">反馈类型</label>
            <select name="type" id="type">
                @foreach (\App\Models\Feedback::TYPE_LABELS as $k => $label)
                    <option value="{{ $k }}" @if(old('type') === $k) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required>
        </div>
        <div class="form-group">
            <label for="content">反馈内容</label>
            <textarea name="content" id="content" placeholder="请详细描述您遇到的问题或建议（至少 5 个字）">{{ old('content') }}</textarea>
        </div>
        <div class="form-group">
            <label for="name">称呼（选填）</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()?->name) }}">
        </div>
        <div class="form-group">
            <label for="contact">联系方式（游客必填，方便我们回复您）</label>
            <input type="text" name="contact" id="contact" value="{{ old('contact', auth()->user()?->email) }}" placeholder="手机号或邮箱">
            @error('contact')<p style="color:#c0392b;font-size:12px">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn">提交反馈</button>
    </form>
</section>
@endsection
