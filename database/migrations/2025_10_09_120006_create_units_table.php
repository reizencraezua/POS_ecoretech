<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the units table for storing measurement units.
     * This table defines the different units of measurement used for products and services.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id('unit_id');
            $table->string('unit_name');
            $table->string('unit_code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
