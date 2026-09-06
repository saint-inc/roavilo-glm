<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Deal;
use App\Models\DealOrder;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * 管理后台控制器
 * 仅管理员可访问（路由层 admin 中间件校验 is_admin）
 * 功能：数据总览、店铺审核、用户管理、点评管理、分类/城市管理
 */
class AdminController extends Controller
{
    /**
     * 后台总览看板
     * 统计：用户数、商户数、点评数、订单数、待审核店铺数
     */
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'shops' => Shop::count(),
            'reviews' => Review::count(),
            'orders' => DealOrder::count(),
            'pendingShops' => Shop::where('status', 2)->count(),
            'deals' => Deal::count(),
        ];
        // 待审核店铺列表（首页快捷展示）
        $pendingShops = Shop::where('status', 2)->with('city', 'category')->latest()->take(10)->get();

        return view('admin.index', compact('stats', 'pendingShops'));
    }

    /**
     * 店铺管理列表
     * 支持 status 筛选（1营业 2待审核 0下架）
     */
    public function shops(Request $request)
    {
        $query = Shop::with('city', 'category', 'owner')->latest();

        if ($request->query('status') !== null && $request->query('status') !== '') {
            $query->where('status', $request->integer('status'));
        }

        $shops = $query->paginate(15)->withQueryString();

        return view('admin.shops', compact('shops'));
    }

    /**
     * 审核店铺：通过（上架）
     */
    public function approveShop(Shop $shop)
    {
        $shop->update(['status' => 1]);

        return back()->with('success', "「{$shop->name}」已审核通过并上架");
    }

    /**
     * 审核店铺：驳回 / 下架
     */
    public function rejectShop(Shop $shop)
    {
        $shop->update(['status' => 0]);

        return back()->with('success', "「{$shop->name}」已被驳回/下架");
    }

    /**
     * 用户管理列表（支持关键词搜索）
     */
    public function users(Request $request)
    {
        $query = User::latest();

        if ($keyword = $request->query('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * 切换用户的管理员身份
     */
    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => ! $user->is_admin]);

        return back()->with('success', "「{$user->name}」管理员身份已".($user->is_admin ? '开启' : '关闭'));
    }

    /**
     * 点评管理列表（支持关键词筛选，可删除违规点评）
     */
    public function reviews(Request $request)
    {
        $query = Review::with('shop', 'user')->latest();

        if ($keyword = $request->query('keyword')) {
            $query->where('content', 'like', "%{$keyword}%");
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews', compact('reviews'));
    }

    /**
     * 删除违规点评，并重算对应商户的评分
     */
    public function destroyReview(Review $review)
    {
        $shop = $review->shop;
        $review->delete();

        // 重算商户评分与点评数
        $agg = Review::where('shop_id', $shop->id)->selectRaw('COUNT(*) c, AVG(rating) r')->first();
        $shop->update([
            'rating_count' => $agg->c,
            'rating' => round($agg->r ?? 0, 1),
        ]);

        return back()->with('success', '点评已删除');
    }

    /**
     * 分类管理列表
     */
    public function categories()
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort')->get();

        return view('admin.categories', compact('categories'));
    }

    /**
     * 创建分类
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($validated + ['sort' => Category::max('sort') + 1]);

        return back()->with('success', '分类已创建');
    }

    /**
     * 删除分类（有子分类或商户时禁止删除）
     */
    public function destroyCategory(Category $category)
    {
        if ($category->children()->exists() || $category->shops()->exists()) {
            return back()->withErrors(['name' => '该分类下存在子分类或商户，无法删除']);
        }

        $category->delete();

        return back()->with('success', '分类已删除');
    }

    /**
     * 城市管理列表
     */
    public function cities()
    {
        $cities = City::orderBy('sort')->get();

        return view('admin.cities', compact('cities'));
    }

    /**
     * 创建城市
     */
    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:cities,name',
            'slug' => 'required|string|max:50|unique:cities,slug|alpha_dash',
            'is_hot' => 'nullable|boolean',
        ]);

        City::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_hot' => $request->boolean('is_hot'),
            'sort' => City::max('sort') + 1,
        ]);

        return back()->with('success', '城市已创建');
    }

    /**
     * 删除城市（有商户时禁止删除）
     */
    public function destroyCity(City $city)
    {
        if ($city->shops()->exists()) {
            return back()->withErrors(['name' => '该城市下存在商户，无法删除']);
        }

        $city->delete();

        return back()->with('success', '城市已删除');
    }
}
