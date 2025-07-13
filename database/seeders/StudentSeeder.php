<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'student_id' => '2025-0001',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@email.com',
                'middle_name' => 'Santos',
                'gender' => 'male',
                'contact_number' => '09123456789',
                'address' => '123 Main St., Manila',
            ],
            [
                'student_id' => '2025-0002',
                'first_name' => 'Maria',
                'last_name' => 'Reyes',
                'email' => 'maria.reyes@email.com',
                'middle_name' => 'Santos',
                'gender' => 'female',
                'contact_number' => '09123456790',
                'address' => '456 Oak Ave., Quezon City',
            ],
            [
                'student_id' => '2025-0003',
                'first_name' => 'Pedro',
                'last_name' => 'Santos',
                'email' => 'pedro.santos@email.com',
                'middle_name' => 'Garcia',
                'gender' => 'male',
                'contact_number' => '09123456791',
                'address' => '789 Pine St., Makati',
            ],
            [
                'student_id' => '2025-0004',
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'email' => 'ana.garcia@email.com',
                'middle_name' => 'Lopez',
                'gender' => 'female',
                'contact_number' => '09123456792',
                'address' => '321 Elm St., Pasig',
            ],
            [
                'student_id' => '2025-0005',
                'first_name' => 'Carlos',
                'last_name' => 'Lopez',
                'email' => 'carlos.lopez@email.com',
                'middle_name' => 'Martinez',
                'gender' => 'male',
                'contact_number' => '09123456793',
                'address' => '654 Maple Dr., Taguig',
            ],
            [
                'student_id' => '2025-0006',
                'first_name' => 'Sofia',
                'last_name' => 'Rodriguez',
                'email' => 'sofia.rodriguez@email.com',
                'middle_name' => 'Fernandez',
                'gender' => 'female',
                'contact_number' => '09123456794',
                'address' => '987 Cedar Ln., Mandaluyong',
            ],
        ];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }
    }
}
