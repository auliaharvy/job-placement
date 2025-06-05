# Job Placement System - Postman API Testing Guide

## 📋 Overview

Collection Postman ini menyediakan semua endpoint API untuk Job Placement System Backend yang dibuat dengan Laravel. Collection ini mencakup semua fitur utama sistem seperti authentication, manajemen pelamar, lowongan kerja, aplikasi, dan integrasi WhatsApp.

## 🚀 Quick Setup

### 1. Import Files ke Postman

1. **Import Collection:**
   - Buka Postman
   - Klik "Import" 
   - Drag & drop file: `Job-Placement-System-API.postman_collection.json`

2. **Import Environment:**
   - Klik "Import" lagi
   - Drag & drop file: `Job-Placement-System-Local.postman_environment.json`

3. **Set Environment:**
   - Pilih environment "Job Placement System - Local Development" di dropdown kanan atas

### 2. Start Backend Server

Pastikan backend Laravel sudah berjalan:

```bash
cd new-backend
php artisan serve
# Server akan running di http://localhost:8000
```

## 🔧 Configuration

### Environment Variables

| Variable | Value | Description |
|----------|-------|-------------|
| `base_url` | `http://localhost:8000/api/v1` | Base URL untuk API |
| `auth_token` | (auto-filled) | JWT token dari login |
| `admin_email` | `admin@jobplacement.com` | Email admin default |
| `admin_password` | `password123` | Password admin default |
| `test_phone` | `+6281234567890` | Nomor test untuk WhatsApp |
| `test_email` | `test@jobplacement.com` | Email test |

## 📚 API Collections

### 🔐 Authentication
- **Login** - Mendapatkan token autentikasi
- **Register Applicant** - Daftar pelamar baru
- **Get Profile** - Ambil data profil user
- **Logout** - Keluar dari sistem

### 📊 Dashboard
- **Get Dashboard Data** - Data overview untuk dashboard

### 👥 Applicants
- **Get All Applicants** - List semua pelamar (pagination)
- **Create Applicant** - Tambah pelamar baru
- **Get Applicant Detail** - Detail pelamar berdasarkan ID
- **Update Applicant** - Update data pelamar
- **Generate QR Code** - Generate QR untuk registrasi

### 💼 Job Postings
- **Get All Jobs** - List semua lowongan (admin)
- **Get Public Jobs** - List lowongan publik (tanpa auth)
- **Create Job Posting** - Buat lowongan baru
- **Get Job Detail** - Detail lowongan berdasarkan ID

### 📝 Applications
- **Get All Applications** - List semua lamaran
- **Create Application** - Buat lamaran baru
- **Progress Application Stage** - Lanjut ke tahap berikutnya
- **Accept Application** - Terima lamaran
- **Reject Application** - Tolak lamaran

### 📱 WhatsApp
- **Get WhatsApp Status** - Status koneksi WhatsApp
- **Start WhatsApp Session** - Mulai sesi WhatsApp
- **Send Test Message** - Kirim pesan test
- **Test Workflow** - Test workflow notifikasi

### 🧪 Testing
- **Health Check** - Cek kesehatan system
- **Test Models** - Test koneksi database & model
- **Test Job Matching** - Test algoritma matching
- **Generate Test Data** - Generate data dummy

## 🎯 Testing Workflow

### 1. Basic Authentication Flow

1. **Login** menggunakan request "Login" dengan credentials:
   ```json
   {
       "email": "admin@jobplacement.com",
       "password": "password123"
   }
   ```

2. Token akan otomatis tersimpan ke environment variable `auth_token`

3. Semua request selanjutnya akan otomatis menggunakan token ini

### 2. Testing CRUD Operations

#### Applicants:
1. **Create** - Tambah pelamar baru
2. **Read** - Get All Applicants → Get Applicant Detail
3. **Update** - Update data pelamar
4. **Delete** - (Available in full collection)

#### Job Postings:
1. **Create** - Buat lowongan baru
2. **Read** - Get All Jobs → Get Job Detail
3. **Update** - (Available in full collection)
4. **Delete** - (Available in full collection)

#### Applications:
1. **Create** - Buat lamaran baru
2. **Progress** - Maju ke tahap seleksi berikutnya
3. **Accept/Reject** - Terima atau tolak lamaran

### 3. WhatsApp Integration Testing

1. **Check Status** - Pastikan WhatsApp gateway siap
2. **Start Session** - Mulai sesi WhatsApp (akan generate QR code)
3. **Send Test Message** - Kirim pesan ke nomor test
4. **Test Workflow** - Test notifikasi otomatis

### 4. System Health Testing

1. **Health Check** - Pastikan API berjalan normal
2. **Test Models** - Pastikan database connection OK
3. **Generate Test Data** - Populate database dengan data dummy

## 📝 Sample Request Bodies

### Login Request
```json
{
    "email": "admin@jobplacement.com",
    "password": "password123"
}
```

### Create Applicant
```json
{
    "full_name": "John Doe",
    "email": "john.doe@email.com",
    "phone": "+6281234567890",
    "date_of_birth": "1995-05-15",
    "gender": "male",
    "education_level": "bachelor",
    "work_experience_years": 3,
    "current_status": "available",
    "address": {
        "province": "DKI Jakarta",
        "city": "Jakarta Selatan",
        "district": "Kebayoran Baru",
        "postal_code": "12110",
        "detail": "Jl. Senayan No. 123"
    },
    "skills": ["JavaScript", "React", "Node.js"]
}
```

### Create Job Posting
```json
{
    "title": "Full Stack Developer",
    "company_id": 1,
    "location": "Jakarta Selatan",
    "job_type": "full_time",
    "experience_level": "mid",
    "salary_min": 8000000,
    "salary_max": 15000000,
    "description": "Mencari Full Stack Developer yang berpengalaman dalam pengembangan web aplikasi.",
    "requirements": "Minimal 2 tahun pengalaman, menguasai React, Node.js, dan database.",
    "status": "active",
    "closing_date": "2024-12-31",
    "skills_required": ["JavaScript", "React", "Node.js", "MongoDB"]
}
```

### Create Application
```json
{
    "job_posting_id": 1,
    "applicant_id": 1,
    "cover_letter": "Saya tertarik dengan posisi ini karena...",
    "expected_salary": 12000000
}
```

## 🔒 Authentication & Authorization

### Token Management
- Login request otomatis menyimpan token ke environment
- Semua protected endpoint otomatis menggunakan Bearer token
- Token berlaku sampai logout atau expired

### Role-based Access
- **Super Admin**: Full access ke semua endpoint
- **Direktur**: Management level access
- **HR Staff**: HR operations access
- **Agent**: Limited access untuk agent features
- **Applicant**: Self-service access

## 🧪 Testing Scenarios

### Scenario 1: Complete Application Flow
1. **Register new applicant** via `/auth/register/applicant`
2. **Create job posting** via `/jobs`
3. **Apply for job** via `/applications`
4. **Progress through stages**: screening → psikotes → interview → medical
5. **Accept application** and create placement

### Scenario 2: WhatsApp Integration
1. **Check WhatsApp status** via `/whatsapp/status`
2. **Start session** via `/whatsapp/start-session`
3. **Send test message** via `/whatsapp/send-test-message`
4. **Test workflow** via `/whatsapp/test-workflow`

### Scenario 3: Admin Management
1. **Login as admin** via `/auth/login`
2. **View dashboard** via `/dashboard`
3. **Manage applicants** via `/applicants/*`
4. **Manage job postings** via `/jobs/*`
5. **Review applications** via `/applications/*`

## 📊 Expected Response Formats

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        // Response data here
    },
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 100,
        "total_pages": 10
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Response
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "full_name": "Admin User",
            "email": "admin@jobplacement.com",
            "role": "super_admin"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_at": "2024-06-11T10:30:00.000000Z"
    }
}
```

## 🔧 Troubleshooting

### Common Issues

1. **401 Unauthorized**
   - Pastikan token valid dan tidak expired
   - Login ulang untuk mendapatkan token baru

2. **404 Not Found**
   - Periksa URL endpoint
   - Pastikan server Laravel berjalan

3. **422 Validation Error**
   - Periksa format request body
   - Pastikan semua required fields terisi

4. **500 Internal Server Error**
   - Periksa log Laravel: `tail -f storage/logs/laravel.log`
   - Pastikan database connection OK

### Debug Tips

1. **Check Server Status**
   ```bash
   curl http://localhost:8000/api/v1/test/health
   ```

2. **Generate Test Data**
   ```bash
   # Via Postman: POST /test/generate-test-data
   ```

3. **Monitor Laravel Logs**
   ```bash
   cd new-backend
   tail -f storage/logs/laravel.log
   ```

## 📈 Performance Testing

### Load Testing dengan Postman
1. Gunakan Postman Runner untuk menjalankan collection
2. Set iterations dan delay antar request
3. Monitor response time dan error rate

### Endpoints untuk Load Testing
- `GET /applicants` - List data dengan pagination
- `GET /jobs/public` - Public job listings
- `POST /applications` - Create applications
- `GET /dashboard` - Dashboard data aggregation

## 🔄 API Versioning

Current API Version: **v1**
- Base URL: `http://localhost:8000/api/v1`
- All endpoints prefixed with `/api/v1`
- Backward compatibility maintained

## 📞 Support

Jika mengalami masalah:
1. Cek dokumentasi API di file ini
2. Periksa log server Laravel
3. Test dengan endpoint `/test/health` untuk basic connectivity
4. Pastikan environment variables sudah benar

## 🚀 Next Steps

Setelah testing berhasil:
1. **Frontend Integration** - Connect dengan React frontend
2. **Production Setup** - Deploy ke server production
3. **Monitoring** - Setup monitoring dan alerting
4. **Documentation** - Update API documentation

---

**Happy Testing! 🎉**

Postman collection ini mencakup semua fitur utama Job Placement System. Gunakan sebagai reference untuk testing dan development.
