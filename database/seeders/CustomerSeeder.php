<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'customer_firstname' => 'ABC',
                'customer_middlename' => null,
                'customer_lastname' => 'Corporation',
                'business_name' => 'ABC Corporation',
                'customer_address' => '100 Business District, Makati City',
                'customer_email' => 'orders@abccorp.com',
                'contact_person1' => 'John Smith',
                'contact_number1' => '09123456789',
                'contact_person2' => 'Jane Doe',
                'contact_number2' => '09123456790',
                'tin' => '123-456-789-000'
            ],
            [
                'customer_firstname' => 'XYZ',
                'customer_middlename' => null,
                'customer_lastname' => 'Enterprises',
                'business_name' => 'XYZ Enterprises Inc.',
                'customer_address' => '200 Commerce Street, Quezon City',
                'customer_email' => 'info@xyzent.com',
                'contact_person1' => 'Michael Johnson',
                'contact_number1' => '09123456791',
                'contact_person2' => 'Sarah Wilson',
                'contact_number2' => '09123456792',
                'tin' => '234-567-890-000'
            ],
            [
                'customer_firstname' => 'DEF',
                'customer_middlename' => null,
                'customer_lastname' => 'Trading',
                'business_name' => 'DEF Trading Company',
                'customer_address' => '300 Trade Center, Taguig City',
                'customer_email' => 'sales@deftrading.com',
                'contact_person1' => 'Robert Brown',
                'contact_number1' => '09123456793',
                'contact_person2' => 'Lisa Davis',
                'contact_number2' => '09123456794',
                'tin' => '345-678-901-000'
            ],
            [
                'customer_firstname' => 'GHI',
                'customer_middlename' => null,
                'customer_lastname' => 'Solutions',
                'business_name' => 'GHI Solutions Ltd.',
                'customer_address' => '400 Tech Hub, Pasig City',
                'customer_email' => 'contact@ghisolutions.com',
                'contact_person1' => 'David Miller',
                'contact_number1' => '09123456795',
                'contact_person2' => 'Emily Garcia',
                'contact_number2' => '09123456796',
                'tin' => '456-789-012-000'
            ],
            [
                'customer_firstname' => 'JKL',
                'customer_middlename' => null,
                'customer_lastname' => 'Group',
                'business_name' => 'JKL Group Holdings',
                'customer_address' => '500 Corporate Plaza, Mandaluyong City',
                'customer_email' => 'admin@jklgroup.com',
                'contact_person1' => 'James Wilson',
                'contact_number1' => '09123456797',
                'contact_person2' => 'Maria Rodriguez',
                'contact_number2' => '09123456798',
                'tin' => '567-890-123-000'
            ],
            [
                'customer_firstname' => 'MNO',
                'customer_middlename' => null,
                'customer_lastname' => 'Industries',
                'business_name' => 'MNO Industries Corp.',
                'customer_address' => '600 Industrial Zone, Caloocan City',
                'customer_email' => 'orders@mnoind.com',
                'contact_person1' => 'Christopher Lee',
                'contact_number1' => '09123456799',
                'contact_person2' => 'Amanda Taylor',
                'contact_number2' => '09123456800',
                'tin' => '678-901-234-000'
            ],
            [
                'customer_firstname' => 'PQR',
                'customer_middlename' => null,
                'customer_lastname' => 'Services',
                'business_name' => 'PQR Services Inc.',
                'customer_address' => '700 Service Center, San Juan City',
                'customer_email' => 'info@pqrservices.com',
                'contact_person1' => 'Daniel Anderson',
                'contact_number1' => '09123456801',
                'contact_person2' => 'Jessica Martinez',
                'contact_number2' => '09123456802',
                'tin' => '789-012-345-000'
            ],
            [
                'customer_firstname' => 'STU',
                'customer_middlename' => null,
                'customer_lastname' => 'Marketing',
                'business_name' => 'STU Marketing Agency',
                'customer_address' => '800 Marketing Hub, Marikina City',
                'customer_email' => 'hello@stumarketing.com',
                'contact_person1' => 'Matthew Thomas',
                'contact_number1' => '09123456803',
                'contact_person2' => 'Ashley Jackson',
                'contact_number2' => '09123456804',
                'tin' => '890-123-456-000'
            ],
            [
                'customer_firstname' => 'VWX',
                'customer_middlename' => null,
                'customer_lastname' => 'Consulting',
                'business_name' => 'VWX Consulting Group',
                'customer_address' => '900 Consultancy Tower, Las Pinas City',
                'customer_email' => 'contact@vwxconsulting.com',
                'contact_person1' => 'Andrew White',
                'contact_number1' => '09123456805',
                'contact_person2' => 'Samantha Harris',
                'contact_number2' => '09123456806',
                'tin' => '901-234-567-000'
            ],
            [
                'customer_firstname' => 'YZA',
                'customer_middlename' => null,
                'customer_lastname' => 'Logistics',
                'business_name' => 'YZA Logistics Corp.',
                'customer_address' => '1000 Logistics Center, Muntinlupa City',
                'customer_email' => 'operations@yzalogistics.com',
                'contact_person1' => 'Ryan Clark',
                'contact_number1' => '09123456807',
                'contact_person2' => 'Nicole Lewis',
                'contact_number2' => '09123456808',
                'tin' => '012-345-678-000'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('Successfully seeded ' . count($customers) . ' customers.');
    }
}
