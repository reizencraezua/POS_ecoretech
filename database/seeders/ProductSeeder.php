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
        $categories = Category::all();
        $sizes = Size::all();
        $units = Unit::all();

        $products = [
            [
                'product_name' => 'Premium Business Cards',
                'base_price' => 150.00,
                'layout_price' => 50.00,
                'requires_layout' => true,
                'product_description' => 'High-quality business cards with premium finish',
                'category_id' => $categories->where('category_name', 'Business Cards')->first()->category_id,
                'size_id' => $sizes->where('size_name', '3.5" x 2"')->first()?->size_id,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'product_name' => 'Marketing Flyers',
                'base_price' => 25.00,
                'layout_price' => 75.00,
                'requires_layout' => true,
                'product_description' => 'Eye-catching marketing flyers for promotions',
                'category_id' => $categories->where('category_name', 'Flyers')->first()->category_id,
                'size_id' => $sizes->where('size_name', '8.5" x 11"')->first()?->size_id,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'product_name' => 'Tri-fold Brochures',
                'base_price' => 45.00,
                'layout_price' => 100.00,
                'requires_layout' => true,
                'product_description' => 'Professional tri-fold brochures for marketing',
                'category_id' => $categories->where('category_name', 'Brochures')->first()->category_id,
                'size_id' => $sizes->where('size_name', '8.5" x 11"')->first()?->size_id,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'product_name' => 'Event Posters',
                'base_price' => 200.00,
                'layout_price' => 150.00,
                'requires_layout' => true,
                'product_description' => 'Large format posters for events and advertising',
                'category_id' => $categories->where('category_name', 'Posters')->first()->category_id,
                'size_id' => $sizes->where('size_name', '18" x 24"')->first()?->size_id,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'product_name' => 'Vinyl Banners',
                'base_price' => 500.00,
                'layout_price' => 200.00,
                'requires_layout' => true,
                'product_description' => 'Durable vinyl banners for outdoor advertising',
                'category_id' => $categories->where('category_name', 'Banners')->first()->category_id,
                'size_id' => $sizes->where('size_name', '3ft x 6ft')->first()?->size_id,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ],
            [
                'product_name' => 'Custom Stickers',
                'base_price' => 5.00,
                'layout_price' => 25.00,
                'requires_layout' => true,
                'product_description' => 'Custom stickers and labels for branding',
                'category_id' => $categories->where('category_name', 'Stickers')->first()->category_id,
                'size_id' => $sizes->where('size_name', '2" x 2"')->first()?->size_id,
                'unit_id' => $units->where('unit_name', 'Pieces')->first()->unit_id
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Successfully seeded ' . count($products) . ' products.');
    }
}
