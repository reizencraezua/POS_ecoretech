<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the stock_usages table for tracking inventory usage.
     * This table records when and how inventory items are used.
     */
    public function up(): void
    {
        Schema::create('stock_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->integer('quantity_used');
            $table->text('purpose')->nullable(); // What was it used for
            $table->string('used_by')->nullable(); // Who used it
            $table->timestamp('used_at');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_usages');
    }
};
