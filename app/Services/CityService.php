<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * 城市服务
 * 统一解析"当前城市"，供控制器与视图布局共用
 *
 * 解析优先级（对标点评平台城市记忆行为）：
 * 1. session 中的 city_id（本次会话内切换）
 * 2. cookie 中的 city_id（跨会话记忆，30 天有效）
 * 3. 默认上海 > 数据库第一个城市
 */
class CityService
{
    /** 默认城市 slug */
    public const DEFAULT_SLUG = 'shanghai';

    /** 城市记忆 cookie 名 */
    public const COOKIE_NAME = 'roavilo-glm_city';

    /** cookie 有效期（天） */
    public const COOKIE_DAYS = 30;

    /**
     * 解析当前城市
     * 注意：session 不可用（如错误页渲染）时静默跳过缓存
     */
    public static function resolve(Request $request): City
    {
        // 安全读取 session（错误页渲染等场景 session store 可能未设置）
        $cityId = null;
        try {
            $cityId = $request->session()->get('city_id');
        } catch (\Throwable) {
            // 忽略
        }

        // session 未命中时读 cookie（跨会话记忆）
        if (! $cityId) {
            $cityId = $request->cookie(self::COOKIE_NAME);
        }

        $city = $cityId ? City::find($cityId) : null;

        $city = $city ?: City::where('slug', self::DEFAULT_SLUG)->first() ?: City::firstOrFail();

        // 回写 session（同上，失败静默）
        try {
            $request->session()->put('city_id', $city->id);
        } catch (\Throwable) {
            // 忽略
        }

        return $city;
    }

    /**
     * 记住用户选择的城市（session + cookie 双写）
     * 城市首页访问时调用，实现"访问即切换"
     */
    public static function remember(Request $request, City $city): void
    {
        try {
            $request->session()->put('city_id', $city->id);
        } catch (\Throwable) {
            // 忽略
        }

        // cookie 排队加入响应（Laravel Cookie 队列机制，随响应下发）
        // cookie 加入下发队列（Cookie::queue 随响应发送），30 天有效
        \Illuminate\Support\Facades\Cookie::queue(
            self::COOKIE_NAME,
            (string) $city->id,
            self::COOKIE_DAYS * 24 * 60
        );
    }

    /**
     * 全部城市按拼音首字母分组（城市选择页左侧字母导航用）
     * 返回：['B' => [城市...], 'C' => [城市...], ...] 按字母排序
     */
    public static function groupedByLetter(): Collection
    {
        return City::orderBy('name')->get()
            ->groupBy(fn ($city) => strtoupper(self::firstLetter($city->name)))
            ->sortKeys();
    }

    /**
     * 取中文城市的拼音首字母（简易映射，覆盖 seeder 中的 51 个城市）
     */
    private static function firstLetter(string $name): string
    {
        $map = [
            '北京' => 'B', '上海' => 'S', '天津' => 'T', '重庆' => 'C',
            '广州' => 'G', '深圳' => 'S', '杭州' => 'H', '成都' => 'C',
            '南京' => 'N', '武汉' => 'W', '西安' => 'X', '苏州' => 'S',
            '长沙' => 'C', '郑州' => 'Z', '青岛' => 'Q', '合肥' => 'H',
            '福州' => 'F', '厦门' => 'X', '宁波' => 'N', '无锡' => 'W',
            '昆明' => 'K', '贵阳' => 'G', '南宁' => 'N', '哈尔滨' => 'H',
            '沈阳' => 'S', '大连' => 'D', '长春' => 'C', '石家庄' => 'S',
            '太原' => 'T', '济南' => 'J', '南昌' => 'N', '佛山' => 'F',
            '东莞' => 'D', '珠海' => 'Z', '惠州' => 'H', '温州' => 'W',
            '常州' => 'C', '绍兴' => 'S', '烟台' => 'Y', '洛阳' => 'L',
            '兰州' => 'L', '乌鲁木齐' => 'W', '呼和浩特' => 'H', '西宁' => 'X',
            '银川' => 'Y', '拉萨' => 'L', '海口' => 'H', '三亚' => 'S',
            '南通' => 'N', '嘉兴' => 'J', '泉州' => 'Q',
        ];

        return $map[$name] ?? 'A';
    }
}
