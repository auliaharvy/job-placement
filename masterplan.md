# Master Plan - Sistem Pengelolaan Penyaluran Kerja & Outsourcing

## 1. Ringkasan Proyek

### 1.1 Visi
Membangun sistem digital terintegrasi yang mengotomatisasi dan mempermudah pengelolaan penyaluran tenaga kerja, menggantikan proses manual yang memakan waktu dengan solusi yang efisien, transparan, dan skalabel.

### 1.2 Misi
- Menyediakan platform pendaftaran pelamar yang mudah melalui QR code
- Mengotomatisasi proses seleksi dan penempatan tenaga kerja
- Membangun database talent pool yang kuat untuk menarik klien perusahaan
- Meningkatkan efisiensi operasional dari proses manual ke digital
- Menyediakan sistem tracking komprehensif untuk semua stakeholder

### 1.3 Tujuan Utama
- **Efisiensi**: Mengurangi waktu input data dari WhatsApp ke Excel menjadi otomatis
- **Skalabilitas**: Menangani 300+ pelamar dan 300+ penempatan per bulan
- **Transparansi**: Tracking real-time untuk agent, staff, dan manajemen
- **Profesionalisme**: Dashboard presentable untuk menarik klien perusahaan

## 2. Target Pengguna

### 2.1 Pengguna Internal
- **Super Admin**: Kontrol penuh sistem
- **Direktur**: Akses dashboard eksekutif dan laporan
- **Staff HR**: Kelola pelamar, lowongan, dan proses seleksi
- **Agent**: Monitor referral dan komisi

### 2.2 Pengguna Eksternal
- **Pelamar Kerja**: Daftar, update profil, apply lowongan
- **Perusahaan Klien**: Akses dashboard talent pool (fase 2)

## 3. Fitur Utama & Modul

### 3.1 Modul Pendaftaran Pelamar
**Fitur Utama:**
- QR Code untuk akses form pendaftaran
- Form pendaftaran online dengan validasi otomatis
- Auto-create user account setelah registrasi
- Upload dokumen (KTP, ijazah, sertifikat)

**Data yang Dikumpulkan:**
- Data Pribadi: Nama, TTL, NIK, TB/BB, No HP, Email, Alamat lengkap
- Pendidikan: Tahun lulus, jurusan, nama sekolah/universitas
- Pengalaman Kerja: Perusahaan, posisi, bidang, durasi
- Keterampilan: Hard skills & soft skills
- Referensi: Kode agent/sumber informasi
- Status: Aktif/Non-aktif, Bekerja/Tersedia

### 3.2 Modul Manajemen Lowongan
**Fitur Utama:**
- CRUD lowongan pekerjaan
- Set kriteria/requirement (pendidikan, usia, gender, pengalaman, dll)
- Auto-matching dengan database pelamar
- Broadcast WhatsApp otomatis ke pelamar yang sesuai
- Tracking jumlah pelamar per lowongan

**Jenis Pekerjaan:**
- Magang (3-6 bulan)
- PKWT (12 bulan)
- Project-based

### 3.3 Modul Proses Seleksi
**Tahapan:**
1. **Apply** → Pelamar mendaftar ke lowongan
2. **Psikotest** → Jadwal & hasil test
3. **Interview** → Jadwal & evaluasi
4. **Medical Checkup** → Status kesehatan
5. **Penempatan** → Diterima/Ditolak

**Fitur Tracking:**
- Status real-time setiap tahap
- Notifikasi ke pelamar & staff
- History seleksi per pelamar

### 3.4 Modul Penempatan & Kontrak
**Fitur:**
- Data penempatan (perusahaan, posisi, lokasi)
- Manajemen kontrak (tanggal mulai, berakhir, jenis)
- Alert kontrak akan berakhir (H-30, H-14, H-7)
- History penempatan per pelamar
- Statistik penempatan per perusahaan

### 3.5 Modul Agent & Referral
**Fitur:**
- Dashboard agent personal
- Tracking pelamar dari referral
- Statistik (total referral, yang bekerja, yang lolos)
- Sistem poin/reward
- Leaderboard agent

### 3.6 Modul Analytics & Reporting
**Dashboard Eksekutif:**
- Total pelamar (aktif/non-aktif)
- Jumlah penempatan bulanan
- Success rate per lowongan
- Performa agent
- Trend industri & posisi

**Report Generator:**
- Export PDF untuk presentasi
- Filter by: periode, industri, pendidikan, lokasi
- Visualisasi data (chart, graph)
- Template report customizable

### 3.7 Modul Integrasi WhatsApp (Self-Hosted)
**Fitur:**
- Multi-session WhatsApp Gateway (wa_gateway - Baileys)
- Broadcast lowongan baru ke pelamar yang sesuai kriteria
- Notifikasi real-time status seleksi
- Reminder otomatis jadwal test/interview
- Template pesan customizable
- Log & monitoring pengiriman pesan
- Support pengiriman text, image, dan document

**Implementasi:**
- Menggunakan wa_gateway (NodeJS v18+)
- Multi device & multi session support
- QR code scanning untuk setup awal
- REST API integration dengan sistem utama

## 4. Arsitektur Teknis

### 4.1 Tech Stack Rekomendasi

**Backend:**
- **Framework**: Laravel 10 (PHP) atau Node.js (Express)
- **Database**: PostgreSQL (untuk performa query kompleks)
- **Cache**: Redis (untuk real-time dashboard)
- **Queue**: Laravel Queue/Bull Queue (untuk broadcast WA)

**Frontend:**
- **Admin Panel**: React.js + Ant Design/Material UI
- **Landing Page**: Next.js (untuk SEO)
- **Mobile-friendly**: Progressive Web App (PWA)

**Integrasi:**
- **WhatsApp**: wa_gateway (Baileys) - Self-hosted solution
  - NodeJS v18+ untuk WhatsApp Gateway
  - Multi-session support
  - REST API endpoints
- **Storage**: AWS S3/Google Cloud Storage (untuk dokumen)
- **Email**: SendGrid/Mailgun
- **SMS**: Twilio (backup notifikasi)

### 4.2 Arsitektur Sistem
```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Frontend      │────▶│    API Gateway  │────▶│   Backend       │
│   (React/Next)  │     │    (Nginx)      │     │   (Laravel)     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                                                          │
                              ┌───────────────────────────┼───────────────┐
                              │                           │               │
                        ┌─────▼─────┐          ┌─────────▼──┐    ┌───────▼────┐
                        │PostgreSQL │          │   Redis    │    │  Storage   │
                        │           │          │   Cache    │    │  (S3/GCS)  │
                        └───────────┘          └────────────┘    └────────────┘
                                                          │
                                                  ┌───────▼────────┐
                                                  │ WA Gateway      │
                                                  │ (NodeJS/Baileys)│
                                                  └────────────────┘
```

## 5. Model Data Konseptual

### 5.1 Entitas Utama
1. **Users** (id, email, password, role, status)
2. **Applicants** (id, user_id, personal_data, education, status)
3. **Agents** (id, user_id, code, total_referrals)
4. **Companies** (id, name, industry, address)
5. **Job_Postings** (id, company_id, title, requirements)
6. **Applications** (id, applicant_id, job_id, status)
7. **Selection_Process** (id, application_id, stage, result)
8. **Placements** (id, applicant_id, company_id, contract_data)
9. **Contracts** (id, placement_id, start_date, end_date, type)

### 5.2 Relasi Penting
- Applicants → Agent (many-to-one) via referral_code
- Applicants → Applications (one-to-many)
- Applications → Selection_Process (one-to-many)
- Placements → Contracts (one-to-one)

## 6. Prinsip Desain UI/UX

### 6.1 Design System
- **Clean & Professional**: Inspirasi dari LinkedIn/Indeed
- **Mobile-First**: 70% user akan akses via smartphone
- **Accessibility**: WCAG 2.1 compliance
- **Brand Colors**: Biru (trust) + Hijau (growth)

### 6.2 Key Interfaces
1. **Landing Page**: Form registrasi dengan QR code prominent
2. **Dashboard Pelamar**: Simple, fokus pada status & lowongan
3. **Admin Dashboard**: Data-rich dengan filter advance
4. **Presentation Mode**: Clean visualisasi untuk client meeting

## 7. Keamanan & Compliance

### 7.1 Security Measures
- **Authentication**: JWT tokens + refresh token
- **Authorization**: Role-based access control (RBAC)
- **Data Protection**: Enkripsi data sensitif (NIK, dokumen)
- **API Security**: Rate limiting, API keys
- **Audit Trail**: Log semua aktivitas penting

### 7.2 Compliance
- **UU Ketenagakerjaan**: Sesuai regulasi Kemnaker
- **GDPR/UU PDP**: Consent management, data deletion
- **Document Retention**: Kebijakan penyimpanan dokumen

## 8. Fase Pengembangan

### Phase 1: MVP (2-3 bulan)
**Sprint 1-2**: Setup & Core Features
- Setup infrastructure
- User management & authentication
- Applicant registration via QR
- Basic dashboard

**Sprint 3-4**: Job Management
- Job posting CRUD
- Application system
- Basic matching algorithm

**Sprint 5-6**: Selection Process
- Selection workflow
- Status tracking
- Basic reporting

### Phase 2: Enhancement (2 bulan)
- WhatsApp integration
- Advanced analytics
- Agent portal
- Contract management

### Phase 3: Scale (1-2 bulan)
- Performance optimization
- Advanced matching AI
- Client portal
- Mobile app (optional)

## 9. Estimasi & Resources

### 9.1 Tim Development
- 1 Project Manager
- 2 Backend Developers
- 2 Frontend Developers
- 1 UI/UX Designer
- 1 QA Engineer
- 1 DevOps Engineer

### 9.2 Timeline
- **Total Duration**: 5-7 bulan
- **MVP Release**: 3 bulan
- **Full Release**: 6 bulan

### 9.3 Budget Estimate
- **Development**: Rp 300-500 juta
- **Infrastructure**: Rp 5-10 juta/bulan
- **Maintenance**: Rp 20-30 juta/bulan

## 10. Risiko & Mitigasi

### 10.1 Technical Risks
| Risiko | Impact | Mitigasi |
|--------|--------|----------|
| WhatsApp banned/blocked | High | Multiple WA sessions, rotate numbers |
| Data migration dari Excel | Medium | Phased migration, data validation |
| Server overload | Medium | Auto-scaling, caching strategy |
| WA Gateway downtime | Medium | Queue system, retry mechanism |

### 10.2 Business Risks
| Risiko | Impact | Mitigasi |
|--------|--------|----------|
| User adoption | High | Training program, incentive agent |
| Data accuracy | High | Validation rules, regular audit |
| Competition | Medium | Unique features, faster time-to-market |

## 11. Success Metrics (KPI)

### 11.1 Operational KPIs
- **Registration Rate**: 300+ pelamar/bulan
- **Placement Rate**: 300+ penempatan/bulan
- **Process Time**: <5 menit per registrasi
- **System Uptime**: 99.9%

### 11.2 Business KPIs
- **Client Acquisition**: 5+ perusahaan baru/bulan
- **Agent Performance**: 10+ referral/agent/bulan
- **Data Completeness**: 95%+ profil lengkap
- **User Satisfaction**: NPS >50

## 12. Future Expansion

### 12.1 Potential Features
1. **AI Matching**: Machine learning untuk job matching
2. **Video Interview**: Integrated video call untuk interview
3. **Skill Assessment**: Online test platform
4. **Career Development**: Training & certification tracking
5. **Payroll Integration**: Untuk perusahaan klien

### 12.2 Market Expansion
1. **Geographic**: Expand ke kota-kota besar lain
2. **Industry**: Spesialisasi per industri
3. **Service**: Tambah layanan training/upskilling
4. **Platform**: Native mobile apps

## 13. Kesimpulan

Sistem ini dirancang untuk mentransformasi operasional yayasan dari manual menjadi digital, dengan fokus pada:
- **Efisiensi**: Otomatisasi mengurangi workload 80%
- **Skalabilitas**: Siap handle 10x growth
- **Profesionalisme**: Meningkatkan kredibilitas dengan klien
- **Data-Driven**: Decisions based on real insights

Dengan implementasi yang tepat, sistem ini akan menjadi competitive advantage dalam industri penyaluran tenaga kerja di Indonesia.

---

*Document Version: 1.0*  
*Created: [Current Date]*  
*Last Updated: [Current Date]*