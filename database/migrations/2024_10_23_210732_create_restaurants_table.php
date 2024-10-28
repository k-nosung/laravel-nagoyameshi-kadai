<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id(); // IDカラム
            $table->string('name'); // 店舗名
            $table->string('image')->default(''); // 画像ファイルへのパス
            $table->text('description'); // 説明
            $table->unsignedInteger('lowest_price')->default(0);; // 最低価格
            $table->unsignedInteger('highest_price')->default(0);; // 最高価格
            $table->string('postal_code')->nullable(); // 郵便番号をNULL可能にする
            $table->string('address'); // 住所
            $table->time('opening_time')->nullable(); // 開店時間
            $table->time('closing_time')->nullable(); // 閉店時間
            $table->unsignedInteger('seating_capacity')->nullable(); // 予約可能な座席数
            $table->timestamps(); // created_at と updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
