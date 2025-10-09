<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the deliveries table for storing delivery information.
     * This table includes delivery details, order/employee relationships, and status tracking.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id('delivery_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->date('delivery_date');
            $table->text('delivery_address');
            $table->string('driver_name');
            $table->string('driver_contact', 20);
            $table->enum('status', ['scheduled', 'in_transit', 'delivered', 'cancelled'])->default('scheduled');
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
