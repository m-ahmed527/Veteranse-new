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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the tax');
            $table->decimal('rate', 5, 2)->comment('Tax rate in percentage');
            $table->boolean('is_active')->default(true)->comment('Indicates if the tax is currently active');
            $table->string('type')->default('percentage')->comment('Type of tax, e.g., percentage or fixed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
