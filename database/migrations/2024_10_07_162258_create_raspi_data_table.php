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
        Schema::create('raspi_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_id')->nullable(false);
            $table->string('temperature')->nullable(false);
            $table->string('humidity')->nullable(false);
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
        Schema::dropIfExists('raspi_data');
    }
};
