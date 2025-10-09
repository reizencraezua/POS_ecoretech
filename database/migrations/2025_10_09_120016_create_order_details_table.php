<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the order_details table for storing order line items.
     * This table includes item details, pricing, layout information, and unit relationships.
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('order_detail_id');
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->boolean('layout')->default(false);
            $table->decimal('layout_price', 10, 2)->default(0);
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('cascade');
            $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
