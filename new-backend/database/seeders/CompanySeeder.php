<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'PT Teknologi Maju Bersama',
                'industry' => 'Technology',
                'description' => 'Perusahaan teknologi yang bergerak di bidang pengembangan software dan sistem informasi.',
                'email' => 'hr@teknologimaju.com',
                'phone' => '021-8765-4321',
                'website' => 'https://www.teknologimaju.com',
                'address' => 'Jl. Sudirman No. 123, Lt. 15',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'contact_person_name' => 'Diana Sari',
                'contact_person_position' => 'HR Manager',
                'contact_person_phone' => '081234567801',
                'contact_person_email' => 'diana@teknologimaju.com',
                'status' => 'active',
                'company_metrics' => json_encode([
                    'total_employees' => 250,
                    'revenue_range' => '10-50M',
                    'established_year' => 2015,
                    'business_type' => 'Software Development'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name' => 'CV Berkah Mandiri',
                'industry' => 'Manufacturing',
                'description' => 'Perusahaan manufaktur yang memproduksi komponen elektronik dan spare part otomotif.',
                'email' => 'recruitment@berkahmandiri.co.id',
                'phone' => '021-5555-7777',
                'website' => 'https://www.berkahmandiri.co.id',
                'address' => 'Kawasan Industri MM2100, Blok A-15',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'postal_code' => '17520',
                'contact_person_name' => 'Ahmad Fauzi',
                'contact_person_position' => 'Recruitment Specialist',
                'contact_person_phone' => '081234567802',
                'contact_person_email' => 'ahmad@berkahmandiri.co.id',
                'status' => 'active',
                'company_metrics' => json_encode([
                    'total_employees' => 150,
                    'revenue_range' => '5-10M',
                    'established_year' => 2010,
                    'business_type' => 'Manufacturing'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name' => 'PT Retail Nusantara',
                'industry' => 'Retail',
                'description' => 'Jaringan ritel modern dengan 50+ outlet di seluruh Indonesia.',
                'email' => 'hrd@retailnusantara.com',
                'phone' => '021-3333-4444',
                'website' => 'https://www.retailnusantara.com',
                'address' => 'Jl. Gatot Subroto No. 88',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '12930',
                'contact_person_name' => 'Lisa Permata',
                'contact_person_position' => 'HRD Manager',
                'contact_person_phone' => '081234567803',
                'contact_person_email' => 'lisa@retailnusantara.com',
                'status' => 'active',
                'company_metrics' => json_encode([
                    'total_employees' => 500,
                    'revenue_range' => '50-100M',
                    'established_year' => 2008,
                    'business_type' => 'Retail Chain'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name' => 'Hotel Grand Permata',
                'industry' => 'Hospitality',
                'description' => 'Hotel bintang 4 dengan fasilitas lengkap untuk bisnis dan leisure.',
                'email' => 'recruitment@grandpermata.com',
                'phone' => '021-6666-8888',
                'website' => 'https://www.grandpermata.com',
                'address' => 'Jl. Thamrin No. 45',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10230',
                'contact_person_name' => 'Robert Tan',
                'contact_person_position' => 'HR Coordinator',
                'contact_person_phone' => '081234567804',
                'contact_person_email' => 'robert@grandpermata.com',
                'status' => 'active',
                'company_metrics' => json_encode([
                    'total_employees' => 120,
                    'revenue_range' => '5-10M',
                    'established_year' => 2012,
                    'business_type' => 'Hospitality'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name' => 'PT Logistik Express',
                'industry' => 'Logistics',
                'description' => 'Perusahaan jasa logistik dan pengiriman dengan jaringan nasional.',
                'email' => 'hr@logistikexpress.co.id',
                'phone' => '021-7777-9999',
                'website' => 'https://www.logistikexpress.co.id',
                'address' => 'Jl. Raya Bekasi Km. 18',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'postal_code' => '17111',
                'contact_person_name' => 'Indra Wijaya',
                'contact_person_position' => 'Recruitment Manager',
                'contact_person_phone' => '081234567805',
                'contact_person_email' => 'indra@logistikexpress.co.id',
                'status' => 'active',
                'company_metrics' => json_encode([
                    'total_employees' => 300,
                    'revenue_range' => '20-50M',
                    'established_year' => 2005,
                    'business_type' => 'Logistics & Transportation'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('companies')->insert($companies);
    }
}