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
                'user_id' => 5, // john.doe@gmail.com
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
        ];

        DB::table('applicants')->insert($applicants);
    }
}
