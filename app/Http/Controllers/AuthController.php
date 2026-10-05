<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 认证控制器
 * 负责用户注册、登录、退出
 */
class AuthController extends Controller
{
    /**
     * 登录表单页
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * 处理登录请求
     * 凭证验证通过后 regenerate session 防止会话固定攻击
     */
    public function login(Request $request)
    {
        // 表单校验
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 是否勾选"记住我"
        $remember = $request->boolean('remember');

        // 尝试登录
        if (Auth::attempt($credentials, $remember)) {
            // 重新生成 session id（安全：防会话固定）
            $request->session()->regenerate();

            // intended()：优先跳转到登录前想访问的页面，否则回首页
            return redirect()->intended(route('home'));
        }

        // 凭证错误：回登录页并提示
        return back()->withErrors(['email' => '邮箱或密码错误'])->onlyInput('email');
    }

    /**
     * 注册表单页
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * 处理注册请求
     * 注册成功后自动登录
     */
    public function register(Request $request)
    {
        // 表单校验：邮箱唯一，密码至少 8 位且需二次确认
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 创建用户（密码由模型 casts 自动哈希）
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // 注册后自动登录并重新生成 session
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', '注册成功，欢迎加入 roavilo-glm！');
    }

    /**
     * 退出登录
     * 清除认证状态、作废 session、重新生成 CSRF token
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
