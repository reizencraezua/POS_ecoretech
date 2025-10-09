<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Supplier;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();

        $inventories = [
            [
                'inventory_id' => 'INV-001',
                'name' => 'A4 Bond Paper (White)',
                'description' => 'High-quality A4 bond paper for general printing',
                'stocks' => 500,
                'stock_in' => 1000,
                'critical_level' => 50,
                'supplier_id' => $suppliers->where('supplier_name', 'Paper Plus Supplies')->first()->supplier_id,
                'unit' => 'Reams',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'inventory_id' => 'INV-002',
                'name' => 'Cardstock (White)',
                'description' => 'Premium cardstock for business cards and invitations',
                'stocks' => 200,
                'stock_in' => 500,
                'critical_level' => 25,
                'supplier_id' => $suppliers->where('supplier_name', 'Cardstock Central')->first()->supplier_id,
                'unit' => 'Sheets',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'inventory_id' => 'INV-003',
                'name' => 'Black Ink Cartridge',
                'description' => 'High-yield black ink cartridge for laser printers',
                'stocks' => 15,
                'stock_in' => 30,
                'critical_level' => 5,
                'supplier_id' => $suppliers->where('supplier_name', 'Ink & Toner Solutions')->first()->supplier_id,
                'unit' => 'Pieces',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'inventory_id' => 'INV-004',
                'name' => 'Color Ink Cartridges (CMYK)',
                'description' => 'Complete set of color ink cartridges',
                'stocks' => 8,
                'stock_in' => 16,
                'critical_level' => 3,
                'supplier_id' => $suppliers->where('supplier_name', 'Ink & Toner Solutions')->first()->supplier_id,
                'unit' => 'Sets',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'inventory_id' => 'INV-005',
                'name' => 'Vinyl Banner Material',
                'description' => 'Heavy-duty vinyl material for outdoor banners',
                'stocks' => 50,
                'stock_in' => 100,
                'critical_level' => 10,
                'supplier_id' => $suppliers->where('supplier_name', 'Vinyl Materials Inc.')->first()->supplier_id,
                'unit' => 'Meters',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'inventory_id' => 'INV-006',
                'name' => 'Lamination Film',
                'description' => 'Glossy lamination film for protective coating',
                'stocks' => 25,
                'stock_in' => 50,
                'critical_level' => 5,
                'supplier_id' => $suppliers->where('supplier_name', 'Print Equipment Co.')->first()->supplier_id,
                'unit' => 'Rolls',
                'last_updated' => now(),
                'is_active' => true
            ]
        ];

        foreach ($inventories as $inventory) {
            Inventory::create($inventory);
        }

        $this->command->info('Successfully seeded ' . count($inventories) . ' inventory items.');
    }
}
