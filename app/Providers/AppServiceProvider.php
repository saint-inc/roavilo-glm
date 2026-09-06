<?php

namespace App\Providers;

use App\Services\CityService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 给主布局注入当前城市（导航栏搜索框/商户链接使用）
        View::composer('layouts.app', function ($view) {
            $view->with('currentCity', CityService::resolve(request()));
        });
    }
}

