<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Default unit for printing shop
            ['unit_name' => 'Pieces', 'unit_code' => 'PCS', 'description' => 'Individual pieces/items - Default unit', 'is_active' => true],
            
            // Common printing units
            ['unit_name' => 'Sheets', 'unit_code' => 'SHT', 'description' => 'Paper sheets or printed sheets', 'is_active' => true],
            ['unit_name' => 'Pages', 'unit_code' => 'PG', 'description' => 'Individual pages', 'is_active' => true],
            ['unit_name' => 'Copies', 'unit_code' => 'CP', 'description' => 'Number of copies', 'is_active' => true],
            ['unit_name' => 'Sets', 'unit_code' => 'SET', 'description' => 'Complete sets of items', 'is_active' => true],
            ['unit_name' => 'Square Feet', 'unit_code' => 'SQ FT', 'description' => 'Square feet for banners/tarpaulins', 'is_active' => true],
            ['unit_name' => 'Linear Feet', 'unit_code' => 'LIN FT', 'description' => 'Linear feet for continuous printing', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        $this->command->info('Successfully seeded ' . count($units) . ' units for printing shop.');
    }
}