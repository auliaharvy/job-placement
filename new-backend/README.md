# 🎯 Job Placement System - Backend API

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue.svg)](https://postgresql.org)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-Integrated-green.svg)](http://brevet.online:8005)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-green.svg)]()

Sistem manajemen penempatan kerja dengan integrasi WhatsApp yang mencakup job matching cerdas, manajemen pelamar, dan otomasi komunikasi.

## ✨ Features

### 🎯 Core Features
- **Smart Job Matching** - AI-powered matching dengan weighted scoring algorithm
- **Multi-Role System** - Admin, Company, Agent, Applicant dengan akses berbeda
- **WhatsApp Integration** - Otomasi notifikasi dan komunikasi business workflow
- **Document Management** - Upload dan manajemen CV, ijazah, sertifikat
- **Application Tracking** - Tracking lengkap proses seleksi dari aplikasi hingga placement

### 🤖 AI Job Matching
- **Weighted Scoring**: Experience (30%), Education (25%), Skills (25%), Age (15%), Gender (5%)
- **Multi-Criteria Filtering**: Age, education, experience, skills, location, gender preferences
- **Bidirectional Matching**: Find jobs for applicants AND applicants for jobs
- **Tolerance-Based**: Flexible matching dengan configurable tolerances
- **Location-Based**: Geographic matching dengan radius search

### 📱 WhatsApp Business Automation
- **Welcome Messages** - Greeting untuk registrasi baru
- **Job Broadcasting** - Targeted job notifications ke candidates yang sesuai
- **Application Updates** - Konfirmasi aplikasi dan update status
- **Interview Scheduling** - Automated scheduling reminders
- **Final Decisions** - Acceptance/rejection notifications
- **Contract Management** - Contract expiration alerts
- **Rate Limiting** - Smart rate limiting untuk prevent spam

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 atau lebih tinggi
- Composer
- PostgreSQL 15+
- Node.js 18+ (optional)

### Setup Instructions

1. **Clone & Navigate**
   ```bash
   cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"
   ```

2. **Quick Setup (Automated)**
   ```bash
   chmod +x *.sh
   ./setup.sh
   ```

3. **Start Development Server**
   ```bash
   ./start-server.sh
   ```

4. **Run Tests**
   ```bash
   # In another terminal
   ./test.sh
   
   # Test with WhatsApp (optional)
   ./test.sh 628123456789
   ```

### Manual Setup

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage setup
php artisan storage:link

# Start server
php artisan serve
```

## 📋 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Testing Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/test/health` | GET | System health check |
| `/test/job-matching` | GET | Test job matching service |
| `/test/whatsapp` | GET | Test WhatsApp integration |
| `/test/models` | GET | Test database models |
| `/test/workflow` | GET | End-to-end workflow test |

### Core API Endpoints

#### Authentication
- `POST /auth/register` - User registration
- `POST /auth/login` - User login
- `POST /auth/logout` - User logout
- `GET /auth/me` - Get current user

#### Applicants
- `GET /applicants` - List applicants (with filtering)
- `POST /applicants` - Create applicant profile
- `GET /applicants/{id}` - Get applicant details
- `PUT /applicants/{id}` - Update applicant
- `DELETE /applicants/{id}` - Delete applicant

#### Job Postings
- `GET /job-postings` - List job postings
- `POST /job-postings` - Create job posting
- `GET /job-postings/{id}` - Get job details
- `PUT /job-postings/{id}` - Update job posting
- `DELETE /job-postings/{id}` - Delete job posting

#### Job Matching
- `GET /job-postings/{id}/matching-applicants` - Get matching applicants for job
- `GET /applicants/{id}/matching-jobs` - Get matching jobs for applicant
- `GET /analytics/matching-trends` - Get matching analytics

#### Applications
- `POST /applications` - Submit job application
- `GET /applications` - List applications
- `PUT /applications/{id}/stage` - Update application stage
- `PUT /applications/{id}/status` - Update application status

#### WhatsApp
- `POST /whatsapp/send-message` - Send WhatsApp message
- `GET /whatsapp/status` - Check gateway status
- `POST /whatsapp/broadcast-job` - Broadcast job to matching candidates

## 🗄️ Database Schema

### Core Tables

#### Users
- Basic user authentication and profile
- Polymorphic relations to Applicant/Company/Agent

#### Applicants
- Complete applicant profiles with skills, experience, preferences
- Document management (CV, certificates, etc.)
- Location and availability information

#### Job Postings  
- Job requirements and preferences
- Skill requirements, education levels, experience
- Location and salary information

#### Applications
- Application tracking with stages (screening, interview, etc.)
- Status management and notes

#### Companies
- Company profiles and information
- Contact details and verification status

#### Agents
- Referral agent management
- Commission tracking and performance metrics

#### WhatsApp Logs
- Complete audit trail of WhatsApp communications
- Message status tracking and analytics

## 🔧 Configuration

### Environment Variables

```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=job_placement_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# WhatsApp Gateway
WHATSAPP_GATEWAY_URL=http://brevet.online:8005
WHATSAPP_DEFAULT_SESSION=job-placement
WHATSAPP_API_KEY=
WHATSAPP_TIMEOUT=30
WHATSAPP_RETRY_ATTEMPTS=3
WHATSAPP_RATE_LIMIT_ENABLED=true
WHATSAPP_MAX_PER_MINUTE=30
WHATSAPP_MAX_PER_HOUR=500
WHATSAPP_MAX_PER_DAY=5000

# File Upload
MAX_UPLOAD_SIZE=10240
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx

# Frontend
FRONTEND_URL=http://localhost:3000
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:3001
```

## 🧪 Testing

### Automated Testing

```bash
# Run all tests
./test.sh

# Test specific endpoints
curl http://localhost:8000/api/v1/test/health
curl http://localhost:8000/api/v1/test/job-matching
curl http://localhost:8000/api/v1/test/whatsapp

# Test WhatsApp message sending
curl -X POST http://localhost:8000/api/v1/test/whatsapp/send-test-message \
  -H "Content-Type: application/json" \
  -d '{"phone":"628123456789","message":"Test message"}'
```

### Manual Testing

1. **Health Check**
   ```bash
   curl http://localhost:8000/api/v1/test/health
   ```
   Expected: Status 200 with system health information

2. **Job Matching Algorithm**
   ```bash
   curl http://localhost:8000/api/v1/test/job-matching
   ```
   Expected: Demonstration of matching algorithm with sample data

3. **WhatsApp Integration**
   ```bash
   curl http://localhost:8000/api/v1/test/whatsapp/status
   ```
   Expected: WhatsApp gateway connection status

## 🏗️ Architecture

### Service Layer

#### JobMatchingService
- `findMatchingApplicants()` - Smart applicant discovery
- `calculateMatchingScore()` - Weighted scoring algorithm
- `getRecommendedApplicants()` - Top candidates with scores
- `findMatchingJobsForApplicant()` - Reverse job matching
- `getMatchingTrends()` - Analytics and insights

#### WhatsAppService
- `sendWelcomeMessage()` - New user onboarding
- `broadcastJobOpening()` - Targeted job notifications
- `sendApplicationConfirmation()` - Application confirmations
- `sendStageUpdateNotification()` - Process updates
- `sendScheduleReminder()` - Interview/test reminders
- `sendAcceptanceNotification()` - Success notifications
- `sendRejectionNotification()` - Rejection with encouragement

### Data Models

```
User
├── Applicant (profile, skills, experience)
├── Company (company info, job postings)
└── Agent (referral tracking)

JobPosting
├── Applications (tracking, stages)
└── Placements (successful hires)

WhatsAppLog (message audit trail)
```

## 📊 Job Matching Algorithm

### Scoring Weights
- **Experience**: 30% - Work experience matching
- **Education**: 25% - Education level compatibility  
- **Skills**: 25% - Technical and soft skills matching
- **Age**: 15% - Age range preferences
- **Gender**: 5% - Gender preferences (if any)

### Matching Criteria
1. **Exact Match**: Perfect alignment with job requirements
2. **Tolerance Match**: Close match within acceptable ranges
3. **Partial Match**: Some criteria met, scored proportionally
4. **Weighted Scoring**: Combined score based on importance weights

### Example Scoring
```json
{
  "applicant_id": 123,
  "job_id": 456,
  "total_score": 87.5,
  "breakdown": {
    "experience": {"score": 0.9, "weight": 0.3},
    "education": {"score": 1.0, "weight": 0.25},
    "skills": {"score": 0.8, "weight": 0.25},
    "age": {"score": 1.0, "weight": 0.15},
    "gender": {"score": 1.0, "weight": 0.05}
  }
}
```

## 🔐 Security Features

### Authentication
- **Laravel Sanctum** - API token-based authentication
- **Role-Based Access Control** - Multi-level permissions
- **Password Hashing** - Secure password storage
- **Rate Limiting** - API abuse prevention

### Data Protection
- **Input Validation** - Request validation on all endpoints
- **SQL Injection Prevention** - Eloquent ORM protection
- **XSS Protection** - Output encoding
- **CSRF Protection** - Cross-site request forgery prevention

### File Upload Security
- **File Type Validation** - Allowed extensions only
- **File Size Limits** - Configurable upload limits
- **Virus Scanning** - Integration ready for antivirus
- **Secure Storage** - Files stored outside web root

## 📱 WhatsApp Integration Details

### Gateway Configuration
- **Endpoint**: `http://brevet.online:8005`
- **Session**: `job-placement`
- **Supported Types**: Text, Image, Document messages
- **Rate Limiting**: Configurable per minute/hour/day limits

### Message Templates

#### Welcome Message
```
🎉 Selamat datang di Job Placement System!

Profil Anda telah berhasil didaftarkan.
Tim kami akan segera menghubungi Anda jika ada lowongan yang sesuai.

Terima kasih! 🙏
```

#### Job Notification
```
💼 Lowongan Kerja Baru!

🏢 {company_name}
📍 {location}
💰 {salary_range}

{job_description}

Minat? Balas pesan ini untuk apply!
```

#### Interview Reminder
```
📅 Pengingat Interview

Halo {applicant_name}!

Anda memiliki interview untuk posisi {position} di {company_name}

🕐 Waktu: {datetime}
📍 Lokasi: {location}

Selamat dan sukses! 💪
```

## 🚀 Deployment

### Production Setup

1. **Server Requirements**
   - PHP 8.1+ with extensions (pdo_pgsql, bcmath, ctype, fileinfo, json, mbstring, openssl, tokenizer, xml)
   - PostgreSQL 15+
   - Nginx/Apache
   - Redis (optional, for caching)

2. **Environment Configuration**
   ```bash
   cp .env.example .env.production
   # Update production values
   ```

3. **Optimization**
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Database Migration**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=ProductionSeeder
   ```

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Error
```bash
# Check PostgreSQL status
sudo systemctl status postgresql

# Check connection
psql -h localhost -U username -d job_placement_db
```

#### WhatsApp Gateway Not Responding
```bash
# Check gateway status
curl http://brevet.online:8005/api/status

# Test session
curl http://localhost:8000/api/v1/test/whatsapp/status
```

#### File Upload Issues
```bash
# Check storage permissions
ls -la storage/
chmod -R 775 storage/
chown -R www-data:www-data storage/
```

### Debug Mode
```bash
# Enable debug mode
APP_DEBUG=true
LOG_LEVEL=debug

# View logs
tail -f storage/logs/laravel.log
```

## 📞 Support

### Getting Help
- **Documentation**: Check `/docs` folder
- **Testing Guide**: `TESTING_FIXES.md`
- **API Documentation**: `BACKEND_VERIFICATION.md`
- **Setup Issues**: Run `./test.sh` for diagnostics

### Error Reporting
When reporting issues, include:
- PHP version
- Laravel version
- Error logs from `storage/logs/laravel.log`
- Steps to reproduce

## 📋 Changelog

### v1.0.0 (Current)
- ✅ Complete backend API implementation
- ✅ WhatsApp integration with business workflows
- ✅ Smart job matching algorithm
- ✅ Role-based authentication system
- ✅ Comprehensive testing infrastructure
- ✅ Database schema optimization
- ✅ Error handling and logging

### Upcoming Features
- 🔄 Real-time notifications
- 🔄 Advanced analytics dashboard
- 🔄 Mobile app API enhancements
- 🔄 AI-powered resume parsing
- 🔄 Video interview integration

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel Framework for the robust foundation
- WhatsApp Business API for communication integration
- PostgreSQL for reliable data storage
- All contributors and developers involved

---

**Built with ❤️ for efficient job placement management**

For more information, visit our [documentation](docs/) or run `./test.sh` to see the system in action!