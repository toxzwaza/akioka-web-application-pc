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
        Schema::create('initial_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->nullable();
            $table->string('order_user')->nullable();
            $table->date('order_date')->nullable();
            $table->string('com_no')->nullable();
            $table->string('com_name')->nullable();
            $table->string('name')->nullable();
            $table->string('s_name')->nullable();
            $table->unsignedBigInteger('quantity')->nullable();
            $table->string('order_unit')->nullable();
            $table->string('deli_location')->nullable();
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
        Schema::dropIfExists('initial_orders');
    }
};
