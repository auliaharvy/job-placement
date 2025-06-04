<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applicants = [
            [
                'user_id' => 7, // john.doe@gmail.com
                'agent_id' => 1, // Referred by RINI001
                'nik' => '3201234567891234',
                'birth_date' => '1995-03-15',
                'birth_place' => 'Jakarta',
                'gender' => 'male',
                'religion' => 'Islam',
                'marital_status' => 'single',
                'height' => 175,
                'weight' => 70,
                'blood_type' => 'A',
                'address' => 'Jl. Merdeka No. 123, RT 001/RW 005',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12560',
                'whatsapp_number' => '081234567896',
                'emergency_contact_name' => 'Maria Doe',
                'emergency_contact_phone' => '081234567800',
                'emergency_contact_relation' => 'Ibu',
                'education_level' => 's1',
                'school_name' => 'Universitas Indonesia',
                'major' => 'Teknik Informatika',
                'graduation_year' => 2018,
                'gpa' => 3.45,
                'work_experience' => json_encode([
                    [
                        'company' => 'PT Digital Solusi',
                        'position' => 'Junior Developer',
                        'duration_months' => 24,
                        'start_date' => '2018-08',
                        'end_date' => '2020-08',
                        'responsibilities' => 'Mengembangkan aplikasi web menggunakan PHP Laravel'
                    ],
                    [
                        'company' => 'CV Tech Startup',
                        'position' => 'Frontend Developer',
                        'duration_months' => 18,
                        'start_date' => '2020-09',
                        'end_date' => '2022-03',
                        'responsibilities' => 'Mengembangkan user interface dengan React.js'
                    ]
                ]),
                'skills' => json_encode([
                    'technical' => ['PHP', 'Laravel', 'JavaScript', 'React.js', 'MySQL', 'Git'],
                    'soft_skills' => ['Komunikasi', 'Teamwork', 'Problem Solving', 'Time Management']
                ]),
                'total_work_experience_months' => 42,
                'status' => 'active',
                'work_status' => 'available',
                'available_from' => now()->toDateString(),
                'preferred_positions' => json_encode(['Web Developer', 'Full Stack Developer', 'Software Engineer']),
                'preferred_locations' => json_encode(['Jakarta', 'Bekasi', 'Tangerang']),
                'expected_salary_min' => 8000000.00,
                'expected_salary_max' => 12000000.00,
                'registration_source' => 'agent_referral',
                'profile_completed_at' => now()->subDays(5),
                'notes' => 'Kandidat potensial dengan pengalaman solid di bidang web development.',
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ],
            
            [
                'user_id' => 8, // jane.smith@gmail.com
                'agent_id' => 2, // Referred by DEDI002
                'nik' => '3201234567891235',
                'birth_date' => '1997-07-22',
                'birth_place' => 'Bandung',
                'gender' => 'female',
                'religion' => 'Kristen',
                'marital_status' => 'single',
                'height' => 160,
                'weight' => 55,
                'blood_type' => 'O',
                'address' => 'Jl. Pahlawan No. 456, RT 002/RW 008',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40123',
                'whatsapp_number' => '081234567897',
                'emergency_contact_name' => 'Robert Smith',
                'emergency_contact_phone' => '081234567801',
                'emergency_contact_relation' => 'Ayah',
                'education_level' => 'd3',
                'school_name' => 'Politeknik Negeri Bandung',
                'major' => 'Manajemen Bisnis',
                'graduation_year' => 2019,
                'gpa' => 3.67,
                'work_experience' => json_encode([
                    [
                        'company' => 'Toko Berkah Jaya',
                        'position' => 'Sales Representative',
                        'duration_months' => 30,
                        'start_date' => '2019-08',
                        'end_date' => '2022-02',
                        'responsibilities' => 'Menangani penjualan retail dan customer service'
                    ]
                ]),
                'skills' => json_encode([
                    'technical' => ['MS Office', 'Point of Sales', 'Inventory Management', 'Customer Relationship Management'],
                    'soft_skills' => ['Customer Service', 'Sales Techniques', 'Public Speaking', 'Negotiation']
                ]),
                'total_work_experience_months' => 30,
                'status' => 'active',
                'work_status' => 'available',
                'available_from' => now()->toDateString(),
                'preferred_positions' => json_encode(['Sales Executive', 'Customer Service', 'Admin Sales', 'Retail Staff']),
                'preferred_locations' => json_encode(['Bandung', 'Jakarta', 'Bekasi']),
                'expected_salary_min' => 4500000.00,
                'expected_salary_max' => 7000000.00,
                'registration_source' => 'agent_referral',
                'profile_completed_at' => now()->subDays(3),
                'notes' => 'Pengalaman sales yang baik, komunikatif dan memiliki target oriented mindset.',
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
        ];

        DB::table('applicants')->insert($applicants);
    }
}