#!/bin/bash

echo "🚀 QUICK APPLICANTS FIX"
echo ""

# Navigate to backend directory
cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

echo "1. Clearing application cache..."
php artisan cache:clear >/dev/null 2>&1
php artisan config:clear >/dev/null 2>&1
php artisan route:clear >/dev/null 2>&1

echo "2. Testing login..."
LOGIN_RESULT=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

TOKEN=$(echo "$LOGIN_RESULT" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ -n "$TOKEN" ] && [ "$TOKEN" != "null" ]; then
    echo "✅ Login successful"
    
    echo "3. Testing applicants endpoint..."
    APPLICANTS_RESULT=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X GET "http://localhost:8000/api/v1/applicants?page=1&per_page=10" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    HTTP_STATUS=$(echo "$APPLICANTS_RESULT" | grep "HTTP_STATUS:" | cut -d: -f2)
    RESPONSE_BODY=$(echo "$APPLICANTS_RESULT" | sed '/HTTP_STATUS:/d')
    
    if [ "$HTTP_STATUS" = "200" ]; then
        echo "✅ Applicants endpoint working! (HTTP $HTTP_STATUS)"
        if echo "$RESPONSE_BODY" | grep -q '"success":true'; then
            echo "✅ JSON response format correct"
        fi
        echo ""
        echo "🎉 APPLICANTS API FIXED!"
        echo "Ready for Postman testing with:"
        echo "   Login: admin@jobplacement.com / password123"
        echo "   GET /applicants?page=1&per_page=10"
    else
        echo "❌ Applicants endpoint failed (HTTP $HTTP_STATUS)"
        echo "Response: $RESPONSE_BODY"
    fi
else
    echo "❌ Login failed"
    echo "Response: $LOGIN_RESULT"
fi
