<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 単位の意味を両アプリ（akioka-web-application-pc / akioka-cloud-iot-legacy）で統一
 * solo_unit: 発注単位 / org_unit: 在庫単位（未設定時は発注単位で補完）
 * quantity_per_org: 発注1単位あたりの在庫数（納品時の換算値）
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE stocks MODIFY solo_unit varchar(255) DEFAULT NULL COMMENT '発注単位'");
        DB::statement("ALTER TABLE stocks MODIFY org_unit varchar(255) DEFAULT NULL COMMENT '在庫単位'");
        DB::statement("ALTER TABLE stocks MODIFY quantity_per_org int(11) DEFAULT 1 COMMENT '発注1単位あたりの在庫数（納品時の換算値）'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stocks MODIFY solo_unit varchar(255) DEFAULT NULL COMMENT '取り出し単位'");
        DB::statement("ALTER TABLE stocks MODIFY org_unit varchar(255) DEFAULT NULL COMMENT '発注単位'");
        DB::statement("ALTER TABLE stocks MODIFY quantity_per_org int(11) DEFAULT 1 COMMENT '納入時のデフォルト換算値'");
    }
};
