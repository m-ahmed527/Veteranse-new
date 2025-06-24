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
        Schema::table('users', function (Blueprint $table) {
            $table->string('vendor_store_title')->nullable()->after('role');
            $table->text('vendor_store_description')->nullable()->after('vendor_store_title');
            $table->string('vendor_store_image')->nullable()->after('vendor_store_description');
            $table->json('vendor_store_gallery')->nullable()->after('vendor_store_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
