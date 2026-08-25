<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * active_flg の意味を両アプリ（akioka-web-application-pc / akioka-cloud-iot-legacy）で統一
 * 0:無効（手動除外・依頼時の単価参照および反映バッチの対象外）
 * 1:適用待ち（有効。登録時の初期値）
 * 2:適用済み（反映バッチが stocks.price へ反映済み）
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE stock_supplier_prices MODIFY active_flg tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:無効 1:適用待ち 2:適用済み'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stock_supplier_prices MODIFY active_flg tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:適用済み 1:未適用'");
    }
};
