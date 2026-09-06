<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1上架 0下架');
            $table->timestamps();
        });

        Schema::create('deal_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 32)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_amount', 10, 2);
            $table->tinyInteger('status')->default(0)->comment('0待支付 1已支付 2已使用 3已退款 4已取消');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_orders');
        Schema::dropIfExists('deals');
    }
};
