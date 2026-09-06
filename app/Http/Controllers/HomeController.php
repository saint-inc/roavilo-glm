<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Deal;
use App\Models\Shop;
use App\Services\CityService;
use Illuminate\Http\Request;

/**
 * 首页控制器
 * URL 结构对标点评类平台：
 * - /            → 跳转到当前城市首页
 * - /{城市拼音}   → 城市首页（如 /shanghai）
 */
class HomeController extends Controller
{
    /**
     * 城市选择页（/city，展示全部城市供切换）
     * 热门城市 + 按拼音首字母分组的全部城市（对标点评平台城市切换页结构）
     */
    public function cityList(Request $request)
    {
        $hotCities = City::where('is_hot', true)->orderBy('sort')->get();
        $groupedCities = \App\Services\CityService::groupedByLetter();
        // 当前城市（视图顶部展示与高亮用）
        $currentCity = \App\Services\CityService::resolve($request);

        return view('city.index', compact('hotCities', 'groupedCities', 'currentCity'));
    }

    /**
     * 根路径：跳转到当前城市的首页（如 / → /shanghai）
     */
    public function root(Request $request)
    {
        return redirect('/'.CityService::resolve($request)->slug);
    }

    /**
     * 城市首页（如 /shanghai、/beijing）
     * 展示：分类导航、热门商户、热门团购、最新点评、热门城市
     */
    public function cityHome(Request $request, string $slug)
    {
        // 按 slug 查找城市，不存在则 404（catch-all 路由误匹配保护）
        $city = City::where('slug', $slug)->first();
        if (! $city) {
            abort(404);
        }
        // 记住用户选择的城市（session + cookie 双写，跨会话记忆）
        \App\Services\CityService::remember($request, $city);
        $request->session()->put('city_id', $city->id);

        // 一级分类列表（用于首页分类导航条）
        $categories = Category::whereNull('parent_id')->orderBy('sort')->get();

        // 热门商户：当前城市、上架状态，按评分+浏览量倒序取前 8
        $hotShops = Shop::active()
            ->where('city_id', $city->id)
            ->with('category')
            ->orderByDesc('rating')
            ->orderByDesc('view_count')
            ->take(8)
            ->get();

        // 最新点评：当前城市商户下的最新 6 条，预加载商户和用户避免 N+1
        $newReviews = \App\Models\Review::with(['shop', 'user'])
            ->whereHas('shop', fn ($q) => $q->where('city_id', $city->id))
            ->latest()
            ->take(6)
            ->get();

        // 热门团购：当前城市、上架状态，按销量倒序取前 6
        $hotDeals = Deal::active()
            ->whereHas('shop', fn ($q) => $q->where('city_id', $city->id))
            ->with('shop')
            ->orderByDesc('sold_count')
            ->take(6)
            ->get();

        // 热门城市列表（首页底部城市切换栏）
        $hotCities = City::where('is_hot', true)->orderBy('sort')->get();

        return view('home', compact('city', 'categories', 'hotShops', 'newReviews', 'hotDeals', 'hotCities'));
    }
}
