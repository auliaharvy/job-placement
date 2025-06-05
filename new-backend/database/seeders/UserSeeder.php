<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'admin@jobplacement.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@jobplacement.com',
                'phone' => '+6281234567890',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create Direktur
        User::updateOrCreate(
            ['email' => 'direktur@jobplacement.com'],
            [
                'first_name' => 'Direktur',
                'last_name' => 'Utama',
                'email' => 'direktur@jobplacement.com',
                'phone' => '+6281234567891',
                'password' => Hash::make('password123'),
                'role' => 'direktur',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create HR Staff
        User::updateOrCreate(
            ['email' => 'hr@jobplacement.com'],
            [
                'first_name' => 'HR',
                'last_name' => 'Staff',
                'email' => 'hr@jobplacement.com',
                'phone' => '+6281234567892',
                'password' => Hash::make('password123'),
                'role' => 'hr_staff',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create Test Agent
        User::updateOrCreate(
            ['email' => 'agent@jobplacement.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Agent',
                'email' => 'agent@jobplacement.com',
                'phone' => '+6281234567893',
                'password' => Hash::make('password123'),
                'role' => 'agent',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Create Test Applicant
        User::updateOrCreate(
            ['email' => 'applicant@jobplacement.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Applicant',
                'email' => 'applicant@jobplacement.com',
                'phone' => '+6281234567894',
                'password' => Hash::make('password123'),
                'role' => 'applicant',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users seeded successfully!');
    }
}
