<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealOrder;
use App\Models\Shop;
use Illuminate\Http\Request;

/**
 * 商户中心控制器
 * 负责商户入驻、店铺管理、数据看板、团购管理、订单核销（需登录）
 */
class MerchantController extends Controller
{
    /**
     * 我的店铺列表
     */
    public function index(Request $request)
    {
        // 查询当前用户名下的所有店铺（预加载城市/分类）
        $shops = Shop::where('owner_id', $request->user()->id)->with('city', 'category')->get();

        return view('merchant.index', compact('shops'));
    }

    /**
     * 商户入驻申请表单页
     */
    public function create()
    {
        return view('merchant.create');
    }

    /**
     * 提交入驻申请
     * 新店铺默认为「待审核」状态，需管理员审核后上架
     */
    public function store(Request $request)
    {
        // 表单校验：名称/分类/地址必填，其余选填
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'avg_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'business_hours' => 'nullable|string|max:100',
        ]);

        // 补充归属信息：申请人、当前城市（session）
        $validated['owner_id'] = $request->user()->id;
        $validated['city_id'] = $request->session()->get('city_id', 1);
        // 生成唯一 slug（名称转 slug + 6 位随机串，避免重名冲突）
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']).'-'.\Illuminate\Support\Str::random(6);
        $validated['status'] = 2; // 2 = 待审核

        Shop::create($validated);

        return redirect()->route('merchant.index')->with('success', '店铺已提交，等待审核');
    }

    /**
     * 校验当前用户是否为指定店铺的所有者（越权保护）
     */
    private function authorizeShop(Request $request, Shop $shop): void
    {
        abort_unless($shop->owner_id === $request->user()->id, 403, '无权管理此店铺');
    }

    /**
     * 店铺数据看板
     * 统计：浏览量、点评数、评分、团购销量、订单数
     */
    public function dashboard(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        // 统计团购总销量与团购数量（聚合查询）
        $dealStats = Deal::where('shop_id', $shop->id)
            ->selectRaw('COALESCE(SUM(sold_count),0) sold, COUNT(*) cnt')
            ->first();
        // 已支付/已使用的订单数
        $paidOrders = DealOrder::whereHas('deal', fn ($q) => $q->where('shop_id', $shop->id))
            ->whereIn('status', [1, 2])
            ->count();
        // 最近 7 天新增点评数
        $recentReviews = $shop->reviews()->where('created_at', '>=', now()->subDays(7))->count();

        return view('merchant.dashboard', compact('shop', 'dealStats', 'paidOrders', 'recentReviews'));
    }

    /**
     * 编辑店铺信息表单页
     */
    public function edit(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        return view('merchant.edit', compact('shop'));
    }

    /**
     * 保存店铺信息修改
     */
    public function update(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'avg_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'business_hours' => 'nullable|string|max:100',
        ]);

        $shop->update($validated);

        return redirect()->route('merchant.dashboard', $shop)->with('success', '店铺信息已更新');
    }

    /**
     * 商户回复某条点评（以店铺身份）
     * 写入 review_replies 表，内容前缀【商家回复】
     */
    public function replyReview(Request $request, Shop $shop, \App\Models\Review $review)
    {
        $this->authorizeShop($request, $shop);
        abort_unless($review->shop_id === $shop->id, 403);

        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $review->replies()->create([
            'user_id' => $request->user()->id,
            'content' => '【商家回复】'.$validated['content'],
        ]);
        $review->increment('reply_count');

        return back()->with('success', '回复成功');
    }

    /**
     * 店铺的点评管理页（供商户查看并回复）
     */
    public function reviews(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        $reviews = $shop->reviews()->with('user', 'replies')->paginate(10);

        return view('merchant.reviews', compact('shop', 'reviews'));
    }

    /**
     * 店铺的团购管理页
     */
    public function deals(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        $deals = $shop->deals()->latest()->paginate(10);

        return view('merchant.deals', compact('shop', 'deals'));
    }

    /**
     * 创建团购表单页
     */
    public function createDeal(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        return view('merchant.deal-form', ['shop' => $shop, 'deal' => null]);
    }

    /**
     * 保存新团购
     */
    public function storeDeal(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        $validated = $this->validateDeal($request);
        $validated['shop_id'] = $shop->id;

        Deal::create($validated);

        return redirect()->route('merchant.deals', $shop)->with('success', '团购已创建');
    }

    /**
     * 编辑团购表单页
     */
    public function editDeal(Request $request, Shop $shop, Deal $deal)
    {
        $this->authorizeShop($request, $shop);
        abort_unless($deal->shop_id === $shop->id, 403);

        return view('merchant.deal-form', ['shop' => $shop, 'deal' => $deal]);
    }

    /**
     * 保存团购修改
     */
    public function updateDeal(Request $request, Shop $shop, Deal $deal)
    {
        $this->authorizeShop($request, $shop);
        abort_unless($deal->shop_id === $shop->id, 403);

        $deal->update($this->validateDeal($request));

        return redirect()->route('merchant.deals', $shop)->with('success', '团购已更新');
    }

    /**
     * 团购上架/下架切换
     */
    public function toggleDeal(Request $request, Shop $shop, Deal $deal)
    {
        $this->authorizeShop($request, $shop);
        abort_unless($deal->shop_id === $shop->id, 403);

        $deal->update(['status' => $deal->status === 1 ? 0 : 1]);

        return back()->with('success', $deal->fresh()->status ? '团购已上架' : '团购已下架');
    }

    /**
     * 团购表单公共校验规则
     * 团购价不得高于原价，结束时间不得早于开始时间
     */
    private function validateDeal(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'original_price' => 'required|numeric|min:0.01',
            'price' => 'required|numeric|min:0.01|lte:original_price',
            'stock' => 'required|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
    }

    /**
     * 店铺的团购订单列表（含核销码）
     */
    public function orders(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        // 该店铺所有团购的订单，按时间倒序，预加载团购与买家
        $orders = DealOrder::whereHas('deal', fn ($q) => $q->where('shop_id', $shop->id))
            ->with(['deal', 'user'])
            ->latest()
            ->paginate(15);

        return view('merchant.orders', compact('shop', 'orders'));
    }

    /**
     * 核销团购订单
     * 凭核销码将「已支付」订单标记为「已使用」
     */
    public function verifyOrder(Request $request, Shop $shop)
    {
        $this->authorizeShop($request, $shop);

        $validated = $request->validate([
            'verify_code' => 'required|string|size:12',
        ]);

        // 查找属于该店铺、状态为已支付、核销码匹配的订单
        $order = DealOrder::where('verify_code', $validated['verify_code'])
            ->where('status', 1)
            ->whereHas('deal', fn ($q) => $q->where('shop_id', $shop->id))
            ->first();

        if (! $order) {
            return back()->withErrors(['verify_code' => '核销码无效或订单状态不允许核销']);
        }

        // 标记为已使用并记录时间
        $order->update(['status' => 2, 'used_at' => now()]);

        return back()->with('success', "订单 {$order->order_no} 核销成功");
    }
}
