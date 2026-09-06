<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 意见反馈表（客服中心"意见反馈"功能）
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('提交用户，游客可为空');
            $table->string('name', 50)->nullable()->comment('联系人姓名');
            $table->string('contact', 100)->nullable()->comment('联系方式（手机/邮箱）');
            $table->string('type', 20)->default('suggest')->comment('类型：suggest建议/complaint投诉/bug问题/other其他');
            $table->string('title', 100)->comment('标题');
            $table->text('content')->comment('反馈内容');
            $table->tinyInteger('status')->default(0)->comment('0待处理 1已处理');
            $table->text('reply')->nullable()->comment('客服回复');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
