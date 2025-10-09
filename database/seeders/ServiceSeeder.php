<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Size;
use App\Models\Unit;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $sizes = Size::all();
        $units = Unit::all();

        $services = [
            [
                'service_name' => 'T-Shirt Printing Only',
                'description' => 'Printing service for customer-provided t-shirts (bring your own shirt)',
                'base_fee' => 80.00,
                'layout_price' => 50.00,
                'requires_layout' => true,
                'category_id' => $categories->where('category_name', 'T-Shirts')->first()?->category_id ?? $categories->first()->category_id,
                'size_id' => null,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'service_name' => 'T-Shirt Embroidery Only',
                'description' => 'Embroidery service for customer-provided t-shirts (bring your own shirt)',
                'base_fee' => 120.00,
                'layout_price' => 50.00,
                'requires_layout' => true,
                'category_id' => $categories->where('category_name', 'T-Shirts')->first()?->category_id ?? $categories->first()->category_id,
                'size_id' => null,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'service_name' => 'Mug Printing Only',
                'description' => 'Printing service for customer-provided mugs (bring your own mug)',
                'base_fee' => 60.00,
                'layout_price' => 30.00,
                'requires_layout' => true,
                'category_id' => $categories->where('category_name', 'Mugs')->first()?->category_id ?? $categories->first()->category_id,
                'size_id' => null,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'service_name' => 'Short Printing Only',
                'description' => 'Printing service for customer-provided shorts (bring your own shorts)',
                'base_fee' => 100.00,
                'layout_price' => 50.00,
                'requires_layout' => true,
                'category_id' => $categories->where('category_name', 'Shorts')->first()?->category_id ?? $categories->first()->category_id,
                'size_id' => null,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'service_name' => 'Tarp Printing Only',
                'description' => 'Printing service for customer-provided tarpaulins (bring your own tarp)',
                'base_fee' => 200.00,
                'layout_price' => 100.00,
                'requires_layout' => true,
                'category_id' => $categories->where('category_name', 'Tarps')->first()?->category_id ?? $categories->first()->category_id,
                'size_id' => null,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'service_name' => 'Cap Embroidery Only',
                'description' => 'Embroidery service for customer-provided caps (bring your own cap)',
                'base_fee' => 80.00,
                'layout_price' => 40.00,
                'requires_layout' => true,
                'category_id' => $categories->where('category_name', 'Caps')->first()?->category_id ?? $categories->first()->category_id,
                'size_id' => null,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->command->info('Successfully seeded ' . count($services) . ' services.');
    }
}
