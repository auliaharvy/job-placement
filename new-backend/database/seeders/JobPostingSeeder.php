<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobPostings = [
            [
                'company_id' => 1, // PT Teknologi Maju Bersama
                'created_by' => 3, // hr1@jobplacement.com
                'title' => 'Full Stack Developer',
                'position' => 'Full Stack Developer',
                'department' => 'IT Development',
                'employment_type' => 'pkwt',
                'description' => 'Kami mencari Full Stack Developer yang berpengalaman untuk bergabung dengan tim development kami. Kandidat akan bertanggung jawab untuk mengembangkan aplikasi web end-to-end.',
                'responsibilities' => '- Mengembangkan aplikasi web menggunakan teknologi modern
- Berkolaborasi dengan tim UI/UX untuk implementasi design
- Melakukan testing dan debugging aplikasi
- Menulis dokumentasi teknis
- Mengoptimalkan performa aplikasi',
                'requirements' => '- Minimal S1 Teknik Informatika atau bidang terkait
- Pengalaman minimal 2 tahun sebagai Full Stack Developer
- Menguasai PHP Laravel, JavaScript, React.js
- Familiar dengan database MySQL/PostgreSQL
- Memahami Git version control',
                'benefits' => '- Gaji kompetitif sesuai pengalaman
- BPJS Kesehatan dan Ketenagakerjaan
- Annual bonus berdasarkan performance
- Flexible working hours
- Training dan sertifikasi',
                'work_location' => 'Jl. Sudirman No. 123, Lt. 15',
                'work_city' => 'Jakarta Selatan',
                'work_province' => 'DKI Jakarta',
                'work_arrangement' => 'hybrid',
                'salary_min' => 8000000.00,
                'salary_max' => 15000000.00,
                'salary_negotiable' => true,
                'contract_duration_months' => 12,
                'start_date' => now()->addDays(14)->toDateString(),
                'application_deadline' => now()->addDays(30)->toDateString(),
                'required_education_levels' => json_encode(['s1']),
                'min_age' => 22,
                'max_age' => 35,
                'preferred_genders' => null,
                'min_experience_months' => 24,
                'required_skills' => json_encode(['PHP', 'Laravel', 'JavaScript', 'React.js', 'MySQL']),
                'preferred_skills' => json_encode(['Node.js', 'Vue.js', 'PostgreSQL', 'AWS', 'Docker']),
                'preferred_locations' => json_encode(['Jakarta', 'Bekasi', 'Tangerang', 'Depok', 'Bogor']),
                'total_positions' => 2,
                'total_applications' => 0,
                'total_hired' => 0,
                'status' => 'published',
                'published_at' => now(),
                'auto_broadcast_whatsapp' => true,
                'priority' => 'high',
                'is_featured' => true,
                'internal_notes' => 'Posisi urgent untuk project besar client. Prioritaskan kandidat dengan pengalaman Laravel dan React.',
                'matching_algorithm_weights' => json_encode([
                    'education' => 0.2,
                    'experience' => 0.3,
                    'skills' => 0.3,
                    'location' => 0.1,
                    'salary_expectation' => 0.1
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'company_id' => 2, // CV Berkah Mandiri
                'created_by' => 4, // hr2@jobplacement.com
                'title' => 'Operator Produksi',
                'position' => 'Machine Operator',
                'department' => 'Production',
                'employment_type' => 'pkwt',
                'description' => 'Dibutuhkan operator produksi untuk mengoperasikan mesin-mesin produksi di pabrik komponen elektronik. Kandidat akan bekerja dalam shift sistem.',
                'responsibilities' => '- Mengoperasikan mesin produksi sesuai SOP
- Melakukan quality control produk
- Melakukan maintenance basic mesin
- Mencatat hasil produksi harian
- Menjaga kebersihan area kerja',
                'requirements' => '- Minimal SMK/SMA sederajat
- Pengalaman di bidang manufaktur minimal 1 tahun
- Mampu bekerja dalam sistem shift
- Teliti dan bertanggung jawab
- Sehat jasmani dan rohani',
                'benefits' => '- Gaji pokok + tunjangan shift
- BPJS Kesehatan dan Ketenagakerjaan
- Makan siang gratis
- Transport allowance
- Overtime pay',
                'work_location' => 'Kawasan Industri MM2100, Blok A-15',
                'work_city' => 'Bekasi',
                'work_province' => 'Jawa Barat',
                'work_arrangement' => 'onsite',
                'salary_min' => 4500000.00,
                'salary_max' => 6000000.00,
                'salary_negotiable' => false,
                'contract_duration_months' => 12,
                'start_date' => now()->addDays(7)->toDateString(),
                'application_deadline' => now()->addDays(21)->toDateString(),
                'required_education_levels' => json_encode(['sma', 'smk']),
                'min_age' => 18,
                'max_age' => 40,
                'preferred_genders' => json_encode(['male']),
                'min_experience_months' => 12,
                'required_skills' => json_encode(['Machine Operation', 'Quality Control', 'Basic Maintenance']),
                'preferred_skills' => json_encode(['Manufacturing Experience', 'Electronics Knowledge', 'Safety Procedures']),
                'preferred_locations' => json_encode(['Bekasi', 'Karawang', 'Cikarang', 'Jakarta Timur']),
                'total_positions' => 5,
                'total_applications' => 0,
                'total_hired' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'auto_broadcast_whatsapp' => true,
                'priority' => 'normal',
                'is_featured' => false,
                'internal_notes' => 'Butuh segera untuk mengganti operator yang resign. Prioritas lokasi Bekasi dan sekitarnya.',
                'matching_algorithm_weights' => json_encode([
                    'education' => 0.15,
                    'experience' => 0.25,
                    'skills' => 0.25,
                    'location' => 0.25,
                    'age' => 0.1
                ]),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            
            [
                'company_id' => 3, // PT Retail Nusantara
                'created_by' => 3, // hr1@jobplacement.com
                'title' => 'Sales Executive',
                'position' => 'Sales Executive',
                'department' => 'Sales & Marketing',
                'employment_type' => 'pkwt',
                'description' => 'Kesempatan berkarir sebagai Sales Executive di perusahaan retail terkemuka. Kandidat akan bertanggung jawab untuk pencapaian target penjualan dan pengembangan customer base.',
                'responsibilities' => '- Mencapai target penjualan bulanan dan tahunan
- Mencari dan mengembangkan prospek customer baru
- Membangun dan memelihara hubungan dengan existing customers
- Melakukan presentasi produk kepada customer
- Menyiapkan proposal dan kontrak penjualan',
                'requirements' => '- Minimal D3 semua jurusan
- Pengalaman sales minimal 2 tahun
- Memiliki kemampuan komunikasi dan presentasi yang baik
- Target oriented dan results driven
- Memiliki kendaraan bermotor dan SIM C',
                'benefits' => '- Gaji pokok + komisi penjualan
- BPJS Kesehatan dan Ketenagakerjaan
- Tunjangan transport dan komunikasi
- Bonus achievement
- Career development program',
                'work_location' => 'Jl. Gatot Subroto No. 88',
                'work_city' => 'Jakarta Pusat',
                'work_province' => 'DKI Jakarta',
                'work_arrangement' => 'onsite',
                'salary_min' => 5000000.00,
                'salary_max' => 8000000.00,
                'salary_negotiable' => true,
                'contract_duration_months' => 12,
                'start_date' => now()->addDays(10)->toDateString(),
                'application_deadline' => now()->addDays(25)->toDateString(),
                'required_education_levels' => json_encode(['d3', 's1']),
                'min_age' => 22,
                'max_age' => 35,
                'preferred_genders' => null,
                'min_experience_months' => 24,
                'required_skills' => json_encode(['Sales Techniques', 'Customer Service', 'Communication', 'Presentation']),
                'preferred_skills' => json_encode(['B2B Sales', 'Retail Experience', 'CRM Systems', 'Negotiation']),
                'preferred_locations' => json_encode(['Jakarta', 'Bekasi', 'Tangerang', 'Depok']),
                'total_positions' => 3,
                'total_applications' => 0,
                'total_hired' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'auto_broadcast_whatsapp' => true,
                'priority' => 'normal',
                'is_featured' => false,
                'internal_notes' => 'Ekspansi ke market baru, butuh sales yang agresif dan berpengalaman.',
                'matching_algorithm_weights' => json_encode([
                    'education' => 0.15,
                    'experience' => 0.35,
                    'skills' => 0.25,
                    'location' => 0.15,
                    'communication' => 0.1
                ]),
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],
        ];

        DB::table('job_postings')->insert($jobPostings);
    }
}