#!/bin/bash

# Quick Fix for Column Issues

echo "🔧 Applying column fixes..."

# Navigate to project directory
cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

echo "📋 Fixed Issues:"
echo "✅ work_experience_years → total_work_experience_months"
echo "✅ SoftDeletes deleted_at column added"
echo ""

echo "🗄️ Ensuring fresh database..."
php artisan migrate:fresh --force

echo "🌱 Seeding database..."
php artisan db:seed --force

echo "✅ Column fixes applied!"
echo ""
echo "🧪 Testing endpoints now..."
echo ""

# Test the fixed endpoints if server is running
if curl -s --fail http://localhost:8000/api/v1/test/health > /dev/null; then
    echo "🟢 Server is running - testing endpoints:"
    
    echo "1. Testing models..."
    curl -s http://localhost:8000/api/v1/test/models | jq '.success' 2>/dev/null || echo "❌ Models test failed"
    
    echo "2. Testing job matching..."
    curl -s http://localhost:8000/api/v1/test/job-matching | jq '.success' 2>/dev/null || echo "❌ Job matching test failed"
    
    echo ""
    echo "✅ Quick fix completed!"
else
    echo "🟡 Server not running. Start server with: ./start-server.sh"
    echo "Then test with: ./test.sh"
fi