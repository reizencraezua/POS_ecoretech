<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Size;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            // Clothing Sizes
            ['size_name' => 'XS', 'size_value' => 'Extra Small', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'S', 'size_value' => 'Small', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'M', 'size_value' => 'Medium', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'L', 'size_value' => 'Large', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'XL', 'size_value' => 'Extra Large', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'XXL', 'size_value' => 'Double Extra Large', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'XXXL', 'size_value' => 'Triple Extra Large', 'size_group' => 'clothing', 'is_active' => true],
            
            // Children's Clothing Sizes
            ['size_name' => '2T', 'size_value' => 'Toddler Size 2', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => '3T', 'size_value' => 'Toddler Size 3', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => '4T', 'size_value' => 'Toddler Size 4', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => '5T', 'size_value' => 'Toddler Size 5', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'Youth 6', 'size_value' => 'Youth Size 6', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'Youth 8', 'size_value' => 'Youth Size 8', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'Youth 10', 'size_value' => 'Youth Size 10', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'Youth 12', 'size_value' => 'Youth Size 12', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'Youth 14', 'size_value' => 'Youth Size 14', 'size_group' => 'clothing', 'is_active' => true],
            ['size_name' => 'Youth 16', 'size_value' => 'Youth Size 16', 'size_group' => 'clothing', 'is_active' => true],
            
            // Mug Sizes
            ['size_name' => '6 oz Mug', 'size_value' => '6 ounces', 'size_group' => 'mug', 'is_active' => true],
            ['size_name' => '8 oz Mug', 'size_value' => '8 ounces', 'size_group' => 'mug', 'is_active' => true],
            ['size_name' => '11 oz Mug', 'size_value' => '11 ounces', 'size_group' => 'mug', 'is_active' => true],
            ['size_name' => '12 oz Mug', 'size_value' => '12 ounces', 'size_group' => 'mug', 'is_active' => true],
            ['size_name' => '15 oz Mug', 'size_value' => '15 ounces', 'size_group' => 'mug', 'is_active' => true],
            ['size_name' => '20 oz Travel Mug', 'size_value' => '20 ounces', 'size_group' => 'mug', 'is_active' => true],
            
            // Paper Sizes - Standard International (ISO 216)
            ['size_name' => 'A0', 'size_value' => '841 × 1189 mm', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'A1', 'size_value' => '594 × 841 mm', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'A2', 'size_value' => '420 × 594 mm', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'A3', 'size_value' => '297 × 420 mm', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'A4', 'size_value' => '210 × 297 mm', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'A5', 'size_value' => '148 × 210 mm', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'A6', 'size_value' => '105 × 148 mm', 'size_group' => 'paper', 'is_active' => true],
            
            // Paper Sizes - US Standard
            ['size_name' => 'Letter', 'size_value' => '8.5 × 11 inches', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'Legal', 'size_value' => '8.5 × 14 inches', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'Tabloid', 'size_value' => '11 × 17 inches', 'size_group' => 'paper', 'is_active' => true],
            ['size_name' => 'Ledger', 'size_value' => '17 × 11 inches', 'size_group' => 'paper', 'is_active' => true],
            
            // Business Card & Small Format Sizes
            ['size_name' => 'Business Card', 'size_value' => '3.5 × 2 inches', 'size_group' => 'small_format', 'is_active' => true],
            ['size_name' => 'Postcard', 'size_value' => '6 × 4 inches', 'size_group' => 'small_format', 'is_active' => true],
            ['size_name' => 'Greeting Card', 'size_value' => '5 × 7 inches', 'size_group' => 'small_format', 'is_active' => true],
            ['size_name' => 'Photo 4R', 'size_value' => '6 × 4 inches', 'size_group' => 'small_format', 'is_active' => true],
            ['size_name' => 'Photo 5R', 'size_value' => '7 × 5 inches', 'size_group' => 'small_format', 'is_active' => true],
            ['size_name' => 'Photo 8R', 'size_value' => '10 × 8 inches', 'size_group' => 'small_format', 'is_active' => true],
            
            // Tarpaulin/Banner Sizes (in feet)
            ['size_name' => '2×3 ft Banner', 'size_value' => '2 × 3 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '3×4 ft Banner', 'size_value' => '3 × 4 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '3×5 ft Banner', 'size_value' => '3 × 5 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '4×6 ft Banner', 'size_value' => '4 × 6 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '5×8 ft Banner', 'size_value' => '5 × 8 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '6×10 ft Banner', 'size_value' => '6 × 10 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '8×12 ft Billboard', 'size_value' => '8 × 12 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '10×15 ft Billboard', 'size_value' => '10 × 15 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '12×18 ft Billboard', 'size_value' => '12 × 18 feet', 'size_group' => 'banner', 'is_active' => true],
            ['size_name' => '15×20 ft Mega Billboard', 'size_value' => '15 × 20 feet', 'size_group' => 'banner', 'is_active' => true],
            
            // Vinyl/Sticker Sizes
            ['size_name' => '1×1 inch Sticker', 'size_value' => '1 × 1 inch', 'size_group' => 'vinyl', 'is_active' => true],
            ['size_name' => '2×2 inch Sticker', 'size_value' => '2 × 2 inch', 'size_group' => 'vinyl', 'is_active' => true],
            ['size_name' => '3×3 inch Sticker', 'size_value' => '3 × 3 inch', 'size_group' => 'vinyl', 'is_active' => true],
            ['size_name' => '4×4 inch Sticker', 'size_value' => '4 × 4 inch', 'size_group' => 'vinyl', 'is_active' => true],
            ['size_name' => '6×6 inch Sticker', 'size_value' => '6 × 6 inch', 'size_group' => 'vinyl', 'is_active' => true],
            ['size_name' => '12×12 inch Vinyl', 'size_value' => '12 × 12 inch', 'size_group' => 'vinyl', 'is_active' => true],
            ['size_name' => '12×24 inch Vinyl', 'size_value' => '12 × 24 inch', 'size_group' => 'vinyl', 'is_active' => true],
            
            // Roll Sizes for Continuous Printing
            ['size_name' => '24" Roll', 'size_value' => '24 inch width', 'size_group' => 'roll', 'is_active' => true],
            ['size_name' => '36" Roll', 'size_value' => '36 inch width', 'size_group' => 'roll', 'is_active' => true],
            ['size_name' => '42" Roll', 'size_value' => '42 inch width', 'size_group' => 'roll', 'is_active' => true],
            ['size_name' => '60" Roll', 'size_value' => '60 inch width', 'size_group' => 'roll', 'is_active' => true],
            
            // Specialty Printing Sizes
            ['size_name' => 'CD Label', 'size_value' => '4.65 inch diameter', 'size_group' => 'specialty', 'is_active' => true],
            ['size_name' => 'Name Tag', 'size_value' => '3.5 × 2.25 inches', 'size_group' => 'specialty', 'is_active' => true],
            ['size_name' => 'ID Card', 'size_value' => '3.375 × 2.125 inches', 'size_group' => 'specialty', 'is_active' => true],
            ['size_name' => 'Bookmark', 'size_value' => '2 × 6 inches', 'size_group' => 'specialty', 'is_active' => true],
            ['size_name' => 'Door Hanger', 'size_value' => '4.25 × 11 inches', 'size_group' => 'specialty', 'is_active' => true],
            ['size_name' => 'Table Tent', 'size_value' => '4 × 6 inches', 'size_group' => 'specialty', 'is_active' => true],
            
            // Large Format Poster Sizes
            ['size_name' => '18×24 inch Poster', 'size_value' => '18 × 24 inches', 'size_group' => 'poster', 'is_active' => true],
            ['size_name' => '24×36 inch Poster', 'size_value' => '24 × 36 inches', 'size_group' => 'poster', 'is_active' => true],
            ['size_name' => '27×40 inch Poster', 'size_value' => '27 × 40 inches', 'size_group' => 'poster', 'is_active' => true],
            ['size_name' => '30×40 inch Poster', 'size_value' => '30 × 40 inches', 'size_group' => 'poster', 'is_active' => true],
            
            // Custom/Variable Sizes
            ['size_name' => 'Custom Small', 'size_value' => 'Up to 8×10 inches', 'size_group' => 'custom', 'is_active' => true],
            ['size_name' => 'Custom Medium', 'size_value' => 'Up to 12×18 inches', 'size_group' => 'custom', 'is_active' => true],
            ['size_name' => 'Custom Large', 'size_value' => 'Up to 24×36 inches', 'size_group' => 'custom', 'is_active' => true],
            ['size_name' => 'Custom XL', 'size_value' => 'Above 24×36 inches', 'size_group' => 'custom', 'is_active' => true],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }

        $this->command->info('Successfully seeded ' . count($sizes) . ' sizes for printing shop categories.');
    }
}