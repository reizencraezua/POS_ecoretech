<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\JobPosition;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobPositions = JobPosition::all();
        
        $employees = [
            [
                'employee_firstname' => 'Maria',
                'employee_middlename' => 'Santos',
                'employee_lastname' => 'Garcia',
                'employee_email' => 'maria.garcia@ecoretech.com',
                'employee_contact' => '09123456789',
                'employee_address' => '123 Main Street, Quezon City',
                'hire_date' => '2023-01-15',
                'job_id' => $jobPositions->where('job_title', 'Cashier')->first()->job_id
            ],
            [
                'employee_firstname' => 'Juan',
                'employee_middlename' => 'Cruz',
                'employee_lastname' => 'Reyes',
                'employee_email' => 'juan.reyes@ecoretech.com',
                'employee_contact' => '09123456790',
                'employee_address' => '456 Oak Avenue, Makati City',
                'hire_date' => '2023-02-20',
                'job_id' => $jobPositions->where('job_title', 'Production Staff')->first()->job_id
            ],
            [
                'employee_firstname' => 'Ana',
                'employee_middlename' => 'Lopez',
                'employee_lastname' => 'Martinez',
                'employee_email' => 'ana.martinez@ecoretech.com',
                'employee_contact' => '09123456791',
                'employee_address' => '789 Pine Street, Taguig City',
                'hire_date' => '2023-03-10',
                'job_id' => $jobPositions->where('job_title', 'Graphics Designer')->first()->job_id
            ],
         
            [
                'employee_firstname' => 'Sofia',
                'employee_middlename' => 'Ramos',
                'employee_lastname' => 'Torres',
                'employee_email' => 'sofia.torres@ecoretech.com',
                'employee_contact' => '09123456793',
                'employee_address' => '654 Maple Avenue, Mandaluyong City',
                'hire_date' => '2023-05-12',
                'job_id' => $jobPositions->where('job_title', 'Production Staff')->first()->job_id
            ],
            [
                'employee_firstname' => 'Miguel',
                'employee_middlename' => 'Villanueva',
                'employee_lastname' => 'Flores',
                'employee_email' => 'miguel.flores@ecoretech.com',
                'employee_contact' => '09123456794',
                'employee_address' => '987 Cedar Street, San Juan City',
                'hire_date' => '2023-06-18',
                'job_id' => $jobPositions->where('job_title', 'Graphics Designer')->first()->job_id
            ],
          
            [
                'employee_firstname' => 'Diego',
                'employee_middlename' => 'Herrera',
                'employee_lastname' => 'Morales',
                'employee_email' => 'diego.morales@ecoretech.com',
                'employee_contact' => '09123456796',
                'employee_address' => '258 Spruce Street, Caloocan City',
                'hire_date' => '2023-08-30',
                'job_id' => $jobPositions->where('job_title', 'Production Staff')->first()->job_id
            ],
            [
                'employee_firstname' => 'Valentina',
                'employee_middlename' => 'Jimenez',
                'employee_lastname' => 'Castillo',
                'employee_email' => 'valentina.castillo@ecoretech.com',
                'employee_contact' => '09123456797',
                'employee_address' => '369 Willow Avenue, Las Pinas City',
                'hire_date' => '2023-09-14',
                'job_id' => $jobPositions->where('job_title', 'Graphics Designer')->first()->job_id
            ],
            [
                'employee_firstname' => 'Sebastian',
                'employee_middlename' => 'Aguilar',
                'employee_lastname' => 'Rivera',
                'employee_email' => 'sebastian.rivera@ecoretech.com',
                'employee_contact' => '09123456798',
                'employee_address' => '741 Poplar Street, Muntinlupa City',
                'hire_date' => '2023-10-22',
                'job_id' => $jobPositions->where('job_title', 'Production Staff')->first()->job_id
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        $this->command->info('Successfully seeded ' . count($employees) . ' employees.');
    }
}
