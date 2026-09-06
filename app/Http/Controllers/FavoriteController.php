<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

/**
 * 收藏控制器
 * 负责商户收藏/取消收藏、我的收藏列表（需登录）
 */
class FavoriteController extends Controller
{
    /**
     * 收藏 / 取消收藏商户（toggle 切换）
     */
    public function toggle(Request $request, Shop $shop)
    {
        $user = $request->user();
        // 查询该用户是否已收藏此商户
        $existing = $shop->favorites()->where('user_id', $user->id)->first();

        if ($existing) {
            // 已收藏 → 取消收藏
            $existing->delete();
            $message = '已取消收藏';
        } else {
            // 未收藏 → 添加收藏
            $shop->favorites()->create(['user_id' => $user->id]);
            $message = '收藏成功';
        }

        return back()->with('success', $message);
    }

    /**
     * 我的收藏列表（分页展示）
     */
    public function index(Request $request)
    {
        // 预加载收藏的商户及其城市/分类信息，避免 N+1 查询
        $shops = $request->user()->favorites()->with('shop.city', 'shop.category')->paginate(12);

        return view('user.favorites', compact('shops'));
    }
}
