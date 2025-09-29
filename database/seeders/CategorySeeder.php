<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Apparel & Clothing
            ['category_name' => 'T-Shirts', 'category_description' => 'Custom printed t-shirts and apparel', 'is_active' => true],
            ['category_name' => 'Polo Shirts', 'category_description' => 'Embroidered and printed polo shirts', 'is_active' => true],
            ['category_name' => 'Hoodies & Jackets', 'category_description' => 'Custom hoodies, jackets and outerwear', 'is_active' => true],
            ['category_name' => 'Uniforms', 'category_description' => 'Corporate and school uniforms', 'is_active' => true],
            ['category_name' => 'Caps & Hats', 'category_description' => 'Embroidered caps and custom headwear', 'is_active' => true],
            
            // Drinkware
            ['category_name' => 'Coffee Mugs', 'category_description' => 'Ceramic and sublimation mugs', 'is_active' => true],
            ['category_name' => 'Travel Mugs', 'category_description' => 'Insulated travel mugs and tumblers', 'is_active' => true],
            ['category_name' => 'Water Bottles', 'category_description' => 'Custom water bottles and sports bottles', 'is_active' => true],
            ['category_name' => 'Glassware', 'category_description' => 'Custom glasses and drinkware', 'is_active' => true],
            
            // Paper Products
            ['category_name' => 'Business Cards', 'category_description' => 'Professional business cards', 'is_active' => true],
            ['category_name' => 'Flyers & Brochures', 'category_description' => 'Marketing flyers and brochures', 'is_active' => true],
            ['category_name' => 'Letterheads', 'category_description' => 'Company letterheads and stationery', 'is_active' => true],
            ['category_name' => 'Certificates', 'category_description' => 'Awards and certificates printing', 'is_active' => true],
            ['category_name' => 'Invitations', 'category_description' => 'Wedding and event invitations', 'is_active' => true],
            ['category_name' => 'Calendars', 'category_description' => 'Custom calendars and planners', 'is_active' => true],
            
            // Large Format Printing
            ['category_name' => 'Banners', 'category_description' => 'Vinyl banners and outdoor signage', 'is_active' => true],
            ['category_name' => 'Tarpaulins', 'category_description' => 'Waterproof tarpaulin printing', 'is_active' => true],
            ['category_name' => 'Billboards', 'category_description' => 'Large format billboard printing', 'is_active' => true],
            ['category_name' => 'Posters', 'category_description' => 'Large format posters and prints', 'is_active' => true],
            ['category_name' => 'Backdrops', 'category_description' => 'Event backdrops and photo walls', 'is_active' => true],
            
            // Signage & Displays
            ['category_name' => 'Acrylic Signs', 'category_description' => 'Acrylic and perspex signage', 'is_active' => true],
            ['category_name' => 'Metal Signs', 'category_description' => 'Aluminum and steel signage', 'is_active' => true],
            ['category_name' => 'LED Signs', 'category_description' => 'Illuminated LED signage', 'is_active' => true],
            ['category_name' => 'Window Graphics', 'category_description' => 'Window decals and graphics', 'is_active' => true],
            ['category_name' => 'Safety Signs', 'category_description' => 'Workplace safety and warning signs', 'is_active' => true],
            
            // Stickers & Labels
            ['category_name' => 'Vinyl Stickers', 'category_description' => 'Custom vinyl stickers and decals', 'is_active' => true],
            ['category_name' => 'Product Labels', 'category_description' => 'Product labeling and packaging', 'is_active' => true],
            ['category_name' => 'Car Decals', 'category_description' => 'Vehicle graphics and decals', 'is_active' => true],
            ['category_name' => 'Wall Decals', 'category_description' => 'Interior wall graphics and decals', 'is_active' => true],
            
            // Promotional Items
            ['category_name' => 'Keychains', 'category_description' => 'Custom keychains and accessories', 'is_active' => true],
            ['category_name' => 'Magnets', 'category_description' => 'Promotional magnets and displays', 'is_active' => true],
            ['category_name' => 'Pens & Pencils', 'category_description' => 'Custom writing instruments', 'is_active' => true],
            ['category_name' => 'Bags & Totes', 'category_description' => 'Custom bags and promotional totes', 'is_active' => true],
            ['category_name' => 'USB Drives', 'category_description' => 'Custom USB drives and tech accessories', 'is_active' => true],
            
            // Specialty Items
            ['category_name' => 'ID Cards', 'category_description' => 'Employee and membership ID cards', 'is_active' => true],
            ['category_name' => 'Name Tags', 'category_description' => 'Professional name tags and badges', 'is_active' => true],
            ['category_name' => 'Table Tents', 'category_description' => 'Restaurant table tents and displays', 'is_active' => true],
            ['category_name' => 'Door Hangers', 'category_description' => 'Marketing door hangers', 'is_active' => true],
            ['category_name' => 'Bookmarks', 'category_description' => 'Custom bookmarks and reading accessories', 'is_active' => true],
            
            // Photo Products
            ['category_name' => 'Photo Prints', 'category_description' => 'High-quality photo printing', 'is_active' => true],
            ['category_name' => 'Canvas Prints', 'category_description' => 'Canvas photo printing and art', 'is_active' => true],
            ['category_name' => 'Photo Books', 'category_description' => 'Custom photo books and albums', 'is_active' => true],
            ['category_name' => 'Frames', 'category_description' => 'Custom frames and mounting', 'is_active' => true],
            
            // Digital Services
            ['category_name' => 'Graphic Design', 'category_description' => 'Custom graphic design services', 'is_active' => true],
            ['category_name' => 'Logo Design', 'category_description' => 'Professional logo design', 'is_active' => true],
            ['category_name' => 'Layout Design', 'category_description' => 'Print layout and design services', 'is_active' => true],
            ['category_name' => 'Photo Editing', 'category_description' => 'Photo retouching and editing', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Successfully seeded ' . count($categories) . ' categories for printing shop.');
    }
}