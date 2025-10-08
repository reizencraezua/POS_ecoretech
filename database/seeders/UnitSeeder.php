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
            ['unit_name' => 'Pieces', 'unit_code' => 'PCS', 'description' => 'Individual pieces/items', 'is_active' => true],
            ['unit_name' => 'Meters', 'unit_code' => 'M', 'description' => 'Length measurement in meters', 'is_active' => true],
            ['unit_name' => 'Set', 'unit_code' => 'SET', 'description' => 'Complete sets of items', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }

        $this->command->info('Successfully seeded ' . count($units) . ' units for printing shop.');
    }
}