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
        // Add missing foreign keys to orders table (check if they don't exist first)
        if (!$this->foreignKeyExists('orders', 'orders_customer_id_foreign')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('orders', 'orders_employee_id_foreign')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            });
        }

        if (!$this->foreignKeyExists('orders', 'orders_layout_employee_id_foreign')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('layout_employee_id')->references('employee_id')->on('employees')->onDelete('set null');
            });
        }

        // Add missing foreign keys to products table
        if (!$this->foreignKeyExists('products', 'products_category_id_foreign')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('products', 'products_size_id_foreign')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('size_id')->references('size_id')->on('sizes')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('products', 'products_unit_id_foreign')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('set null');
            });
        }

        // Add missing foreign keys to services table
        if (!$this->foreignKeyExists('services', 'services_category_id_foreign')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('services', 'services_size_id_foreign')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreign('size_id')->references('size_id')->on('sizes')->onDelete('set null');
            });
        }

        if (!$this->foreignKeyExists('services', 'services_unit_id_foreign')) {
            Schema::table('services', function (Blueprint $table) {
                $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('set null');
            });
        }

        // Add missing foreign key to payments table
        if (!$this->foreignKeyExists('payments', 'payments_order_id_foreign')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            });
        }

        // Note: sizes table already has unit_id foreign key constraint
    }

    /**
     * Check if a foreign key constraint exists
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_NAME = ?
        ", [$table, $constraintName]);

        return count($constraints) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['layout_employee_id']);
        });

        // Drop foreign keys from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['size_id']);
            $table->dropForeign(['unit_id']);
        });

        // Drop foreign keys from services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['size_id']);
            $table->dropForeign(['unit_id']);
        });

        // Drop foreign key from payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        // Note: sizes table unit_id foreign key was created in original migration
    }
};