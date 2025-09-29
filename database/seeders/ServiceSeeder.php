<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Unit;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories and units for relationships
        $graphicDesignCategory = Category::where('category_name', 'Graphic Design')->first();
        $logoDesignCategory = Category::where('category_name', 'Logo Design')->first();
        $layoutDesignCategory = Category::where('category_name', 'Layout Design')->first();
        $photoEditingCategory = Category::where('category_name', 'Photo Editing')->first();

        $hoursUnit = Unit::where('unit_name', 'Pieces')->first(); // Using pieces for service units
        $projectUnit = Unit::where('unit_name', 'Pieces')->first();

        $services = [
            // DESIGN SERVICES (based on layout capabilities shown in image)
            [
                'service_name' => 'T-Shirt Design Service',
                'description' => 'Custom t-shirt design creation with unlimited revisions',
                'base_fee' => 500.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Logo Design Service',
                'description' => 'Professional logo design for businesses and organizations',
                'base_fee' => 1500.00,
                'category_id' => $logoDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Business Card Design',
                'description' => 'Professional business card layout and design service',
                'base_fee' => 800.00,
                'category_id' => $layoutDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Flyer Design Service',
                'description' => 'Eye-catching flyer design for promotions and events',
                'base_fee' => 600.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Banner Design Service',
                'description' => 'Large format banner and tarpaulin design service',
                'base_fee' => 1000.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Calendar Design Service',
                'description' => 'Custom calendar design with photos and branding',
                'base_fee' => 1200.00,
                'category_id' => $layoutDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Letterhead Design',
                'description' => 'Professional letterhead design with company branding',
                'base_fee' => 700.00,
                'category_id' => $layoutDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Poster Design Service',
                'description' => 'Creative poster design for events and promotions',
                'base_fee' => 800.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],

            // EMBROIDERY SERVICES (visible in image)
            [
                'service_name' => 'Embroidery Service',
                'description' => 'Professional embroidery service for logos and designs',
                'base_fee' => 150.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $hoursUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 200.00,
                'layout_description' => 'Embroidery digitization and setup',
            ],

            // DTF & RUBBERIZED PRINTING (visible in image)
            [
                'service_name' => 'DTF Printing Service',
                'description' => 'Direct-to-Film printing service for various materials',
                'base_fee' => 80.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $hoursUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 100.00,
                'layout_description' => 'DTF file preparation and setup',
            ],
            [
                'service_name' => 'Rubberized Printing Service',
                'description' => 'Rubber printing service for durable designs',
                'base_fee' => 120.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $hoursUnit?->unit_id,
                'requires_layout' => true,
                'layout_price' => 150.00,
                'layout_description' => 'Rubber printing setup and preparation',
            ],

            // PHOTO EDITING SERVICES
            [
                'service_name' => 'Photo Editing Service',
                'description' => 'Professional photo editing and retouching service',
                'base_fee' => 300.00,
                'category_id' => $photoEditingCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
            [
                'service_name' => 'Photo Restoration Service',
                'description' => 'Restore old and damaged photos to original quality',
                'base_fee' => 500.00,
                'category_id' => $photoEditingCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],

            // LAYOUT SERVICES
            [
                'service_name' => 'Layout Design Service',
                'description' => 'Professional layout design for various print materials',
                'base_fee' => 400.00,
                'category_id' => $layoutDesignCategory?->category_id,
                'unit_id' => $projectUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],

            // CONSULTATION SERVICES
            [
                'service_name' => 'Design Consultation',
                'description' => 'Professional design consultation and advice',
                'base_fee' => 200.00,
                'category_id' => $graphicDesignCategory?->category_id,
                'unit_id' => $hoursUnit?->unit_id,
                'requires_layout' => false,
                'layout_price' => 0.00,
                'layout_description' => null,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->command->info('Successfully seeded ' . count($services) . ' services based on Ecoretech printing shop capabilities.');
    }
}