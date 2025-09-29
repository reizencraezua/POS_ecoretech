<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
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
            $table->string('payment_terms')->nullable();
            $table->string('tin')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
