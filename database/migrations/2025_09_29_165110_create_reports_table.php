<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->enum('report_type', ['Sales', 'Expenses', 'Income Statement', 'Aging']);
            $table->enum('period', ['Monthly', 'Yearly']);
            $table->date('generated_date');
            $table->string('generated_by');
            $table->json('report_data')->nullable(); // Store report data as JSON
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
