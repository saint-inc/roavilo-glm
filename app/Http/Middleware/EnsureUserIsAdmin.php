<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 管理员中间件
 * 校验当前登录用户是否为管理员（is_admin），否则 403
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user() && $request->user()->is_admin, 403, '仅管理员可访问');

        return $next($request);
    }
}
