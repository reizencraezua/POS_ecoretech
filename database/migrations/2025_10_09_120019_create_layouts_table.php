<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the layouts table for storing layout design information.
     * This table includes layout details and order detail relationships.
     */
    public function up(): void
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id('layout_id');
            $table->decimal('layout_fee', 8, 2)->default(400.00);
            $table->unsignedBigInteger('order_detail_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_detail_id')->references('order_detail_id')->on('order_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layouts');
    }
};
