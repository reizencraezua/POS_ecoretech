<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id('quotation_id');
            $table->date('quotation_date');
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->enum('status', ['Pending', 'Closed'])->default('Pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};
