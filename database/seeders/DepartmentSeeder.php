<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'TECH', 'description' => 'Technology Department'],
            ['name' => 'HR', 'description' => 'Human Resources Department'],
            ['name' => 'FINANCE', 'description' => 'Finance Department'],
            ['name' => 'MARKETING', 'description' => 'Marketing Department'],
            ['name' => 'OPERATIONS', 'description' => 'Operations Department'],
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create($department);
        }
    }
}
