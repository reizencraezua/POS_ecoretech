<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed data for printing shop
        $this->call([
            UserSeeder::class,
            JobPositionSeeder::class,
            EmployeeSeeder::class,
            CustomerSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            SizeSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,
            InventorySeeder::class,
        ]);
    }
}