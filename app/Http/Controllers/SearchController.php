<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

/**
 * 搜索控制器
 * 提供搜索联想（autocomplete）JSON 接口，供前端下拉提示
 */
class SearchController extends Controller
{
    /**
     * 搜索联想接口 GET /api/search/suggest?keyword=xxx
     * 返回匹配的商户名（最多 8 条）
     */
    public function suggest(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        // 关键词过短直接返回空数组
        if (mb_strlen($keyword) < 1) {
            return response()->json(['suggestions' => []]);
        }

        // 匹配商户名称（限当前城市可后续扩展）
        $shops = Shop::active()
            ->where('name', 'like', "%{$keyword}%")
            ->orderByDesc('view_count')
            ->take(8)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'suggestions' => $shops->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'url' => route('shops.show', $s),
            ]),
        ]);
    }
}
