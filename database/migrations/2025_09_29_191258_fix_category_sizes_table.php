<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if category_sizes table already exists
        if (!Schema::hasTable('category_sizes')) {
            // Create the category_sizes table (plural form) to match the schema
            Schema::create('category_sizes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories', 'category_id')->onDelete('cascade');
                $table->foreignId('size_id')->constrained('sizes', 'size_id')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['category_id', 'size_id']);
            });
        }

        // Copy data from category_size to category_sizes if category_size exists and category_sizes is empty
        if (Schema::hasTable('category_size') && Schema::hasTable('category_sizes')) {
            $count = DB::table('category_sizes')->count();
            if ($count == 0) {
                DB::statement('INSERT INTO category_sizes (category_id, size_id, created_at, updated_at) 
                              SELECT category_id, size_id, created_at, updated_at FROM category_size');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_sizes');
    }
};