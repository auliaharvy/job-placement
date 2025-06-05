#!/bin/bash

echo "🔧 Quick Fix - Adding missing User methods..."

cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

php artisan cache:clear >/dev/null 2>&1
php artisan config:clear >/dev/null 2>&1
php artisan route:clear >/dev/null 2>&1

echo "✅ Cache cleared"

echo "Testing applications endpoint..."

LOGIN_RESULT=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

TOKEN=$(echo "$LOGIN_RESULT" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ -n "$TOKEN" ]; then
    echo "✅ Login successful"
    
    APPS_RESULT=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X GET "http://localhost:8000/api/v1/applications?page=1&per_page=10&stage=" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    HTTP_STATUS=$(echo "$APPS_RESULT" | grep "HTTP_STATUS:" | cut -d: -f2)
    
    if [ "$HTTP_STATUS" = "200" ]; then
        echo "✅ Applications endpoint working! (HTTP $HTTP_STATUS)"
        echo "🎉 APPLICATIONS API FIXED!"
    else
        echo "❌ Applications endpoint failed (HTTP $HTTP_STATUS)"
        echo "Response: $(echo "$APPS_RESULT" | sed '/HTTP_STATUS:/d')"
    fi
else
    echo "❌ Login failed"
fi
