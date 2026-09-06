<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Region;
use App\Models\Shop;
use App\Services\CityService;
use Illuminate\Http\Request;

/**
 * 商户控制器
 * URL 结构对标点评类平台：
 * - /{城市拼音}/ch{分类ID}          → 分类商户列表（如 /shanghai/ch1）
 * - /search/keyword/{城市ID}/0_关键词 → 关键词搜索结果
 * - /shop/{商户ID}                  → 商户详情
 */
class ShopController extends Controller
{
    /**
     * 分类商户列表页
     * URL 结构对标点评平台：
     * - /{城市}/ch{分类ID}            → 全区域列表（如 /shanghai/ch1）
     * - /{城市}/ch{分类ID}/g{区域ID}   → 区域筛选列表（如 /shanghai/ch1/g2）
     * 同时兼容 query 参数 ?region=（旧链接与排序跳转使用）
     */
    public function category(Request $request, string $citySlug, int $categoryId, ?int $regionId = null)
    {
        // 按城市拼音解析城市
        $city = City::where('slug', $citySlug)->firstOrFail();
        $request->session()->put('city_id', $city->id);

        // 分类校验：不存在则 404
        $category = Category::whereNull('parent_id')->findOrFail($categoryId);

        // 区域：优先路径参数 g{regionId}，其次 query 参数
        $regionId = $regionId ?: $request->query('region');

        // 区域存在性校验（不属于该城市的区域返回 404）
        $region = $regionId ? Region::where('city_id', $city->id)->findOrFail($regionId) : null;

        return $this->renderList($request, $city,
            array_filter(['category' => $category->id, 'region' => $region?->id]),
            $category, $region);
    }

    /**
     * 关键词搜索结果页（如 /search/keyword/1/0_小笼包）
     * 关键词来源：URL 路径中 "0_" 之后的部分，或 query 参数 keyword
     */
    public function search(Request $request, int $cityId, ?string $filters = null)
    {
        // 按城市 ID 解析城市，不存在则回退到当前城市
        $city = City::find($cityId) ?? CityService::resolve($request);
        $request->session()->put('city_id', $city->id);

        // 解析路径中的关键词（格式 "0_关键词"，0_ 后为空则取 query 参数）
        $keyword = '';
        if ($filters !== null && str_contains($filters, '0_')) {
            $keyword = urldecode(substr($filters, strpos($filters, '0_') + 2));
        }
        $keyword = trim($request->query('keyword', $keyword));

        return $this->renderList($request, $city, $keyword !== '' ? ['keyword' => $keyword] : []);
    }

    /**
     * 商户详情页（如 /shop/1）
     * 展示：商户基本信息、团购、点评列表（分页+筛选）、收藏状态
     * 点评筛选 rf（all/good/mid/bad）、排序 r_sort（new/old/hot）
     */
    public function show(Request $request, Shop $shop)
    {
        // 预加载关联，减少查询次数
        $shop->load(['city', 'category', 'region', 'images']);

        // 浏览量 +1
        $shop->increment('view_count');

        // ---------- 点评查询（支持筛选与排序，对标点评平台详情页） ----------
        $reviewQuery = $shop->reviews()->with('user');

        // 评分档位筛选：good 好评(4-5星) / mid 中评(3星) / bad 差评(1-2星)
        switch ($request->query('rf', 'all')) {
            case 'good':
                $reviewQuery->where('rating', '>=', 4);
                break;
            case 'mid':
                $reviewQuery->where('rating', '=', 3);
                break;
            case 'bad':
                $reviewQuery->where('rating', '<=', 2);
                break;
        }

        // 点评排序：hot 按点赞数 / old 按时间正序 / 默认最新
        switch ($request->query('r_sort', 'new')) {
            case 'hot':
                $reviewQuery->orderByDesc('like_count');
                break;
            case 'old':
                $reviewQuery->oldest();
                break;
            default:
                $reviewQuery->latest();
        }

        $reviews = $reviewQuery->paginate(10)->withQueryString();

        // ---------- 星级分布统计（1~5 星各多少条，用于评分分布条） ----------
        $distribution = [];
        for ($star = 1; $star <= 5; $star++) {
            $distribution[$star] = $shop->reviews()
                ->where('rating', '>=', $star)->where('rating', '<', $star + 1)
                ->count();
        }

        // 该商户上架中的团购
        $deals = $shop->deals()->active()->get();

        // 当前登录用户是否已收藏该商户
        $isFavorited = $request->user()
            ? $shop->favorites()->where('user_id', $request->user()->id)->exists()
            : false;

        return view('shops.show', compact('shop', 'reviews', 'deals', 'isFavorited', 'distribution'));
    }

    /**
     * 商户列表渲染（分类页与搜索页共用）
     * $filters 支持键：keyword 关键词、category 分类
     * 排序通过 query 参数 sort 控制
     */
    private function renderList(Request $request, City $city, array $filters = [], ?Category $category = null, ?Region $region = null)
    {
        // 基础查询：当前城市 + 上架状态的商户，预加载分类与区域
        $query = Shop::active()->where('city_id', $city->id)->with(['category', 'region']);

        // 关键词搜索：匹配商户名称或地址
        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        // 分类筛选
        if (! empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        // 区域筛选：filters 中的 region（分类页路径 g 参数解析结果）；搜索页回退 query 参数
        $regionFilter = $filters['region'] ?? $request->query('region');
        if ($regionFilter) {
            $query->where('region_id', $regionFilter);
        }

        // 排序：rating 评分 / price_asc 人均升序 / price_desc 人均降序 / default 浏览量
        switch ($request->query('sort', 'default')) {
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'price_asc':
                $query->orderBy('avg_price');
                break;
            case 'price_desc':
                $query->orderByDesc('avg_price');
                break;
            default:
                $query->orderByDesc('view_count');
        }

        // 分页（每页 20 条），withQueryString 保留筛选参数
        $shops = $query->paginate(20)->withQueryString();

        // 筛选条件数据：一级分类 + 当前城市的区域
        $categories = Category::whereNull('parent_id')->orderBy('sort')->get();
        $regions = Region::where('city_id', $city->id)->orderBy('sort')->get();

        // 当前搜索关键词（用于页面标题与回显）
        $keyword = $filters['keyword'] ?? '';

        return view('shops.index', compact('city', 'shops', 'categories', 'regions', 'category', 'keyword', 'region'));
    }
}
