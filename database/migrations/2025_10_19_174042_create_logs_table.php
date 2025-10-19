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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type'); // quotation, order, payment, delivery
            $table->unsignedBigInteger('transaction_id'); // ID of the transaction
            $table->string('transaction_name')->nullable(); // Custom name for the transaction
            $table->string('action'); // created, updated, deleted, status_changed
            $table->string('edited_by'); // Name of the user who made the change
            $table->unsignedBigInteger('user_id'); // ID of the user who made the change
            $table->json('changes')->nullable(); // JSON of what was changed
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['transaction_type', 'transaction_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
