<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the categories table for storing product/service categories.
     * This table includes category information with size grouping and soft delete functionality.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name')->unique();
            $table->text('category_description')->nullable();
            $table->string('category_color', 7)->default('#3B82F6');
            $table->string('size_group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('size')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
