@extends('layouts.app')
@section('title', '人才招聘')

@section('content')
<section class="panel">
    <h2>加入 roavilo-glm</h2>
    <p style="color:var(--muted);font-size:14px;margin-bottom:16px">
        我们正在寻找热爱本地生活的小伙伴，一起打造值得信赖的消费决策平台。简历投递：hr@roavilo-glm.com
    </p>
    @foreach ($jobs as $job)
        <div class="job-item">
            <div class="job-head">
                <strong>{{ $job['title'] }}</strong>
                <span class="job-meta">{{ $job['dept'] }} · {{ $job['location'] }} · {{ $job['type'] }}</span>
            </div>
            <p class="job-req">{{ $job['req'] }}</p>
            <a href="mailto:hr@roavilo-glm.com?subject=应聘：{{ $job['title'] }}" class="btn btn-outline" style="padding:4px 14px;font-size:13px">投递简历</a>
        </div>
    @endforeach
</section>
@endsection
