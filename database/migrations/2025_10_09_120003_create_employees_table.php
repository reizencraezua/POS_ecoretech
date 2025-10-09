<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the employees table for storing employee information.
     * This table includes personal details, job assignment, and soft delete functionality.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->string('employee_firstname');
            $table->string('employee_middlename')->nullable();
            $table->string('employee_lastname');
            $table->string('employee_email')->unique();
            $table->string('employee_contact');
            $table->text('employee_address');
            $table->date('hire_date');
            $table->unsignedBigInteger('job_id');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('job_id')->references('job_id')->on('job_positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
