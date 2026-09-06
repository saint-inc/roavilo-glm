{{-- 商户中心：单店铺管理侧边栏 --}}
<aside class="user-side">
    <div style="font-weight:bold;font-size:13px;color:var(--muted);padding:6px 12px">{{ $shop->name }}</div>
    <a href="{{ route('merchant.dashboard', $shop) }}">数据看板</a>
    <a href="{{ route('merchant.edit', $shop) }}">店铺信息</a>
    <a href="{{ route('merchant.reviews', $shop) }}">点评管理</a>
    <a href="{{ route('merchant.deals', $shop) }}">团购管理</a>
    <a href="{{ route('merchant.orders', $shop) }}">订单与核销</a>
    <div style="border-top:1px solid var(--border);margin:10px 0"></div>
    <a href="{{ route('merchant.index') }}">← 返回店铺列表</a>
</aside>
