<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the customers table for storing customer information.
     * This table includes personal details, business information, and soft delete functionality.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->string('customer_firstname');
            $table->string('customer_middlename')->nullable();
            $table->string('customer_lastname');
            $table->string('business_name')->nullable();
            $table->text('customer_address');
            $table->string('customer_email')->nullable();
            $table->string('contact_person1');
            $table->string('contact_number1');
            $table->string('contact_person2')->nullable();
            $table->string('contact_number2')->nullable();
            $table->string('tin')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
