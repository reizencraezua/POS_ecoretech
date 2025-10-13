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
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('On-Process', 'Designing', 'Production', 'For Releasing', 'Completed', 'Cancelled', 'Voided') DEFAULT 'On-Process'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('On-Process', 'Designing', 'Production', 'For Releasing', 'Completed', 'Cancelled') DEFAULT 'On-Process'");
    }
};
