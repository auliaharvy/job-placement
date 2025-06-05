#!/bin/bash

cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

echo "🔧 Quick Fix - Clearing cache and testing..."

php artisan cache:clear
php artisan config:clear  
php artisan route:clear

echo "✅ Cache cleared, testing applicants endpoint..."

curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}' | \
    grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/' > /tmp/token.txt

TOKEN=$(cat /tmp/token.txt)

if [ -n "$TOKEN" ]; then
    echo "Testing with token: ${TOKEN:0:20}..."
    curl -s -X GET "http://localhost:8000/api/v1/applicants?page=1&per_page=10" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" | \
        python3 -m json.tool 2>/dev/null || echo "Response received but not valid JSON"
else
    echo "❌ Failed to get token"
fi

rm -f /tmp/token.txt
