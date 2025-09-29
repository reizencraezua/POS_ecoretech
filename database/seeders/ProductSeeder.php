<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Models\Unit;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories and units for relationships
        $tshirtCategory = Category::where('category_name', 'T-Shirts')->first();
        $uniformCategory = Category::where('category_name', 'Uniforms')->first();
        $mugCategory = Category::where('category_name', 'Coffee Mugs')->first();
        $businessCardCategory = Category::where('category_name', 'Business Cards')->first();
        $flyerCategory = Category::where('category_name', 'Flyers & Brochures')->first();
        $bannerCategory = Category::where('category_name', 'Banners')->first();
        $tarpaulinCategory = Category::where('category_name', 'Tarpaulins')->first();
        $calendarCategory = Category::where('category_name', 'Calendars')->first();
        $letterheadCategory = Category::where('category_name', 'Letterheads')->first();
        $posterCategory = Category::where('category_name', 'Posters')->first();

        $piecesUnit = Unit::where('unit_name', 'Pieces')->first();
        $sheetsUnit = Unit::where('unit_name', 'Sheets')->first();
        $sqftUnit = Unit::where('unit_name', 'Square Feet')->first();

        $products = [
            // CLOTHING APPARELS (from image)
            [
                'product_name' => 'Custom Printed T-Shirt',
                'item_number' => 'TSH001',
                'base_price' => 250.00,
                'product_description' => 'High-quality custom printed t-shirts - Fast, Quality & Affordable',
                'category_id' => $tshirtCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 100.00,
                'layout_description' => 'Custom design layout for t-shirt printing',
            ],
            [
                'product_name' => 'Sports Jersey',
                'item_number' => 'JSY001',
                'base_price' => 450.00,
                'product_description' => 'Custom sports jerseys with team names and numbers',
                'category_id' => $uniformCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 150.00,
                'layout_description' => 'Sports jersey design with numbers and names',
            ],
            [
                'product_name' => 'Polo Shirt',
                'item_number' => 'POL001',
                'base_price' => 350.00,
                'product_description' => 'Premium polo shirts for corporate uniforms',
                'category_id' => $uniformCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 80.00,
                'layout_description' => 'Corporate logo embroidery design',
            ],

            // CUSTOMIZED PRINTED MUGS (from image)
            [
                'product_name' => 'Ceramic Printed Mug - 11oz',
                'item_number' => 'MUG001',
                'base_price' => 180.00,
                'product_description' => 'High-quality ceramic mug with custom printing',
                'category_id' => $mugCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 50.00,
                'layout_description' => 'Custom design for mug printing',
            ],
            [
                'product_name' => 'Magic Mug - Color Changing',
                'item_number' => 'MUG002',
                'base_price' => 280.00,
                'product_description' => 'Color-changing magic mug with custom design',
                'category_id' => $mugCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 75.00,
                'layout_description' => 'Magic mug design layout',
            ],

            // BUSINESS CARDS (from image)
            [
                'product_name' => 'Premium Business Cards',
                'item_number' => 'BC001',
                'base_price' => 8.00,
                'product_description' => 'High-quality business cards on premium cardstock',
                'category_id' => $businessCardCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 200.00,
                'layout_description' => 'Professional business card design',
            ],
            [
                'product_name' => 'Standard Business Cards',
                'item_number' => 'BC002',
                'base_price' => 5.00,
                'product_description' => 'Standard business cards on quality paper',
                'category_id' => $businessCardCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 150.00,
                'layout_description' => 'Standard business card layout',
            ],

            // LETTERHEADS (from image)
            [
                'product_name' => 'Company Letterhead',
                'item_number' => 'LH001',
                'base_price' => 12.00,
                'product_description' => 'Professional company letterheads on premium paper',
                'category_id' => $letterheadCategory?->category_id,
                'unit_id' => $sheetsUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 300.00,
                'layout_description' => 'Corporate letterhead design with logo',
            ],

            // CALENDARS (from image)
            [
                'product_name' => 'Wall Calendar',
                'item_number' => 'CAL001',
                'base_price' => 350.00,
                'product_description' => 'Custom wall calendars with company branding',
                'category_id' => $calendarCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 500.00,
                'layout_description' => 'Calendar layout with custom photos and branding',
            ],
            [
                'product_name' => 'Desk Calendar',
                'item_number' => 'CAL002',
                'base_price' => 250.00,
                'product_description' => 'Desktop calendar with custom design',
                'category_id' => $calendarCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 400.00,
                'layout_description' => 'Desk calendar design and layout',
            ],

            // FLYERS (from image)
            [
                'product_name' => 'A4 Flyer - Full Color',
                'item_number' => 'FLY001',
                'base_price' => 15.00,
                'product_description' => 'Full color A4 flyers on quality paper',
                'category_id' => $flyerCategory?->category_id,
                'unit_id' => $sheetsUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 250.00,
                'layout_description' => 'Professional flyer design and layout',
            ],
            [
                'product_name' => 'A5 Flyer - Full Color',
                'item_number' => 'FLY002',
                'base_price' => 10.00,
                'product_description' => 'Full color A5 flyers for promotions',
                'category_id' => $flyerCategory?->category_id,
                'unit_id' => $sheetsUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 200.00,
                'layout_description' => 'A5 flyer design and layout',
            ],

            // TARPAULIN PRINTING (from image)
            [
                'product_name' => 'Tarpaulin Banner - 3x5 ft',
                'item_number' => 'TAR001',
                'base_price' => 45.00,
                'product_description' => 'Weather-resistant tarpaulin banner for outdoor use',
                'category_id' => $tarpaulinCategory?->category_id,
                'unit_id' => $sqftUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 400.00,
                'layout_description' => 'Tarpaulin banner design with graphics',
            ],
            [
                'product_name' => 'Tarpaulin Banner - 4x6 ft',
                'item_number' => 'TAR002',
                'base_price' => 50.00,
                'product_description' => 'Large tarpaulin banner for events and advertising',
                'category_id' => $tarpaulinCategory?->category_id,
                'unit_id' => $sqftUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 500.00,
                'layout_description' => 'Large format tarpaulin design',
            ],

            // POSTERS (from image - clearance sale poster visible)
            [
                'product_name' => 'A3 Poster - Full Color',
                'item_number' => 'POS001',
                'base_price' => 85.00,
                'product_description' => 'High-quality A3 posters for promotions',
                'category_id' => $posterCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 300.00,
                'layout_description' => 'A3 poster design and layout',
            ],
            [
                'product_name' => 'A2 Poster - Full Color',
                'item_number' => 'POS002',
                'base_price' => 150.00,
                'product_description' => 'Large format A2 posters for maximum impact',
                'category_id' => $posterCategory?->category_id,
                'unit_id' => $piecesUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 400.00,
                'layout_description' => 'A2 poster design and layout',
            ],

            // VINYL BANNERS (from image)
            [
                'product_name' => 'Vinyl Banner - 2x4 ft',
                'item_number' => 'VIN001',
                'base_price' => 35.00,
                'product_description' => 'Durable vinyl banner for indoor/outdoor use',
                'category_id' => $bannerCategory?->category_id,
                'unit_id' => $sqftUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 350.00,
                'layout_description' => 'Vinyl banner design with graphics',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Successfully seeded ' . count($products) . ' products based on Ecoretech printing shop offerings.');
    }
}