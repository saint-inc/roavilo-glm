<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

/**
 * 个人中心控制器
 * 负责个人中心总览、我的点评、个人资料展示与修改（需登录）
 */
class UserController extends Controller
{

    /**
     * 个人中心总览
     * 统计：点评数、收藏数、订单数
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 三项统计数据（用于首页统计卡片展示）
        $reviewCount = $user->reviews()->count();
        $favoriteCount = $user->favorites()->count();
        $orderCount = $user->orders()->count();

        return view('user.index', compact('user', 'reviewCount', 'favoriteCount', 'orderCount'));
    }

    /**
     * 我的点评列表（按发布时间倒序，每页 10 条）
     */
    public function reviews(Request $request)
    {
        // 预加载点评所属商户信息
        $reviews = $request->user()->reviews()->with('shop')->latest()->paginate(10);

        return view('user.reviews', compact('reviews'));
    }

    /**
     * 个人资料编辑页
     */
    public function profile(Request $request)
    {
        return view('user.profile', ['user' => $request->user()]);
    }

    /**
     * 保存个人资料（昵称/头像/个性签名）
     * 头像支持文件上传（存 storage/app/public/avatars）或直接填 URL
     */
    public function updateProfile(Request $request)
    {
        // 校验：头像可选文件（图片类型，最大 2MB）或 URL 字符串
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'avatar_url' => 'nullable|url|max:500',
            'bio' => 'nullable|string|max:200',
        ]);

        $data = ['name' => $validated['name'], 'bio' => $validated['bio'] ?? null];

        // 优先处理文件上传的头像
        if ($request->hasFile('avatar')) {
            // 存储到 storage/app/public/avatars，返回相对路径
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/'.$path;
        } elseif (! empty($validated['avatar_url'])) {
            // 否则使用用户填写的头像 URL
            $data['avatar'] = $validated['avatar_url'];
        }

        $request->user()->update($data);

        return back()->with('success', '资料已更新');
    }
}
