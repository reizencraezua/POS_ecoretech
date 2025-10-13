<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;

class JobPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobPositions = [
            [
                'job_title' => 'Cashier',
                'job_description' => 'Handles customer transactions, payments, and order processing at the front desk.'
            ],
            [
                'job_title' => 'Production Staff',
                'job_description' => 'Responsible for printing, cutting, and finishing production tasks.'
            ],
            [
                'job_title' => 'Graphics Designer',
                'job_description' => 'Creates and designs layouts, graphics, and visual materials for printing projects.'
            ]
        ];

        foreach ($jobPositions as $position) {
            Job::create($position);
        }

        $this->command->info('Successfully seeded ' . count($jobPositions) . ' job positions.');
    }
}
