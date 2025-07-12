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
        Schema::table('order_product', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('product_id')->comment('ID of vendor whose product is')->constrained('users');
            $table->float('tax_price')->default(0)->comment('amount admin will receive')->after('total_price');
            $table->float('vendor_cut')->default(0)->comment('amount vendor will receive')->after('tax_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_product', function (Blueprint $table) {
            //
        });
    }
};
