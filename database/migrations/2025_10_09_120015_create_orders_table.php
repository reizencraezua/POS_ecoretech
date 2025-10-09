<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the orders table for storing order information.
     * This table includes order details, customer/employee relationships, void functionality, and soft deletes.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->date('order_date');
            $table->date('deadline_date');
            $table->enum('order_status', [
                'On-Process',
                'Designing',
                'Production',
                'For Releasing',
                'Completed',
                'Cancelled'
            ])->default('On-Process');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('layout_design_fee', 10, 2)->default(0);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('layout_employee_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->text('void_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreign('layout_employee_id')->references('employee_id')->on('employees')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('voided_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
