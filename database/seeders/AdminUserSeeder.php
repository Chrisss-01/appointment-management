<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@uv.edu.ph'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@uv.edu.ph',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed a default staff member for testing (nurse)
        User::updateOrCreate(
            ['email' => 'staff@uv.edu.ph'],
            [
                'name' => 'Maria Santos RN',
                'email' => 'staff@uv.edu.ph',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'staff_type' => 'nurse',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed a default doctor for testing
        User::updateOrCreate(
            ['email' => 'doctor@uv.edu.ph'],
            [
                'name' => 'Dr. Jose Rizal',
                'email' => 'doctor@uv.edu.ph',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'staff_type' => 'doctor',
                'license_number' => '0012345',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed a test student
        User::updateOrCreate(
            ['email' => 'student@uv.edu.ph'],
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'student@uv.edu.ph',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => '2024-00001',
                'department' => 'ceta',
                'program' => 'BS CS',
                'year_level' => '3rd-year',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
