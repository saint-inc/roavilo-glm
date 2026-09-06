<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 用户表：新增管理员标记（0 普通 1 管理员）
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('bio')->comment('是否管理员');
        });

        // 订单表：新增核销码（商户核销凭证）
        Schema::table('deal_orders', function (Blueprint $table) {
            $table->string('verify_code', 12)->nullable()->after('status')->comment('核销码');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
        Schema::table('deal_orders', function (Blueprint $table) {
            $table->dropColumn('verify_code');
        });
    }
};
