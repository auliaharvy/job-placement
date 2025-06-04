# Job Placement System - Backend API

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- PostgreSQL 14+
- Redis (optional, for caching)
- Node.js 18+ (for WhatsApp Gateway)

### 1. Backend Setup (Laravel)

```bash
# Clone project
cd /Users/auliaharvy/AI Development/job-placement-system/backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env

# Configure your .env file
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=job_placement_db
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Generate application key
php artisan key:generate

# Create database (make sure PostgreSQL is running)
createdb job_placement_db

# Run migrations and seeders
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve
```

### 2. WhatsApp Gateway Setup

```bash
# Clone WA Gateway
git clone https://github.com/auliaharvy/wa_gateway.git whatsapp-gateway
cd whatsapp-gateway

# Install dependencies
npm install

# Setup environment
cp .env.example .env

# Configure .env
PORT=3001
WEBHOOK_URL=http://localhost:8000/api/v1/webhooks/whatsapp

# Start gateway
npm start
```

## 📡 API Endpoints

### Base URL: `http://localhost:8000/api/v1`

### Authentication Endpoints

```http
POST /auth/login
POST /auth/register/applicant
GET  /auth/profile
PUT  /auth/profile
POST /auth/change-password
POST /auth/logout
GET  /auth/check
```

### Applicant Endpoints

```http
GET    /applicants
POST   /applicants
GET    /applicants/{id}
PUT    /applicants/{id}
DELETE /applicants/{id}
POST   /applicants/{id}/upload-document
GET    /applicants/statistics
```

### Job Posting Endpoints

```http
GET    /jobs
POST   /jobs
GET    /jobs/{id}
PUT    /jobs/{id}
DELETE /jobs/{id}
POST   /jobs/{id}/publish
POST   /jobs/{id}/close
GET    /jobs/{id}/matching-applicants
POST   /jobs/{id}/broadcast-whatsapp
GET    /jobs/statistics
```

### Application Endpoints

```http
GET    /applications
POST   /applications
GET    /applications/{id}
PUT    /applications/{id}
DELETE /applications/{id}
POST   /applications/{id}/progress
POST   /applications/{id}/reject
POST   /applications/{id}/accept
POST   /applications/{id}/schedule-interview
POST   /applications/{id}/schedule-psikotes
POST   /applications/{id}/schedule-medical
GET    /applications/statistics
```

### Placement Endpoints

```http
GET    /placements
POST   /placements
GET    /placements/{id}
PUT    /placements/{id}
DELETE /placements/{id}
POST   /placements/{id}/terminate
POST   /placements/{id}/complete
POST   /placements/{id}/add-review
GET    /placements/expiring
GET    /placements/statistics
```

### Dashboard Endpoint

```http
GET /dashboard
```

## 🧪 Testing with Postman

### 1. Authentication Flow

**Login:**
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "admin@jobplacement.com",
    "password": "admin123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "first_name": "Super",
            "last_name": "Admin",
            "email": "admin@jobplacement.com",
            "role": "super_admin"
        },
        "token": "your_jwt_token_here",
        "token_type": "Bearer"
    }
}
```

### 2. Protected Endpoints

For all protected endpoints, add header:
```http
Authorization: Bearer your_jwt_token_here
```

### 3. Sample Test Cases

**Create Job Posting:**
```http
POST /api/v1/jobs
Authorization: Bearer your_token
Content-Type: application/json

{
    "company_id": 1,
    "title": "Full Stack Developer",
    "position": "Full Stack Developer", 
    "employment_type": "pkwt",
    "description": "We are looking for a skilled Full Stack Developer...",
    "work_location": "Jakarta Office",
    "work_city": "Jakarta",
    "work_province": "DKI Jakarta",
    "work_arrangement": "hybrid",
    "salary_min": 8000000,
    "salary_max": 15000000,
    "application_deadline": "2024-07-04",
    "required_education_levels": ["s1"],
    "min_experience_months": 24,
    "required_skills": ["PHP", "Laravel", "JavaScript", "React"],
    "total_positions": 2,
    "priority": "high",
    "publish_immediately": true
}
```

**Register New Applicant:**
```http
POST /api/v1/auth/register/applicant
Content-Type: application/json

{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "081234567890",
    "nik": "3201234567890123",
    "birth_date": "1995-03-15",
    "birth_place": "Jakarta",
    "gender": "male",
    "address": "Jl. Merdeka No. 123",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "whatsapp_number": "081234567890",
    "education_level": "s1",
    "school_name": "Universitas Indonesia",
    "graduation_year": 2018,
    "referral_code": "RINI001"
}
```

**Submit Application:**
```http
POST /api/v1/applications
Authorization: Bearer your_token
Content-Type: application/json

{
    "applicant_id": 1,
    "job_posting_id": 1,
    "source": "direct",
    "applicant_notes": "I am very interested in this position..."
}
```

**Get Dashboard Data:**
```http
GET /api/v1/dashboard
Authorization: Bearer your_token
```

## 🔧 Default Login Credentials

```
Super Admin:
Email: admin@jobplacement.com
Password: admin123

Direktur:
Email: direktur@jobplacement.com  
Password: direktur123

HR Staff:
Email: hr1@jobplacement.com
Password: hr123

Agent:
Email: agent1@jobplacement.com
Password: agent123

Applicant:
Email: john.doe@gmail.com
Password: 3201234567891234 (NIK)
```

## 📊 Sample Data

The seeders will create:
- 8 users with different roles
- 5 companies 
- 2 agents with performance data
- 2 sample applicants
- 5 job postings (published and ready for applications)

## 🚨 Common Issues

1. **Database Connection**: Make sure PostgreSQL is running
2. **Permission Denied**: Ensure storage directory is writable
3. **WhatsApp Gateway**: Make sure it's running on port 3001
4. **CORS Issues**: Frontend should run on port 3000

## 📱 WhatsApp Integration

The system will automatically:
- Send welcome messages to new applicants
- Broadcast job opportunities to matching applicants  
- Send selection stage updates
- Send placement confirmations
- Send contract expiry alerts

## 🎯 Next Steps

1. Test all API endpoints with Postman
2. Verify database data with seeders
3. Test WhatsApp integration
4. Setup frontend for visual interface

## 📞 Support

If you encounter any issues, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Database connection
3. Environment variables
4. File permissions