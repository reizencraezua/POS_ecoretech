<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the products table for storing product information.
     * This table includes product details, pricing, and relationships to categories, sizes, and units.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('layout_price', 10, 2)->default(0);
            $table->boolean('requires_layout')->default(false);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            $table->foreign('size_id')->references('size_id')->on('sizes')->onDelete('set null');
            $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
