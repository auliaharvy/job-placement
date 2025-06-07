# JSON untuk Membuat Job Posting dengan WhatsApp Auto Broadcast

```json
{
    "title": "Operator Pabrik Produksi",
    "position": "Operator Pabrik",
    "company_id": 1,
    "employment_type": "pkwt",
    "work_location": "Kawasan Industri MM2100, Bekasi",
    "work_city": "Bekasi",
    "work_province": "Jawa Barat",
    "work_arrangement": "onsite",
    "salary_min": 4500000,
    "salary_max": 6500000,
    "description": "Dicari Operator Pabrik untuk mengoperasikan mesin produksi, melakukan quality control, dan memastikan target produksi tercapai sesuai standar perusahaan.",
    "requirements": "Minimal lulusan SMA/SMK, berpengalaman minimal 1 tahun di bidang manufaktur, mampu bekerja shift, teliti dan bertanggung jawab.",
    "application_deadline": "2024-12-31",
    "required_education_levels": ["sma", "smk"],
    "min_experience_months": 12,
    "required_skills": ["Operasional Mesin", "Quality Control", "Kerja Shift", "Keselamatan Kerja"],
    "total_positions": 5,
    "priority": "medium",
    "status": "published",
    "auto_broadcast_whatsapp": true
}
```

## Field Penting untuk WhatsApp Broadcast:

- `auto_broadcast_whatsapp: true` - Mengaktifkan broadcast otomatis
- `status: "published"` - Job harus published untuk broadcast
- `required_education_levels`, `required_skills` - Untuk matching applicants

## Cara Kerja Sistem:

1. **Saat job dibuat** dengan `status: "published"` dan `auto_broadcast_whatsapp: true`
2. **Observer** akan detect dan queue job broadcast
3. **Background job** akan:
   - Mencari applicants yang matching dengan criteria
   - Kirim WhatsApp ke semua applicants matching
   - Log semua message ke database

## Log Messages:

Semua WhatsApp message akan tersimpan di tabel `whatsapp_logs` dengan informasi:
- Phone number, message content, status
- Job ID, applicant ID, broadcast ID
- Timestamp dan metadata
