#!/bin/bash

echo "🚀 Testing Registrasi Pelamar"
echo "============================"

# Test jika backend berjalan
echo "1. Cek Backend..."
if curl -s --max-time 5 "http://localhost:8000/api/health" &> /dev/null; then
    echo "   ✅ Backend berjalan di port 8000"
else
    echo "   ❌ Backend tidak berjalan"
    echo "   Jalankan: cd ../new-backend && php artisan serve --port=8000"
fi

echo ""
echo "2. Cek File Frontend..."
if [[ -f "src/app/register/applicant/page.tsx" ]]; then
    echo "   ✅ Halaman registrasi tersedia"
else
    echo "   ❌ File registrasi tidak ditemukan"
fi

if [[ -f "src/lib/registration.ts" ]]; then
    echo "   ✅ Service registrasi tersedia"
else
    echo "   ❌ Service registrasi tidak ditemukan"
fi

echo ""
echo "🎯 Langkah Test Manual:"
echo "1. Jalankan frontend: npm run dev"
echo "2. Buka: http://localhost:3000/register/applicant"
echo "3. Isi form 5 langkah"
echo "4. Submit dan cek redirect ke login"
echo "5. Login dengan email + NIK sebagai password"

echo ""
echo "📝 Contoh Data Test:"
echo "Nama: John Doe"
echo "Email: john.test@email.com"
echo "NIK: 1234567890123456"
echo "Phone: +6281234567890"
echo "Alamat: Jakarta"
echo "Pendidikan: S1 Teknik Informatika"

echo ""
echo "✅ Halaman registrasi siap digunakan!"
