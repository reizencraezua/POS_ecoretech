<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the job_positions table for storing job position information.
     * This table defines the different job roles available in the organization.
     */
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id('job_id');
            $table->string('job_title')->unique();
            $table->text('job_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_positions');
    }
};
