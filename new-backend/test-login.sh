#!/bin/bash

# Test login API after database fix

echo "🧪 Testing Login API..."
echo ""

BASE_URL="http://localhost:8000/api/v1"

# Test login with admin credentials
echo "Testing login with admin credentials..."
response=$(curl -s -X POST "$BASE_URL/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@jobplacement.com","password":"password123"}')

echo "Response: $response"
echo ""

# Check if login successful
if echo "$response" | grep -q '"success":true'; then
    echo "✅ Login test PASSED!"
    
    # Extract token for further testing
    token=$(echo "$response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')
    
    if [ -n "$token" ]; then
        echo "🔑 Token obtained: ${token:0:20}..."
        
        # Test dashboard endpoint
        echo ""
        echo "Testing dashboard endpoint..."
        dashboard_response=$(curl -s -X GET "$BASE_URL/dashboard" \
            -H "Authorization: Bearer $token" \
            -H "Content-Type: application/json")
        
        if echo "$dashboard_response" | grep -q '"success":true'; then
            echo "✅ Dashboard test PASSED!"
        else
            echo "❌ Dashboard test FAILED!"
            echo "Response: $dashboard_response"
        fi
    else
        echo "⚠️ Could not extract token from response"
    fi
else
    echo "❌ Login test FAILED!"
    echo "Please check the database and user seeder"
fi
