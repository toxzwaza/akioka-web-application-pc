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
        Schema::create('split_order_quantities', function (Blueprint $table) {
            $table->id();
            // 注文リストID
            $table->unsignedBigInteger('initial_order_id');
            $table->foreign('initial_order_id')->references('id')->on('initial_orders');
            // 個数
            $table->unsignedBigInteger('quantity')->nullable(false);
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
        Schema::dropIfExists('split_order_quantities');
    }
};
