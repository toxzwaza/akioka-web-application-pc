<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_supplier_prices', function (Blueprint $table) {
            // 既存のactive_flgカラムを削除
            $table->dropColumn('active_flg');
        });
        
        Schema::table('stock_supplier_prices', function (Blueprint $table) {
            // tinyInteger型で再作成
            // 0: 無効, 1: 有効, 2: 適用済み（またはその他の状態）
            $table->tinyInteger('active_flg')->default(1)->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_supplier_prices', function (Blueprint $table) {
            // tinyIntegerを削除
            $table->dropColumn('active_flg');
        });
        
        Schema::table('stock_supplier_prices', function (Blueprint $table) {
            // 元のboolean型で再作成
            $table->boolean('active_flg')->default(true)->after('end_date');
        });
    }
};
