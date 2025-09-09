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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // デバイス名
            $table->string('device_type', 100); // デバイス種類
            $table->string('ip_address', 15)->nullable(); // IPアドレス
            $table->string('mac_address', 17)->nullable(); // MACアドレス
            $table->string('location')->nullable(); // 設置場所
            $table->string('status', 50)->nullable(); // ステータス
            $table->datetime('last_access_date')->nullable(); // 最終アクセス日時
            $table->text('description')->nullable(); // 説明・備考
            $table->timestamps();
            
            // インデックス
            $table->index('device_type');
            $table->index('last_access_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
