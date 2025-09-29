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
        Schema::create('layout_fee_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->string('setting_name')->unique();
            $table->decimal('layout_fee_amount', 10, 2)->default(0);
            $table->string('layout_fee_type')->default('fixed'); // fixed, percentage
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layout_fee_settings');
    }
};
