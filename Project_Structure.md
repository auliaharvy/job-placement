# Struktur Proyek - Sistem Pengelolaan Penyaluran Kerja & Outsourcing

## 📋 Overview Arsitektur

Sistem ini dibangun dengan arsitektur microservices yang terdiri dari 3 komponen utama:

1. **Backend API** (Laravel 10) - Core business logic dan database management
2. **Frontend Web App** (React/Next.js) - Admin panel dan user interface  
3. **WhatsApp Gateway** (Node.js) - Service untuk integrasi WhatsApp messaging

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Frontend      │────▶│    Backend API  │────▶│   PostgreSQL    │
│   (React/Next)  │     │    (Laravel)    │     │   Database      │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │                        │                        
         │                        │                        
         │               ┌─────────────────┐               
         └──────────────▶│ WhatsApp Gateway│               
                         │    (Node.js)    │               
                         └─────────────────┘               
```

## 🗂️ Struktur Direktori Lengkap

```
job-placement-system/
├── 📁 backend/                    # Laravel 10 API Backend
│   ├── 📁 app/
│   │   ├── 📁 Console/           # Artisan commands
│   │   ├── 📁 Exceptions/        # Exception handlers
│   │   ├── 📁 Http/
│   │   │   ├── 📁 Controllers/   # API Controllers
│   │   │   │   ├── 📄 ApplicantController.php ✅
│   │   │   │   ├── 📄 JobPostingController.php ✅
│   │   │   │   ├── 📄 ApplicationController.php ✅
│   │   │   │   ├── 📄 AgentController.php (TODO)
│   │   │   │   ├── 📄 CompanyController.php (TODO)
│   │   │   │   ├── 📄 PlacementController.php (TODO)
│   │   │   │   ├── 📄 DashboardController.php (TODO)
│   │   │   │   └── 📄 AuthController.php (TODO)
│   │   │   ├── 📁 Middleware/    # Custom middleware
│   │   │   ├── 📁 Requests/      # Form request validators
│   │   │   └── 📁 Resources/     # API response transformers
│   │   ├── 📁 Models/            # Eloquent models
│   │   │   ├── 📄 User.php       ✅ Base user model
│   │   │   ├── 📄 Applicant.php  ✅ Pelamar kerja
│   │   │   ├── 📄 Agent.php      ✅ Agent referral
│   │   │   ├── 📄 Company.php    ✅ Perusahaan klien
│   │   │   ├── 📄 JobPosting.php ✅ Lowongan pekerjaan
│   │   │   ├── 📄 Application.php ✅ Lamaran pekerjaan
│   │   │   ├── 📄 Placement.php  ✅ Penempatan kerja
│   │   │   ├── 📄 Contract.php   (TODO) Kontrak kerja
│   │   │   ├── 📄 SelectionProcess.php (TODO) Proses seleksi
│   │   │   ├── 📄 WhatsAppLog.php (TODO) Log pesan WA
│   │   │   └── 📄 Notification.php (TODO) Notifikasi sistem
│   │   ├── 📁 Services/          # Business logic services
│   │   │   ├── 📄 WhatsAppService.php ✅ Integrasi WhatsApp
│   │   │   ├── 📄 JobMatchingService.php ✅ Algoritma matching
│   │   │   ├── 📄 QRCodeService.php (TODO) Generate QR code
│   │   │   ├── 📄 NotificationService.php (TODO) Notification
│   │   │   ├── 📄 ReportService.php (TODO) Generate reports
│   │   │   ├── 📄 AuthService.php (TODO) Authentication
│   │   │   └── 📄 FileUploadService.php (TODO) File handling
│   │   ├── 📁 Jobs/              # Queue jobs
│   │   ├── 📁 Events/            # Event classes
│   │   ├── 📁 Listeners/         # Event listeners
│   │   └── 📁 Observers/         # Model observers
│   ├── 📁 config/                # Configuration files
│   │   └── 📄 whatsapp.php       ✅ WhatsApp config
│   ├── 📁 database/
│   │   ├── 📁 migrations/        # Database migrations
│   │   └── 📁 seeders/           # Database seeders
│   ├── 📁 routes/                # API routes
│   ├── 📁 storage/               # Storage directories
│   └── 📁 tests/                 # Test files
│
├── 📁 frontend/                   # React/Next.js Frontend
│   ├── 📁 src/
│   │   ├── 📁 components/        # React components
│   │   │   ├── 📁 Layout/
│   │   │   │   └── 📄 AdminLayout.tsx ✅ Main admin layout
│   │   │   ├── 📁 Common/        # Reusable components
│   │   │   ├── 📁 Forms/         # Form components
│   │   │   ├── 📁 Charts/        # Chart components
│   │   │   └── 📁 Modals/        # Modal components
│   │   ├── 📁 pages/             # Next.js pages
│   │   │   ├── 📁 dashboard/
│   │   │   │   └── 📄 index.tsx  ✅ Main dashboard (placeholder)
│   │   │   ├── 📁 applicants/    # Applicant management
│   │   │   ├── 📁 jobs/          # Job management
│   │   │   ├── 📁 applications/  # Application management
│   │   │   ├── 📁 placements/    # Placement management
│   │   │   ├── 📁 agents/        # Agent management
│   │   │   ├── 📁 companies/     # Company management
│   │   │   ├── 📁 analytics/     # Analytics & reports
│   │   │   └── 📁 settings/      # System settings
│   │   ├── 📁 services/          # API services
│   │   ├── 📁 hooks/             # Custom React hooks
│   │   ├── 📁 utils/             # Utility functions
│   │   ├── 📁 types/             # TypeScript types
│   │   ├── 📁 contexts/          # React contexts
│   │   └── 📁 styles/            # CSS/SCSS files
│   └── 📄 package.json           ✅ Frontend dependencies
│
├── 📁 whatsapp-gateway/           # Node.js WhatsApp Gateway
│   ├── 📁 src/
│   │   ├── 📄 server.js          ✅ Main server file
│   │   ├── 📁 controllers/       # Route controllers
│   │   ├── 📁 services/          # Business logic
│   │   │   └── 📄 WhatsAppService.js ✅ Main WA service
│   │   ├── 📁 middleware/        # Express middleware
│   │   ├── 📁 utils/             # Utility functions
│   │   ├── 📁 routes/            # Express routes
│   │   ├── 📁 config/            # Configuration
│   │   └── 📁 templates/         # Message templates
│   ├── 📄 package.json           ✅ Gateway dependencies
│   └── 📄 .env.example           ✅ Environment variables
│
├── 📁 docs/                       # Dokumentasi teknis
├── 📁 deployment/                 # Deployment configurations
├── 📄 README.md                   ✅ Project overview
├── 📄 Project_Structure.md        ✅ Detailed project structure
└── 📄 .gitignore                  ✅ Git ignore rules
```

## 🏗️ Komponen yang Sudah Dibuat

### ✅ **Backend Components**

| Komponen | Status | Deskripsi |
|----------|--------|-----------|
| **Models** | ✅ Lengkap | 7 model utama dengan relationships dan business logic |
| **Controllers** | ✅ Kerangka | 3 controller utama dengan method placeholders |
| **Services** | ✅ Kerangka | WhatsApp dan JobMatching service dengan logic placeholder |
| **Config** | ✅ Lengkap | Konfigurasi WhatsApp integration |

#### Models Detail:
- **User.php** - Base authentication dengan role-based access (Super Admin, Direktur, HR Staff, Agent, Applicant)
- **Applicant.php** - Data pelamar lengkap (personal, education, experience, skills, documents)
- **Agent.php** - Sistem referral dengan point calculation dan leaderboard
- **Company.php** - Manajemen perusahaan klien dengan analytics dan performance metrics
- **JobPosting.php** - Lowongan dengan matching criteria (age, education, gender, experience, skills)
- **Application.php** - Proses seleksi 5 tahap (Apply → Psikotest → Interview → Medical → Penempatan)
- **Placement.php** - Penempatan kerja dengan contract management dan expiration alerts

### ✅ **Frontend Components**

| Komponen | Status | Deskripsi |
|----------|--------|-----------|
| **Layout** | ✅ Lengkap | AdminLayout dengan sidebar navigation role-based |
| **Dashboard** | ✅ Kerangka | Template dashboard dengan chart placeholders |
| **Package.json** | ✅ Lengkap | Dependencies lengkap (React, Next.js, Ant Design, Recharts) |

#### Layout Features:
- Responsive sidebar navigation dengan collapse
- Role-based menu items (Dashboard, Applicants, Jobs, Applications, Placements, Agents, Companies, Analytics, Settings)
- User profile dropdown dengan logout
- Notification system dengan badge
- Modern UI design dengan Ant Design components

### ✅ **WhatsApp Gateway Components**

| Komponen | Status | Deskripsi |
|----------|--------|-----------|
| **Server** | ✅ Kerangka | Express server dengan routing structure lengkap |
| **WhatsApp Service** | ✅ Kerangka | Baileys integration dengan multi-session support |
| **Configuration** | ✅ Lengkap | Environment variables dan package dependencies |

#### Gateway Features:
- Multi-session WhatsApp support menggunakan Baileys
- RESTful API endpoints untuk message sending
- Message queue processing untuk bulk messages
- Template message system dengan variables
- File upload handling untuk media messages
- Rate limiting dan error handling
- QR code generation untuk session pairing

## 🎯 Fitur Sistem yang Dirancang

### 📱 **1. Pendaftaran Pelamar via QR Code**
```
Flow: QR Code → Form Online → Auto Account → WhatsApp Welcome
```
**Components:**
- `QRCodeService` - Generate unique QR codes untuk pendaftaran
- `ApplicantController@register` - Handle registration dengan validasi
- `WhatsAppService@sendWelcomeMessage` - Auto welcome message
- Frontend QR display dan registration form

**Features:**
- QR code dengan unique URL untuk setiap agent
- Form pendaftaran online dengan validasi real-time
- Auto-create user account dengan default password (NIK)
- Upload dokumen (KTP, ijazah, sertifikat, foto)
- WhatsApp welcome message dengan credentials

### 💼 **2. Manajemen Lowongan Pekerjaan**
```
Flow: Create Job → Auto Matching → Broadcast WhatsApp → Track Applications
```
**Components:**
- `JobMatchingService` - Smart matching algorithm berdasarkan kriteria
- `WhatsAppService@broadcastJobOpening` - Mass notification ke matching applicants
- Job analytics tracking per lowongan
- Frontend job management dengan matching preview

**Features:**
- Job posting dengan kriteria detail (umur, pendidikan, gender, pengalaman, skills)
- Auto-matching algorithm dengan scoring system
- Broadcast WhatsApp ke pelamar yang sesuai kriteria
- Tracking aplikasi dan success rate per lowongan
- Job analytics dan performance metrics

### 🔄 **3. Proses Seleksi 5 Tahap**
```
Apply → Psikotest → Interview → Medical → Penempatan
```
**Components:**
- `Application` model dengan stage management
- `ApplicationController` dengan stage progression
- WhatsApp notifications setiap perpindahan stage
- Frontend selection pipeline dashboard

**Features:**
- 5 tahap seleksi dengan tracking lengkap
- Auto-progression dengan approval system
- WhatsApp notifications setiap stage update
- Scheduling system untuk test dan interview
- Score tracking dan evaluation notes
- Rejection handling dengan reasons

### 👥 **4. Sistem Agent & Referral**
```
Flow: Agent Referral → Track Performance → Calculate Commission → Leaderboard
```
**Components:**
- `Agent` model dengan performance tracking
- Point system dan commission calculation
- Leaderboard dan ranking system
- Agent dashboard dan analytics

**Features:**
- Agent registration dengan unique referral code
- Performance tracking (total referrals, successful placements, success rate)
- Point system dengan level progression (Bronze, Silver, Gold, Platinum)
- Commission calculation berdasarkan successful placements
- Real-time leaderboard dan ranking
- Agent personal dashboard

### 📋 **5. Penempatan & Kontrak Management**
```
Flow: Acceptance → Contract Creation → Placement Tracking → Expiration Alerts
```
**Components:**
- `Placement` model dengan contract management
- Contract expiration monitoring dengan alerts
- WhatsApp reminders untuk contract renewal
- Performance tracking per placement

**Features:**
- Contract management dengan start/end dates
- Auto-alerts untuk contract expiration (H-30, H-14, H-7)
- Placement performance tracking
- Employee retention analytics
- Contract renewal workflow
- Termination handling dengan reasons

### 📊 **6. Analytics & Reporting Dashboard**
```
Executive Dashboard → Real-time Analytics → Export Reports → Data Visualization
```
**Components:**
- `DashboardController` dengan executive metrics
- Report generation service dengan PDF export
- Real-time data visualization dengan charts
- Customizable filters dan date ranges

**Features:**
- Executive dashboard dengan key metrics
- Real-time analytics dan trend analysis
- Customizable reports dengan PDF export
- Data visualization (charts, graphs, heatmaps)
- Performance comparisons dan benchmarking
- Predictive analytics untuk forecasting

### 📱 **7. WhatsApp Integration Lengkap**
```
Multi-Session → Template Messages → Bulk Broadcasting → Delivery Tracking
```
**Components:**
- Multi-session WhatsApp gateway dengan Baileys
- Template message system dengan variables
- Bulk messaging dengan queue processing
- Delivery status tracking dan analytics

**Features:**
- Multi-device WhatsApp support dengan session management
- Template messages untuk berbagai scenarios
- Bulk broadcasting dengan rate limiting
- Media file support (images, documents, audio)
- Delivery status tracking dan analytics
- Auto-reconnection dan session recovery
- Webhook support untuk incoming messages

## 🔧 Tech Stack Implementation

### **Backend (Laravel 10)**
```php
Framework: Laravel 10
Database: PostgreSQL 14+
Cache: Redis
Queue: Laravel Queue with Redis
Authentication: JWT Tokens
File Storage: AWS S3 / Google Cloud Storage
```

**Key Packages:**
- `laravel/sanctum` - API authentication
- `spatie/laravel-permission` - Role-based permissions
- `intervention/image` - Image processing
- `maatwebsite/excel` - Excel import/export
- `barryvdh/laravel-dompdf` - PDF generation

### **Frontend (React/Next.js)**
```javascript
Framework: Next.js 14
UI Library: Ant Design 5.12+
State Management: React Query
Charts: Recharts
Forms: React Hook Form
Styling: Tailwind CSS + Ant Design
```

**Key Dependencies:**
- `antd` - UI components
- `@ant-design/icons` - Icon library
- `recharts` - Data visualization
- `axios` - HTTP client
- `react-query` - Server state management
- `react-hook-form` - Form handling
- `dayjs` - Date manipulation
- `qrcode` - QR code generation

### **WhatsApp Gateway (Node.js)**
```javascript
Runtime: Node.js 18+
Framework: Express.js
WhatsApp: @whiskeysockets/baileys
Queue: Bull Queue with Redis
Storage: File system + Cloud storage
```

**Key Dependencies:**
- `@whiskeysockets/baileys` - WhatsApp Web API
- `express` - Web framework
- `bull` - Job queue processing
- `redis` - Caching and queue storage
- `multer` - File upload handling
- `sharp` - Image processing
- `winston` - Logging

## 🚀 Development Roadmap

### **Phase 1: Core Infrastructure (Week 1-2)**
- [ ] Setup database migrations dan seeders
- [ ] Implement JWT authentication system
- [ ] Complete API routes dan middleware
- [ ] Setup basic frontend routing
- [ ] WhatsApp Gateway connection testing

### **Phase 2: Core Features (Week 3-5)**
- [ ] Complete applicant registration flow
- [ ] Implement job posting dan matching
- [ ] Build selection process workflow
- [ ] Frontend forms dan data tables
- [ ] WhatsApp message integration

### **Phase 3: Advanced Features (Week 6-7)**
- [ ] Agent system dan referral tracking
- [ ] Analytics dashboard dan reporting
- [ ] File upload dan document management
- [ ] Advanced filtering dan search
- [ ] Bulk operations dan imports

### **Phase 4: Optimization & Testing (Week 8)**
- [ ] Performance optimization
- [ ] Comprehensive testing (Unit + Integration)
- [ ] Security audit dan hardening
- [ ] Documentation completion
- [ ] Deployment preparation

## 📝 Next Development Steps

### **Immediate Priorities:**

1. **Database Setup**
   ```bash
   # Create migrations for all models
   php artisan make:migration create_users_table
   php artisan make:migration create_applicants_table
   # ... etc for all models
   ```

2. **Authentication System**
   ```php
   // Implement JWT auth middleware
   // Role-based access control
   // API authentication for WhatsApp Gateway
   ```

3. **API Routes**
   ```php
   // Complete all CRUD endpoints
   // Authentication routes
   // File upload endpoints
   // WhatsApp integration endpoints
   ```

4. **Frontend Development**
   ```javascript
   // Complete form components
   // Data table components
   // Chart components
   // API service layer
   ```

5. **WhatsApp Gateway**
   ```javascript
   // Complete controller implementations
   // Message queue processing
   // Session management
   // Error handling dan logging
   ```

## 🎯 Success Metrics

### **Technical Metrics:**
- [ ] **API Response Time** < 200ms average
- [ ] **System Uptime** > 99.9%
- [ ] **WhatsApp Delivery Rate** > 95%
- [ ] **Database Query Performance** optimized
- [ ] **Frontend Load Time** < 3 seconds

### **Business Metrics:**
- [ ] **Registration Efficiency** < 5 minutes per applicant
- [ ] **Matching Accuracy** > 80% relevant matches
- [ ] **Process Automation** 80% reduction in manual work
- [ ] **User Satisfaction** NPS > 50
- [ ] **System Adoption** 90% user adoption rate

Kerangka proyek ini siap untuk development penuh dengan struktur yang solid dan roadmap yang jelas. Semua komponen utama sudah dirancang dan siap untuk implementasi detail.
