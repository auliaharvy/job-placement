#!/bin/bash

# Job Placement System - Testing Script

echo "🧪 Running Job Placement System Tests..."
echo ""

BASE_URL="http://localhost:8000/api/v1"

# Function to test endpoint
test_endpoint() {
    local endpoint=$1
    local method=${2:-GET}
    local data=${3:-}
    
    echo "🔍 Testing: $method $endpoint"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -w "\n%{http_code}" "$BASE_URL$endpoint")
    else
        response=$(curl -s -w "\n%{http_code}" -X "$method" -H "Content-Type: application/json" -d "$data" "$BASE_URL$endpoint")
    fi
    
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | head -n -1)
    
    if [ "$http_code" = "200" ]; then
        echo "✅ Success (200)"
        echo "$body" | jq . 2>/dev/null || echo "$body"
    else
        echo "❌ Failed ($http_code)"
        echo "$body"
    fi
    echo ""
}

# Check if server is running
echo "🌐 Checking if server is running..."
if ! curl -s http://localhost:8000 >/dev/null; then
    echo "❌ Server is not running!"
    echo "Please start server first: ./start-server.sh"
    exit 1
fi
echo "✅ Server is running"
echo ""

# Run tests
echo "=== BASIC TESTS ==="
test_endpoint "/test/health"
test_endpoint "/test/models"

echo "=== SERVICE TESTS ==="
test_endpoint "/test/job-matching"
test_endpoint "/test/whatsapp"

echo "=== WORKFLOW TESTS ==="
test_endpoint "/test/workflow"

echo "=== WHATSAPP INTEGRATION TESTS ==="
test_endpoint "/test/whatsapp/status"

# Test message sending (optional - only if phone number provided)
if [ ! -z "$1" ]; then
    echo "=== WHATSAPP MESSAGE TEST ==="
    test_endpoint "/test/whatsapp/send-test-message" "POST" "{\"phone\":\"$1\",\"message\":\"🎉 Test dari Job Placement System berhasil!\"}"
fi

echo "🎉 Testing completed!"
echo ""
echo "💡 To test WhatsApp messages, run:"
echo "./test.sh 628123456789"