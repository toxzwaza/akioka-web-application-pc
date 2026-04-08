<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_signage_manual_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_user');
            $table->string('name');
            $table->string('s_name');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::table('initial_orders', function (Blueprint $table) {
            $table->integer('delivery_signage_order')->nullable()->after('receipt_flg');
        });
    }

    public function down(): void
    {
        Schema::table('initial_orders', function (Blueprint $table) {
            $table->dropColumn('delivery_signage_order');
        });

        Schema::dropIfExists('delivery_signage_manual_items');
    }
};
