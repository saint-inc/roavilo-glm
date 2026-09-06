<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * 密码找回控制器
 * 简化流程：输入注册邮箱 → 生成重置令牌 → 页面直接展示重置链接（本地/测试环境）
 * 生产环境接入邮件服务后改为发送邮件
 */
class PasswordResetController extends Controller
{
    /**
     * 找回密码表单页（输入邮箱）
     */
    public function requestForm()
    {
        return view('auth.forgot');
    }

    /**
     * 生成重置令牌
     * 使用 Laravel 内置 password_reset_tokens 表存储令牌
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => '该邮箱未注册']);
        }

        // 生成随机令牌并写入 password_reset_tokens 表（1 小时有效）
        $token = \Illuminate\Support\Str::random(64);
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // 本地环境：直接展示重置链接（生产环境应改为发送邮件）
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        return back()->with('success', "重置链接已生成（有效期 1 小时）：<a href=\"{$resetUrl}\">点击重置密码</a>");
    }

    /**
     * 重置密码表单页
     */
    public function resetForm(Request $request, string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->query('email')]);
    }

    /**
     * 保存新密码
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 校验令牌是否匹配且未过期（1 小时）
        $record = \DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $record || ! Hash::check($validated['token'], $record->token) || now()->diffInHours($record->created_at) >= 1) {
            return back()->withErrors(['email' => '重置链接无效或已过期']);
        }

        // 更新密码并清除令牌
        User::where('email', $validated['email'])->update([
            'password' => $validated['password'],
        ]);
        \DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('success', '密码已重置，请使用新密码登录');
    }
}
