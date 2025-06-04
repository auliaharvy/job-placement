# Backend Testing Guide

## Overview
Backend sistem job placement telah dilengkapi dengan service yang komprehensif dan endpoint testing untuk memastikan semua fungsi berjalan dengan baik.

## Service yang Telah Diselesaikan

### 1. JobMatchingService
Service untuk mencocokan pelamar dengan lowongan kerja dengan fitur:
- `findMatchingApplicants()` - Mencari pelamar yang cocok dengan lowongan
- `calculateMatchingScore()` - Menghitung skor kesesuaian (0-100)
- `getMatchCriteria()` - Mendapatkan kriteria matching
- `findMatchingJobsForApplicant()` - Mencari lowongan untuk pelamar (reverse matching)
- `findNearbyJobs()` - Mencari lowongan berdasarkan lokasi
- `getMatchingTrends()` - Analisis trend matching
- `getRecommendedApplicants()` - Rekomendasi pelamar dengan skor tertinggi
- `getRecommendedJobs()` - Rekomendasi lowongan untuk pelamar

### 2. WhatsAppService
Service untuk integrasi WhatsApp dengan fitur:
- `sendWelcomeMessage()` - Pesan selamat datang untuk pelamar baru
- `broadcastJobOpening()` - Broadcast lowongan ke pelamar yang sesuai
- `sendApplicationConfirmation()` - Konfirmasi lamaran diterima
- `sendStageUpdateNotification()` - Notifikasi update tahap seleksi
- `sendAcceptanceNotification()` - Notifikasi diterima kerja
- `sendRejectionNotification()` - Notifikasi ditolak
- `sendContractExpirationReminder()` - Pengingat kontrak akan berakhir
- `sendScheduleReminder()` - Pengingat jadwal interview/test
- `sendBulkMessages()` - Kirim pesan massal
- `getMessageTemplate()` - Template pesan
- `getMessageStats()` - Statistik pengiriman pesan
- `checkGatewayStatus()` - Cek status koneksi WhatsApp Gateway

## Endpoint Testing

### 1. Health Check
**GET** `/api/v1/test/health`

Mengecek kesehatan sistem secara keseluruhan:
- Koneksi database
- Status service Job Matching
- Status service WhatsApp
- Konfigurasi sistem

**Response Example:**
```json
{
  "timestamp": "2025-06-04T10:30:00.000Z",
  "status": "healthy",
  "services": {
    "database": {
      "status": "healthy",
      "response_time": 45.67,
      "details": {
        "connection": "successful",
        "driver": "mysql"
      }
    },
    "job_matching": {
      "status": "healthy",
      "response_time": 120.34
    },
    "whatsapp": {
      "status": "healthy",
      "response_time": 89.12
    }
  }
}
```

### 2. Job Matching Test
**GET** `/api/v1/test/job-matching`

Test fungsionalitas job matching service:
- Pencarian pelamar yang cocok
- Perhitungan skor matching
- Analisis trend
- Kriteria matching

### 3. WhatsApp Service Test
**GET** `/api/v1/test/whatsapp`

Test fungsionalitas WhatsApp service:
- Status gateway
- Statistik pesan
- Template pesan
- Method availability

### 4. Models Test
**GET** `/api/v1/test/models`

Test model dan konstanta:
- Konstanta yang didefinisikan
- Relasi antar model
- Koneksi database

### 5. Workflow Test
**GET** `/api/v1/test/workflow`

Test workflow lengkap sistem:
- Job matching workflow
- WhatsApp notification workflow
- Database operations

### 6. Generate Test Data
**POST** `/api/v1/test/generate-test-data`

Generate data testing (hanya untuk development).

## Cara Menjalankan Testing

### 1. Prerequisites
```bash
# Pastikan environment sudah setup
cp .env.example .env

# Update konfigurasi database dan WhatsApp di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job_placement
DB_USERNAME=your_username
DB_PASSWORD=your_password

WHATSAPP_GATEWAY_URL=http://localhost:3000
WHATSAPP_API_KEY=your_api_key
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Migration
```bash
php artisan migrate
php artisan db:seed
```

### 4. Start Laravel Server
```bash
php artisan serve
```

### 5. Test Endpoints

#### Health Check
```bash
curl -X GET http://localhost:8000/api/v1/test/health
```

#### Job Matching Test
```bash
curl -X GET http://localhost:8000/api/v1/test/job-matching
```

#### WhatsApp Test
```bash
curl -X GET http://localhost:8000/api/v1/test/whatsapp
```

#### Models Test
```bash
curl -X GET http://localhost:8000/api/v1/test/models
```

#### Workflow Test
```bash
curl -X GET http://localhost:8000/api/v1/test/workflow
```

## Konstanta yang Ditambahkan

### Applicant Model
```php
const STATUS_ACTIVE = 'active';
const STATUS_INACTIVE = 'inactive';
const STATUS_BLACKLISTED = 'blacklisted';

const AVAILABILITY_AVAILABLE = 'available';
const AVAILABILITY_WORKING = 'working';
const AVAILABILITY_NOT_AVAILABLE = 'not_available';

const GENDER_MALE = 'male';
const GENDER_FEMALE = 'female';
```

### Application Model
```php
const STAGE_APPLICATION = 'applied';
const STAGE_SCREENING = 'screening';
const STAGE_PSYCOTEST = 'psikotes';
const STAGE_INTERVIEW = 'interview';
const STAGE_MEDICAL = 'medical';
const STAGE_FINAL = 'final_review';
const STAGE_ACCEPTED = 'accepted';
const STAGE_REJECTED = 'rejected';
```

### JobPosting Model
```php
const EDUCATION_SD = 'sd';
const EDUCATION_SMP = 'smp';
const EDUCATION_SMA = 'sma';
const EDUCATION_D3 = 'd3';
const EDUCATION_S1 = 's1';
const EDUCATION_S2 = 's2';

const GENDER_ANY = 'any';
const GENDER_MALE_ONLY = 'male_only';
const GENDER_FEMALE_ONLY = 'female_only';

const STATUS_ACTIVE = 'published';
const STATUS_DRAFT = 'draft';
const STATUS_PAUSED = 'paused';
const STATUS_CLOSED = 'closed';
```

## Status Implementasi

### ✅ Completed
- [x] JobMatchingService - Semua method diimplementasi
- [x] WhatsAppService - Semua method diimplementasi
- [x] Model constants - Semua konstanta ditambahkan
- [x] TestController - Endpoint testing tersedia
- [x] Error handling - Comprehensive error handling
- [x] Configuration - WhatsApp configuration file

### 📋 Next Steps
1. Setup database dan run migration
2. Setup WhatsApp Gateway (Node.js service)
3. Test semua endpoint
4. Buat sample data untuk testing
5. Integration testing dengan frontend

## Error Handling

Semua service dilengkapi dengan:
- Try-catch blocks
- Logging untuk debugging
- Graceful error responses
- Fallback mechanisms

## Logging

Service akan log ke Laravel log:
- WhatsApp API errors
- Database connection issues
- Service errors
- Debug information

Cek log di `storage/logs/laravel.log`

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Pastikan database sudah dibuat
   - Cek kredensial di .env
   - Pastikan MySQL service running

2. **WhatsApp Service Error**
   - Pastikan WhatsApp Gateway running di port 3000
   - Cek API key di .env
   - Cek network connectivity

3. **Missing Constants Error**
   - Pastikan semua model sudah diupdate
   - Clear cache: `php artisan cache:clear`
   - Restart server

4. **Service Not Found Error**
   - Pastikan service terdaftar di service provider
   - Run `composer dump-autoload`

Backend sekarang siap untuk testing dan dapat diintegrasikan dengan frontend atau WhatsApp Gateway.
