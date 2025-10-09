<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the payments table for storing payment information.
     * This table includes payment details, order relationship, and soft delete functionality.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('receipt_number')->unique();
            $table->date('payment_date');
            $table->enum('payment_method', ['Cash', 'GCash', 'Bank Transfer', 'Check', 'Credit Card']);
            $table->enum('payment_term', ['Downpayment', 'Initial', 'Full'])->nullable();
            $table->string('reference_number')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
