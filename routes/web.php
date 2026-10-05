<?php

/**
 * Web 路由定义 — roavilo-glm 本地生活服务平台
 *
 * URL 路径结构对标点评类平台（dianping 风格）：
 * - /                                → 根路径，跳转当前城市首页
 * - /{城市拼音}（如 /shanghai）        → 城市首页
 * - /{城市拼音}/ch{分类ID}             → 分类商户列表（如 /shanghai/ch1）
 * - /search/keyword/{城市ID}/0_关键词  → 关键词搜索结果
 * - /shop/{商户ID}                    → 商户详情
 * - /deal/{团购ID}                    → 团购详情
 * - /t                                → 团购列表（对应 t.xxx.com 子域）
 *
 * 旧版 URL（/shops、/deals 等）均 301 跳转到新 URL，保证兼容。
 */

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Services\CityService;
use Illuminate\Support\Facades\Route;

// ---------------- 认证（公开） ----------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 找回密码
Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// 搜索联想接口（前端下拉提示用）
Route::get('/api/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

// ---------------- 站点信息页与客服中心（对标点评平台 footer 全部链接） ----------------
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/help', [PageController::class, 'help'])->name('pages.help');
Route::get('/kf', [PageController::class, 'kf'])->name('pages.kf');
Route::post('/kf/feedback', [PageController::class, 'submitFeedback'])->name('pages.feedback');
Route::get('/app', [PageController::class, 'app'])->name('pages.app');
Route::get('/agreement', [PageController::class, 'agreement'])->name('pages.agreement');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/copyright', [PageController::class, 'copyright'])->name('pages.copyright');
Route::get('/merchant-convention', [PageController::class, 'merchantConvention'])->name('pages.merchant-convention');
Route::get('/promote', [PageController::class, 'promote'])->name('pages.promote');
Route::get('/jobs', [PageController::class, 'jobs'])->name('pages.jobs');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');

// 资讯栏目（对标"最新资讯/媒体报道"）
Route::get('/news', [PageController::class, 'newsList'])->name('news.index');
Route::get('/news/{news}', [PageController::class, 'newsShow'])->name('news.show');

// union 子域网关欢迎响应（union.roavilo-glm.com，对应 union.dianping.com）
Route::get('/union-gateway', [PageController::class, 'unionGateway'])->name('pages.union');

// ---------------- 前台核心页面（公开，URL 对标点评平台） ----------------

// 根路径：跳转到当前城市首页（如 / → /shanghai）
Route::get('/', [HomeController::class, 'root'])->name('home');

// 关键词搜索（如 /search/keyword/1/0_小笼包；filters 可为空）
Route::get('/search/keyword/{cityId}/{filters?}', [ShopController::class, 'search'])
    ->where('filters', '.*')->name('shops.search');

// 商户详情（如 /shop/123）与点评提交
Route::get('/shop/{shop}', [ShopController::class, 'show'])->name('shops.show');
Route::post('/shop/{shop}/review', [ReviewController::class, 'store'])->name('reviews.store');

// 团购列表与详情（/t 对应 t.xxx.com 子域）
Route::get('/t', [DealController::class, 'index'])->name('deals.index');
Route::get('/deal/{deal}', [DealController::class, 'show'])->name('deals.show');
Route::post('/deal/{deal}/order', [OrderController::class, 'store'])->name('orders.store');

// ---------------- 旧版 URL 兼容跳转（301） ----------------
Route::get('/shops', fn () => redirect('/'.CityService::resolve(request())->slug, 301));
Route::get('/shops/{id}', fn (string $id) => redirect("/shop/{$id}", 301))->whereNumber('id');
Route::get('/deals', fn () => redirect('/t', 301));
Route::get('/deals/{id}', fn (string $id) => redirect("/deal/{$id}", 301))->whereNumber('id');

// 城市选择页（全部城市列表；需在 /city/{slug} 跳转之前注册）
Route::get('/city', [HomeController::class, 'cityList'])->name('city.list');
// 旧版城市切换跳转
Route::get('/city/{slug}', fn (string $slug) => redirect('/'.$slug, 301));

// ---------------- 登录用户（auth 中间件） ----------------
Route::middleware(['auth'])->group(function () {
    // 点评互动
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    // 商户收藏（对标 /shop/{id}/fav）
    Route::post('/shop/{shop}/fav', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/shops/{shop}/favorite', [FavoriteController::class, 'toggle']);
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('user.favorites');

    // 订单
    Route::get('/orders', [OrderController::class, 'index'])->name('user.orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');

    // 个人中心
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/reviews', [UserController::class, 'reviews'])->name('user.reviews');
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/user/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

    // 商户中心
    Route::get('/merchant', [MerchantController::class, 'index'])->name('merchant.index');
    Route::get('/merchant/create', [MerchantController::class, 'create'])->name('merchant.create');
    Route::post('/merchant', [MerchantController::class, 'store'])->name('merchant.store');

    // 商户中心：店铺管理（看板/编辑/点评回复/团购管理/订单核销）
    Route::prefix('merchant/shops/{shop}')->group(function () {
        Route::get('/dashboard', [MerchantController::class, 'dashboard'])->name('merchant.dashboard');
        Route::get('/edit', [MerchantController::class, 'edit'])->name('merchant.edit');
        Route::put('/edit', [MerchantController::class, 'update'])->name('merchant.update');
        Route::get('/reviews', [MerchantController::class, 'reviews'])->name('merchant.reviews');
        Route::post('/reviews/{review}/reply', [MerchantController::class, 'replyReview'])->name('merchant.review.reply');
        Route::get('/deals', [MerchantController::class, 'deals'])->name('merchant.deals');
        Route::get('/deals/create', [MerchantController::class, 'createDeal'])->name('merchant.deals.create');
        Route::post('/deals', [MerchantController::class, 'storeDeal'])->name('merchant.deals.store');
        Route::get('/deals/{deal}/edit', [MerchantController::class, 'editDeal'])->name('merchant.deals.edit');
        Route::put('/deals/{deal}', [MerchantController::class, 'updateDeal'])->name('merchant.deals.update');
        Route::post('/deals/{deal}/toggle', [MerchantController::class, 'toggleDeal'])->name('merchant.deals.toggle');
        Route::get('/orders', [MerchantController::class, 'orders'])->name('merchant.orders');
        Route::post('/orders/verify', [MerchantController::class, 'verifyOrder'])->name('merchant.orders.verify');
    });
});

// 管理后台（需登录 + 管理员权限）
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // 店铺审核与管理
    Route::get('/shops', [AdminController::class, 'shops'])->name('admin.shops');
    Route::post('/shops/{shop}/approve', [AdminController::class, 'approveShop'])->name('admin.shops.approve');
    Route::post('/shops/{shop}/reject', [AdminController::class, 'rejectShop'])->name('admin.shops.reject');

    // 用户管理
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');

    // 点评管理
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::delete('/reviews/{review}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');

    // 分类管理
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');

    // 城市管理
    Route::get('/cities', [AdminController::class, 'cities'])->name('admin.cities');
    Route::post('/cities', [AdminController::class, 'storeCity'])->name('admin.cities.store');
    Route::delete('/cities/{city}', [AdminController::class, 'destroyCity'])->name('admin.cities.destroy');
});


// ---------------- 城市首页与分类列表（catch-all，必须放在所有路由最后） ----------------
// 城市首页（如 /shanghai）
Route::get('/{city}', [HomeController::class, 'cityHome'])
    ->where('city', '[a-z]+')->name('city.home');
// 分类商户列表（如 /shanghai/ch1；区域筛选 /shanghai/ch1/g2 对标点评平台 g 路径）
Route::get('/{city}/ch{categoryId}/g{regionId}', [ShopController::class, 'category'])
    ->where(['city' => '[a-z]+', 'categoryId' => '[0-9]+', 'regionId' => '[0-9]+'])->name('shops.category.region');
Route::get('/{city}/ch{categoryId}', [ShopController::class, 'category'])
    ->where(['city' => '[a-z]+', 'categoryId' => '[0-9]+'])->name('shops.category');
