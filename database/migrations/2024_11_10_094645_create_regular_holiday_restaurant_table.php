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
        Schema::create('regular_holiday_restaurant', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->foreignId('restaurant_id')  // 外部キー（店舗ID）
                  ->constrained()              // 外部キー制約（restaurantsテーブルを参照）
                  ->cascadeOnDelete();        // 参照先が削除された場合、関連レコードも削除される
            $table->foreignId('regular_holiday_id') // 外部キー（定休日ID）
                  ->constrained()              // 外部キー制約（regular_holidaysテーブルを参照）
                  ->cascadeOnDelete();        // 参照先が削除された場合、関連レコードも削除される
            $table->timestamps(); // 作成日時と更新日時
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regular_holiday_restaurant');
    }
};
