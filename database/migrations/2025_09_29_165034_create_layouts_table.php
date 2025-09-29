<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id('layout_id');
            $table->string('layout_source')->nullable();
            $table->string('file_path')->nullable();
            $table->decimal('design_fee', 8, 2)->default(400.00);
            $table->foreignId('order_detail_id')->constrained('order_details', 'order_detail_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('layouts');
    }
};
