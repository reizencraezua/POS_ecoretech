<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the sizes table for storing product/service sizes.
     * This table includes size information with grouping and soft delete functionality.
     */
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id('size_id');
            $table->string('size_name');
            $table->string('size_value')->nullable();
            $table->string('size_group')->nullable();
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
        Schema::dropIfExists('sizes');
    }
};
