<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // First, drop the foreign key constraints that reference order_id
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['layout_employee_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['voided_by']);
        });

        // Change the order_id column from bigint to string
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_id', 50)->change();
        });

        // Recreate the foreign key constraints
        Schema::table('orders', function (Blueprint $table) {
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
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['layout_employee_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['voided_by']);
        });

        // Change back to bigint
        Schema::table('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id')->change();
        });

        // Recreate the foreign key constraints
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreign('layout_employee_id')->references('employee_id')->on('employees')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('voided_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};