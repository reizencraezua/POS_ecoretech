<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the quotation_details table for storing quotation line items.
     * This table includes item details, pricing, layout information, and unit relationships.
     */
    public function up(): void
    {
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id('quotation_detail_id');
            $table->integer('quantity');
            $table->string('size')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->boolean('layout')->default(false);
            $table->decimal('layout_price', 10, 2)->default(0);
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('quotation_id')->references('quotation_id')->on('quotations')->onDelete('cascade');
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
        Schema::dropIfExists('quotation_details');
    }
};
