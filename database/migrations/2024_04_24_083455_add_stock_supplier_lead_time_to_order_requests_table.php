<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->integer('stock_supplier_lead_time')->nullable()->after('supplier_id');
            $table->string('supplier_name')->nullable()->after('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_requests', function (Blueprint $table) {
            $table->dropColumn('stock_supplier_lead_time');
            $table->dropColumn('supplier_name');
        });
    }
}; 