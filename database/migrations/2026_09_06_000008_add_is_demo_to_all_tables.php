<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 为全部业务表添加 is_demo 字段（tinyint，默认 0）
 *
 * 用途：标记记录是否为演示/调试数据（1=demo，0=真实）。
 * 约定：应用代码不读写该字段；demo 数据由 Seeder 写入时统一标记，
 * 项目上线前删除该字段与全部 demo 数据即可，不影响业务逻辑。
 */
return new class extends Migration
{
    /** 需要加 is_demo 的业务表清单 */
    private const TABLES = [
        'users',
        'cities',
        'categories',
        'regions',
        'shops',
        'shop_images',
        'reviews',
        'review_replies',
        'review_likes',
        'favorites',
        'deals',
        'deal_orders',
        'feedbacks',
        'news',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                // 0=真实数据，1=demo 数据；索引便于上线前批量清理
                $t->boolean('is_demo')->default(false)->index()->comment('是否demo数据：1是 0否');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropIndex(['is_demo']);
                $t->dropColumn('is_demo');
            });
        }
    }
};
