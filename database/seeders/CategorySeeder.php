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
            [
                'category_name' => 'Business Cards',
                'category_description' => 'Professional business cards for corporate clients',
                'category_color' => '#3B82F6',
                'size_group' => 'small_format',
                'is_active' => true,
                'size' => '3.5" x 2"',
            ],
            [
                'category_name' => 'Flyers',
                'category_description' => 'Marketing flyers and promotional materials',
                'category_color' => '#10B981',
                'size_group' => 'paper',
                'is_active' => true,
                'size' => '8.5" x 11"',
            ],
            [
                'category_name' => 'Brochures',
                'category_description' => 'Tri-fold and bi-fold brochures for marketing',
                'category_color' => '#F59E0B',
                'size_group' => 'paper',
                'is_active' => true,
                'size' => '8.5" x 11"',
            ],
            [
                'category_name' => 'Posters',
                'category_description' => 'Large format posters for events and advertising',
                'category_color' => '#EF4444',
                'size_group' => 'poster',
                'is_active' => true,
                'size' => '18" x 24"',
            ],
            [
                'category_name' => 'Banners',
                'category_description' => 'Vinyl banners for outdoor advertising',
                'category_color' => '#8B5CF6',
                'size_group' => 'banner',
                'is_active' => true,
                'size' => '3ft x 6ft',
            ],
            [
                'category_name' => 'Stickers',
                'category_description' => 'Custom stickers and labels',
                'category_color' => '#06B6D4',
                'size_group' => 'vinyl',
                'is_active' => true,
                'size' => '2" x 2"',
            ],
            [
                'category_name' => 'Invitations',
                'category_description' => 'Wedding and event invitations',
                'category_color' => '#EC4899',
                'size_group' => 'small_format',
                'is_active' => true,
                'size' => '5.5" x 8.5"',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Successfully seeded ' . count($categories) . ' categories.');
    }
}
