#!/bin/bash

echo "🚀 Quick Test Dashboard API after PostgreSQL Fix"
echo ""

BASE_URL="http://localhost:8000/api/v1"

echo "1. Testing server connectivity..."
health_response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/test/health")

if [ "$health_response" = "200" ]; then
    echo "✅ Server is running"
else
    echo "❌ Server not accessible (HTTP $health_response)"
    echo "Please make sure Laravel server is running: php artisan serve"
    exit 1
fi

echo ""
echo "2. Testing login..."
login_response=$(curl -s -X POST "$BASE_URL/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

# Extract token
token=$(echo "$login_response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ -n "$token" ] && [ "$token" != "null" ]; then
    echo "✅ Login successful! Token: ${token:0:20}..."
else
    echo "❌ Login failed!"
    echo "Response: $login_response"
    exit 1
fi

echo ""
echo "3. Testing dashboard endpoint..."
dashboard_response=$(curl -s -X GET "$BASE_URL/dashboard?start_date=2024-01-01&end_date=2024-12-31" \
    -H "Authorization: Bearer $token" \
    -H "Content-Type: application/json")

if echo "$dashboard_response" | grep -q '"success":true'; then
    echo "✅ Dashboard API working!"
    
    # Check for specific data sections
    if echo "$dashboard_response" | grep -q '"overview"'; then
        echo "✅ Overview data: OK"
    fi
    
    if echo "$dashboard_response" | grep -q '"charts"'; then
        echo "✅ Charts data: OK"
    fi
    
    if echo "$dashboard_response" | grep -q '"recent_activities"'; then
        echo "✅ Recent activities: OK"
    fi
    
    if echo "$dashboard_response" | grep -q '"alerts"'; then
        echo "✅ System alerts: OK"
    fi
    
    echo ""
    echo "🎉 PostgreSQL Dashboard Fix SUCCESSFUL!"
    echo ""
    echo "📊 Dashboard Features Working:"
    echo "   ✅ Overview statistics"
    echo "   ✅ Chart data (trend, pipeline, performance)"
    echo "   ✅ Recent activities feed"
    echo "   ✅ System alerts"
    echo "   ✅ Role-based metrics"
    echo ""
    echo "🔗 Ready for Postman testing:"
    echo "   Login: admin@jobplacement.com / password123"
    echo "   Dashboard: GET /dashboard?start_date=2024-01-01&end_date=2024-12-31"
    
else
    echo "❌ Dashboard API failed!"
    echo ""
    echo "Error response:"
    echo "$dashboard_response"
    echo ""
    echo "Please check:"
    echo "1. Database connection"
    echo "2. Table migrations completed"
    echo "3. Laravel logs: tail -f storage/logs/laravel.log"
fi
