@extends('layouts.app')
@section('title', '选择城市')

@section('content')
<section class="panel">
    <h2>当前城市：{{ $currentCity->name }}</h2>
    {{-- 热门城市快捷切换 --}}
    <h3 class="city-group-title">热门城市</h3>
    <div class="city-grid">
        @foreach ($hotCities as $city)
            {{-- 点击即切换到该城市首页；当前城市高亮 --}}
            <a href="{{ url('/'.$city->slug) }}" class="city-item hot {{ $city->id === $currentCity->id ? 'current' : '' }}">{{ $city->name }}</a>
        @endforeach
    </div>
</section>

<section class="panel city-list-panel">
    <h2>全部城市</h2>
    <div class="city-layout">
        {{-- 左侧字母快速导航（锚点跳转） --}}
        <nav class="city-letters">
            @foreach ($groupedCities->keys() as $letter)
                <a href="#letter-{{ $letter }}">{{ $letter }}</a>
            @endforeach
        </nav>
        {{-- 右侧按字母分组的城市列表 --}}
        <div class="city-groups">
            @foreach ($groupedCities as $letter => $cities)
                <div id="letter-{{ $letter }}" class="city-group">
                    <h3 class="city-group-title">{{ $letter }}</h3>
                    <div class="city-grid">
                        @foreach ($cities as $city)
                            <a href="{{ url('/'.$city->slug) }}" class="city-item {{ $city->id === $currentCity->id ? 'current' : '' }}">{{ $city->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
