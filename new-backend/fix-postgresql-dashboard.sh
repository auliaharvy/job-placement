#!/bin/bash

# Job Placement System - PostgreSQL Dashboard Fix

echo "🔧 Fixing Dashboard PostgreSQL Compatibility..."
echo ""

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found."
    echo "Please run this script from the Laravel backend directory."
    exit 1
fi

echo "📋 Running database migrations..."
php artisan migrate --force

echo ""
echo "🌱 Running database seeders..."
php artisan db:seed --force

echo ""
echo "🧹 Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🔄 Optimizing application..."
php artisan config:cache
php artisan route:cache

echo ""
echo "🧪 Testing dashboard endpoint..."
response=$(curl -s -X POST "http://localhost:8000/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

# Extract token
token=$(echo "$response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')

if [ -n "$token" ]; then
    echo "✅ Login successful! Testing dashboard..."
    
    dashboard_response=$(curl -s -X GET "http://localhost:8000/api/v1/dashboard" \
        -H "Authorization: Bearer $token" \
        -H "Content-Type: application/json")
    
    if echo "$dashboard_response" | grep -q '"success":true'; then
        echo "✅ Dashboard endpoint working!"
        echo ""
        echo "🎉 PostgreSQL compatibility fix completed successfully!"
    else
        echo "❌ Dashboard test failed!"
        echo "Response: $dashboard_response"
    fi
else
    echo "❌ Login failed!"
    echo "Response: $response"
fi

echo ""
echo "📝 Ready for testing:"
echo "   Login: admin@jobplacement.com / password123"
echo "   Dashboard: GET /api/v1/dashboard"
echo ""
