<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 资讯表（平台动态/媒体报道，对标点评平台"最新资讯"栏目）
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->comment('标题');
            $table->string('category', 20)->default('news')->comment('分类：news平台动态/media媒体报道/guide消费指南');
            $table->text('content')->comment('正文（支持简单段落）');
            $table->string('source', 100)->nullable()->comment('来源（媒体报道用）');
            $table->string('cover')->nullable()->comment('封面图');
            $table->unsignedInteger('view_count')->default(0)->comment('浏览量');
            $table->boolean('is_published')->default(true)->comment('是否发布');
            $table->timestamps();

            $table->index(['is_published', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
