<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// ---------------------------------------------------------------------
// 子域路径重写（对应 dianping 子域映射）
// 在请求对象创建之前重写 REQUEST_URI，保证路由按重写后的路径解析：
// - merchant.roavilo-glm.com/* → /merchant/*（商户中心，对应 e.dianping.com）
// - zhaopin.roavilo-glm.com/*  → /jobs/*（招聘，对应 hr/zhaopin.dianping.com）
// - h5.roavilo-glm.com/*       → /app/*（H5/下载页，对应 h5.dianping.com）
// - union.roavilo-glm.com/*    → /union-gateway（API 网关欢迎响应，对应 union.dianping.com）
// 静态资源（css/js/storage 等）与已带前缀的路径不重写。
// ---------------------------------------------------------------------
(function () {
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    $host = preg_replace('/:\d+$/', '', $host); // 去掉端口

    $map = [
        'merchant.roavilo-glm.com' => 'merchant',
        'zhaopin.roavilo-glm.com' => 'jobs',
        'h5.roavilo-glm.com' => 'app',
        'union.roavilo-glm.com' => 'union-gateway',
    ];
    if (! isset($map[$host])) {
        return;
    }
    $prefix = $map[$host];

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $query = parse_url($uri, PHP_URL_QUERY);

    // 静态资源与已带前缀的路径不重写
    $excluded = preg_match('#^/(css|js|storage|build|favicon|robots|up)(/|$)#i', $path);
    // 认证相关页面在所有子域保持主站路径（子域未登录时会跳 /login，不能被重写成 404）
    $authPages = ! $excluded && preg_match('#^/(login|register|logout|forgot-password|reset-password)(/|$)#', $path);
    $prefixed = ($path === '/'.$prefix) || str_starts_with($path, '/'.$prefix.'/');
    if ($excluded || $authPages || $prefixed) {
        return;
    }

    // union 子域统一落到网关欢迎路由
    $newPath = $prefix === 'union-gateway' ? '/union-gateway' : '/'.$prefix.$path;
    $_SERVER['REQUEST_URI'] = $newPath.($query ? '?'.$query : '');
})();

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
