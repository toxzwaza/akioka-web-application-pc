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
        Schema::create('order_request_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('order_request_id')->constrained('order_requests');
            $table->tinyInteger('status')->nullable()->default(null); // 承認ステータス null:前承認待ち 0:承認待ち 1:承認 2:却下
            $table->tinyInteger('final_flg')->nullable(false)->default(0); // 0:中間承認者 1:最終承認者 
            $table->text('comment')->nullable(); // 承認・非承認コメント
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
        Schema::dropIfExists('order_request_approvals');
    }
};
