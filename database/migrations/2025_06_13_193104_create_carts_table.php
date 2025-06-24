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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('cart_name')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('discount_type')->nullable();
            $table->float('discount_value')->default(0);
            $table->float('sub_total_amount')->default(0);
            $table->float('total_amount')->default(0);
            $table->integer('total_items')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
