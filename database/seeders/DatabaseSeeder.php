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
            UnitSeeder::class,
            SizeSeeder::class,
            AdminSeeder::class,
        ]);
    }
}