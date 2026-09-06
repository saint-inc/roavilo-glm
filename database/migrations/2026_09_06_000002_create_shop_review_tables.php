<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->comment('商户绑定的用户');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('cover')->nullable();
            $table->text('address');
            $table->decimal('longitude', 10, 6)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->string('phone', 30)->nullable();
            $table->decimal('avg_price', 8, 2)->nullable()->comment('人均消费');
            $table->decimal('rating', 2, 1)->unsigned()->default(0)->comment('评分 0-5');
            $table->unsignedInteger('rating_count')->default(0);
            $table->text('description')->nullable();
            $table->string('business_hours')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1正常 2待审核 0下架');
            $table->timestamps();

            $table->index(['city_id', 'category_id']);
        });

        Schema::create('shop_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('rating', 2, 1)->unsigned();
            $table->text('content');
            $table->decimal('cost', 8, 2)->nullable()->comment('本次消费');
            $table->json('images')->nullable();
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('reply_count')->default(0);
            $table->timestamps();

            $table->unique(['shop_id', 'user_id']);
        });

        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('review_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['review_id', 'user_id']);
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('review_likes');
        Schema::dropIfExists('review_replies');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('shop_images');
        Schema::dropIfExists('shops');
    }
};
