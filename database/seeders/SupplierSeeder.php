<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_name' => 'Paper Plus Supplies',
                'supplier_email' => 'orders@paperplus.com',
                'supplier_contact' => '09123456789',
                'supplier_address' => '100 Paper Street, Quezon City'
            ],
            [
                'supplier_name' => 'Ink & Toner Solutions',
                'supplier_email' => 'sales@inktoner.com',
                'supplier_contact' => '09123456790',
                'supplier_address' => '200 Ink Avenue, Makati City'
            ],
            [
                'supplier_name' => 'Print Equipment Co.',
                'supplier_email' => 'info@printequipment.com',
                'supplier_contact' => '09123456791',
                'supplier_address' => '300 Equipment Blvd, Taguig City'
            ],
            [
                'supplier_name' => 'Vinyl Materials Inc.',
                'supplier_email' => 'orders@vinylmaterials.com',
                'supplier_contact' => '09123456792',
                'supplier_address' => '400 Vinyl Street, Pasig City'
            ],
            [
                'supplier_name' => 'Cardstock Central',
                'supplier_email' => 'sales@cardstockcentral.com',
                'supplier_contact' => '09123456793',
                'supplier_address' => '500 Cardstock Lane, Mandaluyong City'
            ],
            [
                'supplier_name' => 'Printing Chemicals Ltd.',
                'supplier_email' => 'contact@printchemicals.com',
                'supplier_contact' => '09123456794',
                'supplier_address' => '600 Chemical Road, Caloocan City'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('Successfully seeded ' . count($suppliers) . ' suppliers.');
    }
}
