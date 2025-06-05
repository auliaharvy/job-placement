#!/bin/bash

echo "🚀 TESTING ALL MAIN ENDPOINTS"
echo ""

cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

php artisan cache:clear >/dev/null 2>&1
php artisan config:clear >/dev/null 2>&1

echo "1. Login..."
LOGIN=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

TOKEN=$(echo "$LOGIN" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ -n "$TOKEN" ]; then
    echo "✅ Login OK"
    
    echo "2. Dashboard..."
    DASH=$(curl -s -o /dev/null -w "%{http_code}" -X GET "http://localhost:8000/api/v1/dashboard" \
        -H "Authorization: Bearer $TOKEN")
    [ "$DASH" = "200" ] && echo "✅ Dashboard OK" || echo "❌ Dashboard failed ($DASH)"
    
    echo "3. Applicants..."
    APPS=$(curl -s -o /dev/null -w "%{http_code}" -X GET "http://localhost:8000/api/v1/applicants?page=1&per_page=10" \
        -H "Authorization: Bearer $TOKEN")
    [ "$APPS" = "200" ] && echo "✅ Applicants OK" || echo "❌ Applicants failed ($APPS)"
    
    echo "4. Applications..."
    APPLICATIONS=$(curl -s -o /dev/null -w "%{http_code}" -X GET "http://localhost:8000/api/v1/applications?page=1&per_page=10" \
        -H "Authorization: Bearer $TOKEN")
    [ "$APPLICATIONS" = "200" ] && echo "✅ Applications OK" || echo "❌ Applications failed ($APPLICATIONS)"
    
    echo "5. Jobs..."
    JOBS=$(curl -s -o /dev/null -w "%{http_code}" -X GET "http://localhost:8000/api/v1/jobs?page=1&per_page=10" \
        -H "Authorization: Bearer $TOKEN")
    [ "$JOBS" = "200" ] && echo "✅ Jobs OK" || echo "❌ Jobs failed ($JOBS)"
    
    echo ""
    echo "🎉 ALL MAIN ENDPOINTS TESTED!"
    echo "Ready for Postman with admin@jobplacement.com / password123"
    
else
    echo "❌ Login failed"
fi
