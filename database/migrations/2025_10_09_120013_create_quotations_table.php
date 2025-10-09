<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the quotations table for storing quotation information.
     * This table includes quotation details, customer relationship, and soft delete functionality.
     */
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id('quotation_id');
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->enum('status', ['Pending', 'Closed'])->default('Pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
