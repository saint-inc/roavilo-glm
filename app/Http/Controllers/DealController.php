<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Services\CityService;
use Illuminate\Http\Request;

/**
 * 团购控制器
 * 负责团购列表（/t，对应 t.xxx.com 子域）与团购详情（/deal/{id}）
 */
class DealController extends Controller
{
    /**
     * 团购列表页
     * 展示当前城市的上架团购，按销量倒序分页
     */
    public function index(Request $request)
    {
        $city = CityService::resolve($request);

        // 当前城市商户的上架团购，按销量倒序，每页 20 条
        $deals = Deal::active()
            ->whereHas('shop', fn ($q) => $q->where('city_id', $city->id))
            ->with('shop')
            ->orderByDesc('sold_count')
            ->paginate(20);

        return view('deals.index', compact('city', 'deals'));
    }

    /**
     * 团购详情页
     * 展示团购信息（价格/介绍/有效期）及所属商户
     */
    public function show(Deal $deal)
    {
        // 预加载所属商户信息
        $deal->load('shop');

        return view('deals.show', compact('deal'));
    }
}

