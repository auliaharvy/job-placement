#!/bin/bash

echo "🔧 Fixing Applicant Model Search Issue..."

cd /Users/auliaharvy/AI\ Development/job-placement-system/new-backend

echo "🧹 Clearing cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo "🧪 Testing applicants endpoint..."
login_response=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

token=$(echo "$login_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ -n "$token" ]; then
    echo "✅ Login successful"
    
    applicants_response=$(curl -s -w "%{http_code}" -X GET "http://localhost:8000/api/v1/applicants?page=1&per_page=10&search=&status=" \
        -H "Authorization: Bearer $token" \
        -H "Content-Type: application/json")
    
    http_code=$(echo "$applicants_response" | tail -n1)
    response_body=$(echo "$applicants_response" | head -n -1)
    
    if [ "$http_code" = "200" ]; then
        echo "✅ Applicants endpoint working!"
        echo "Response preview: $(echo "$response_body" | head -c 200)..."
    else
        echo "❌ Applicants endpoint failed (HTTP $http_code)"
        echo "Response: $response_body"
    fi
else
    echo "❌ Login failed"
fi
