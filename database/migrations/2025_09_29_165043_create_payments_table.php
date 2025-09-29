<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('receipt_number')->unique();
            $table->date('payment_date');
            $table->enum('payment_method', ['Cash', 'GCash', 'Bank Transfer', 'Check', 'Credit Card']);
            $table->string('reference_number')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
