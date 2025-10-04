<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Job;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Size;
use App\Models\Unit;
use App\Models\Supplier;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample customers
        Customer::create([
            'customer_firstname' => 'John',
            'customer_lastname' => 'Doe',
            'customer_email' => 'john.doe@example.com',
            'contact_person1' => 'John Doe',
            'contact_number1' => '09123456789',
            'customer_address' => '123 Main Street, City',
        ]);

        Customer::create([
            'customer_firstname' => 'Jane',
            'customer_lastname' => 'Smith',
            'customer_email' => 'jane.smith@example.com',
            'contact_person1' => 'Jane Smith',
            'contact_number1' => '09987654321',
            'customer_address' => '456 Oak Avenue, Town',
        ]);

        // Create sample job positions
        $adminJob = Job::create([
            'job_title' => 'Administrator',
            'job_description' => 'System administrator',
        ]);

        $designerJob = Job::create([
            'job_title' => 'Graphics Designer',
            'job_description' => 'Layout and design specialist',
        ]);

        $productionJob = Job::create([
            'job_title' => 'Production Staff',
            'job_description' => 'Production and printing specialist',
        ]);

        // Create sample employees
        Employee::create([
            'employee_firstname' => 'Admin',
            'employee_lastname' => 'User',
            'employee_email' => 'admin@ecoretech.com',
            'employee_contact' => '09111111111',
            'employee_address' => 'Admin Office',
            'hire_date' => now(),
            'job_id' => $adminJob->job_id,
        ]);

        Employee::create([
            'employee_firstname' => 'Design',
            'employee_lastname' => 'Specialist',
            'employee_email' => 'designer@ecoretech.com',
            'employee_contact' => '09222222222',
            'employee_address' => 'Design Department',
            'hire_date' => now(),
            'job_id' => $designerJob->job_id,
        ]);

        Employee::create([
            'employee_firstname' => 'Production',
            'employee_lastname' => 'Manager',
            'employee_email' => 'production@ecoretech.com',
            'employee_contact' => '09333333333',
            'employee_address' => 'Production Floor',
            'hire_date' => now(),
            'job_id' => $productionJob->job_id,
        ]);

        // Create sample categories
        $printingCategory = Category::create([
            'category_name' => 'Printing Services',
            'category_description' => 'Various printing services',
        ]);

        $designCategory = Category::create([
            'category_name' => 'Design Services',
            'category_description' => 'Layout and design services',
        ]);

        // Create sample products
        Product::create([
            'product_name' => 'Business Cards',
            'product_description' => 'High-quality business card printing',
            'base_price' => 50.00,
            'category_id' => $printingCategory->category_id,
            'size_id' => Size::where('size_name', '3.5" x 2"')->first()?->size_id,
            'unit_id' => Unit::where('unit_name', 'Pieces')->first()?->unit_id,
        ]);

        Product::create([
            'product_name' => 'Flyers',
            'product_description' => 'A4 size flyer printing',
            'base_price' => 5.00,
            'category_id' => $printingCategory->category_id,
            'size_id' => Size::where('size_name', '8.5" x 11"')->first()?->size_id,
            'unit_id' => Unit::where('unit_name', 'Pieces')->first()?->unit_id,
        ]);

        // Create sample services
        Service::create([
            'service_name' => 'Logo Design',
            'description' => 'Custom logo design service',
            'base_fee' => 500.00,
            'layout_price' => 0.00,
            'requires_layout' => false,
            'category_id' => $designCategory->category_id,
            'size_id' => null,
            'unit_id' => Unit::where('unit_name', 'Service')->first()?->unit_id,
        ]);

        Service::create([
            'service_name' => 'Layout Design',
            'description' => 'Professional layout design',
            'base_fee' => 200.00,
            'layout_price' => 100.00,
            'requires_layout' => true,
            'category_id' => $designCategory->category_id,
            'size_id' => null,
            'unit_id' => Unit::where('unit_name', 'Service')->first()?->unit_id,
        ]);

        // Create sample supplier
        Supplier::create([
            'supplier_name' => 'Paper Supply Co.',
            'supplier_contact' => '09444444444',
            'supplier_email' => 'supply@paperco.com',
            'supplier_address' => 'Supply Warehouse',
        ]);
    }
}
