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
        Schema::create('add_on_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('add_on_id')
                ->comment('Foreign key to the add_ons table, representing the add-on service')
                ->constrained('add_ons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('service_id')
                ->comment('Foreign key to the services table, representing the service that the add-on is associated with')
                ->constrained('services')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('add_on_name')->nullable()->comment('Add on name');
            $table->float('add_on_price')->default(0)->comment('Price of the add-on service');
            $table->string('service_name')->nullable()->comment('Name of the service');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_on_service');
    }
};
