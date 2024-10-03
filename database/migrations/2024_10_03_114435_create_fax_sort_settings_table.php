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
        Schema::create('fax_sort_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('fax')->nullable();
            $table->unsignedBigInteger('fax_group_id');
            $table->foreign('fax_group_id')->references('id')->on('fax_groups')->onDelete('cascade');
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
        Schema::dropIfExists('fax_sort_settings');
    }
};
