<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Super Admin
            [
                'email' => 'admin@jobplacement.com',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'status' => 'active',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '081234567890',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Direktur
            [
                'email' => 'direktur@jobplacement.com',
                'password' => Hash::make('direktur123'),
                'role' => 'direktur',
                'status' => 'active',
                'first_name' => 'Budi',
                'last_name' => 'Santoso',
                'phone' => '081234567891',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // HR Staff
            [
                'email' => 'hr1@jobplacement.com',
                'password' => Hash::make('hr123'),
                'role' => 'hr_staff',
                'status' => 'active',
                'first_name' => 'Sari',
                'last_name' => 'Wijaya',
                'phone' => '081234567892',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'email' => 'hr2@jobplacement.com',
                'password' => Hash::make('hr123'),
                'role' => 'hr_staff',
                'status' => 'active',
                'first_name' => 'Andi',
                'last_name' => 'Pratama',
                'phone' => '081234567893',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Agents
            [
                'email' => 'agent1@jobplacement.com',
                'password' => Hash::make('agent123'),
                'role' => 'agent',
                'status' => 'active',
                'first_name' => 'Rini',
                'last_name' => 'Maharani',
                'phone' => '081234567894',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'email' => 'agent2@jobplacement.com',
                'password' => Hash::make('agent123'),
                'role' => 'agent',
                'status' => 'active',
                'first_name' => 'Dedi',
                'last_name' => 'Kurniawan',
                'phone' => '081234567895',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Sample Applicants
            [
                'email' => 'john.doe@gmail.com',
                'password' => Hash::make('3201234567891234'), // NIK as password
                'role' => 'applicant',
                'status' => 'active',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '081234567896',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'email' => 'jane.smith@gmail.com',
                'password' => Hash::make('3201234567891235'),
                'role' => 'applicant',
                'status' => 'active',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '081234567897',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}