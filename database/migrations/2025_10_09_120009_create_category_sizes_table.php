<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the category_sizes table as a junction table.
     * This table links categories with their available sizes (many-to-many relationship).
     */
    public function up(): void
    {
        Schema::create('category_size', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('size_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
            $table->foreign('size_id')->references('size_id')->on('sizes')->onDelete('cascade');

            // Unique constraint to prevent duplicate category-size combinations
            $table->unique(['category_id', 'size_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_sizes');
    }
};
