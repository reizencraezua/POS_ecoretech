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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_id')->unique(); // Custom inventory ID
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('stocks')->default(0); // Current balance
            $table->integer('stock_in')->default(0); // Total stock in
            $table->integer('critical_level')->default(5); // Critical level threshold
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers', 'supplier_id')->onDelete('set null');
            $table->string('unit')->nullable(); // Unit of measurement
            $table->timestamp('last_updated')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
