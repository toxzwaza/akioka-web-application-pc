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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('kind')->nullable(); //種別 0:製品 1:新規案件 2:営業・広告 3:その他
            $table->string('name')->nullable(); //名前
            $table->string('furi_name')->nullable(); //フリガナ
            $table->string('email')->nullable(); //メールアドレス
            $table->string('tel')->nullable(); //電話番号
            $table->text('content')->nullable(); //内容
            $table->text('summary')->nullable(); //要約
            $table->tinyInteger('progress')->default(0); //進捗 (0:未対応 1:対応中 2:対応済み)
            $table->foreignId('user_id')->constrained('users'); //担当者
            $table->tinyInteger('del_flg')->default(0); //削除フラグ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
