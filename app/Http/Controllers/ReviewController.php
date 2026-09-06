<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 点评控制器
 * 负责点评的发布、点赞、回复（需登录，路由层已加 auth 中间件）
 */
class ReviewController extends Controller
{
    /**
     * 发布点评
     * 每个用户对每个商户只能有一条点评，重复提交则更新（updateOrCreate）
     * 发布后重新计算商户的评分和点评数
     */
    public function store(Request $request, Shop $shop)
    {
        // 参数校验：评分 1-5，内容 10-2000 字
        $validated = $request->validate([
            'rating' => 'required|numeric|between:1,5',
            'content' => 'required|string|min:10|max:2000',
            'cost' => 'nullable|numeric|min:0',
        ]);

        // 同一用户对同一商户的点评：存在则更新，不存在则创建
        $review = $shop->reviews()->updateOrCreate(
            ['user_id' => Auth::id()],
            $validated + ['like_count' => 0, 'reply_count' => 0]
        );

        // 重算商户评分（评分均值）与点评总数
        $this->recalcShopRating($shop);

        return back()->with('success', '点评发布成功！');
    }

    /**
     * 点赞 / 取消点赞（toggle 切换）
     */
    public function like(Request $request, Review $review)
    {
        $user = $request->user();
        if ($review->isLikedBy($user)) {
            // 已点赞 → 取消：删除点赞记录并减计数
            $review->likes()->where('user_id', $user->id)->delete();
            $review->decrement('like_count');
            $liked = false;
        } else {
            // 未点赞 → 点赞：创建记录并加计数
            $review->likes()->create(['user_id' => $user->id]);
            $review->increment('like_count');
            $liked = true;
        }

        return back()->with('success', $liked ? '已点赞' : '已取消点赞');
    }

    /**
     * 回复点评
     */
    public function reply(Request $request, Review $review)
    {
        // 回复内容校验：必填，最长 500 字
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        // 创建回复并更新点评的回复计数
        $review->replies()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);
        $review->increment('reply_count');

        return back()->with('success', '回复成功！');
    }

    /**
     * 重新计算商户评分
     * 汇总该商户全部点评：总数 + 评分平均值
     */
    private function recalcShopRating(Shop $shop): void
    {
        $agg = Review::where('shop_id', $shop->id)->selectRaw('COUNT(*) c, AVG(rating) r')->first();
        $shop->update([
            'rating_count' => $agg->c,
            'rating' => round($agg->r ?? 0, 1),
        ]);
    }
}
