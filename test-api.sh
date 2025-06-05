#!/bin/bash

# Job Placement System API Quick Test Script
# This script performs basic API testing using curl

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
BASE_URL="http://localhost:8000/api/v1"
ADMIN_EMAIL="admin@jobplacement.com"
ADMIN_PASSWORD="password123"
TOKEN=""

# Functions
print_header() {
    echo -e "${BLUE}============================================${NC}"
    echo -e "${BLUE}  Job Placement System - API Quick Test  ${NC}"
    echo -e "${BLUE}============================================${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Test server connectivity
test_server() {
    print_info "Testing server connectivity..."
    
    response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/test/health" || echo "000")
    
    if [ "$response" = "200" ]; then
        print_success "Server is running and accessible"
        return 0
    else
        print_error "Server is not accessible (HTTP $response)"
        print_info "Make sure Laravel server is running: php artisan serve"
        return 1
    fi
}

# Login and get token
login() {
    print_info "Logging in as admin..."
    
    response=$(curl -s -X POST "$BASE_URL/auth/login" \
        -H "Content-Type: application/json" \
        -d "{\"email\":\"$ADMIN_EMAIL\",\"password\":\"$ADMIN_PASSWORD\"}")
    
    # Extract token using grep and sed (works on most systems)
    TOKEN=$(echo "$response" | grep -o '"token":"[^"]*"' | sed 's/"token":"\([^"]*\)"/\1/')
    
    if [ -n "$TOKEN" ] && [ "$TOKEN" != "null" ]; then
        print_success "Login successful! Token obtained."
        return 0
    else
        print_error "Login failed!"
        echo "Response: $response"
        return 1
    fi
}

# Test dashboard endpoint
test_dashboard() {
    print_info "Testing dashboard endpoint..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/dashboard" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "Dashboard endpoint working"
        return 0
    else
        print_error "Dashboard endpoint failed (HTTP $http_code)"
        return 1
    fi
}

# Test applicants endpoint
test_applicants() {
    print_info "Testing applicants endpoint..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/applicants" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "Applicants endpoint working"
        return 0
    else
        print_error "Applicants endpoint failed (HTTP $http_code)"
        return 1
    fi
}

# Test jobs endpoint
test_jobs() {
    print_info "Testing jobs endpoint..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/jobs" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "Jobs endpoint working"
        return 0
    else
        print_error "Jobs endpoint failed (HTTP $http_code)"
        return 1
    fi
}

# Test public jobs endpoint (no auth required)
test_public_jobs() {
    print_info "Testing public jobs endpoint..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/jobs/public")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "Public jobs endpoint working"
        return 0
    else
        print_error "Public jobs endpoint failed (HTTP $http_code)"
        return 1
    fi
}

# Test applications endpoint
test_applications() {
    print_info "Testing applications endpoint..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/applications" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "Applications endpoint working"
        return 0
    else
        print_error "Applications endpoint failed (HTTP $http_code)"
        return 1
    fi
}

# Test WhatsApp status
test_whatsapp() {
    print_info "Testing WhatsApp endpoint..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/whatsapp/status" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "WhatsApp endpoint working"
        return 0
    else
        print_warning "WhatsApp endpoint failed (HTTP $http_code) - This is normal if WhatsApp gateway is not running"
        return 1
    fi
}

# Test creating an applicant
test_create_applicant() {
    print_info "Testing create applicant..."
    
    test_data='{
        "full_name": "Test User",
        "email": "test.user@email.com",
        "phone": "+6281234567890",
        "date_of_birth": "1995-05-15",
        "gender": "male",
        "education_level": "bachelor",
        "work_experience_years": 3,
        "current_status": "available",
        "address": {
            "province": "DKI Jakarta",
            "city": "Jakarta Selatan",
            "district": "Kebayoran Baru",
            "postal_code": "12110",
            "detail": "Jl. Test No. 123"
        },
        "skills": ["Testing", "API", "Postman"]
    }'
    
    response=$(curl -s -w "%{http_code}" -X POST "$BASE_URL/applicants" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Content-Type: application/json" \
        -d "$test_data")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "201" ] || [ "$http_code" = "200" ]; then
        print_success "Create applicant working"
        return 0
    else
        print_warning "Create applicant failed (HTTP $http_code) - May be due to validation or existing data"
        return 1
    fi
}

# Test models and database
test_models() {
    print_info "Testing models and database..."
    
    response=$(curl -s -w "%{http_code}" -X GET "$BASE_URL/test/models")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ]; then
        print_success "Models and database working"
        return 0
    else
        print_error "Models test failed (HTTP $http_code)"
        return 1
    fi
}

# Generate test data
generate_test_data() {
    print_info "Generating test data..."
    
    test_data='{
        "users": 2,
        "companies": 2,
        "job_postings": 5,
        "applicants": 10,
        "applications": 15
    }'
    
    response=$(curl -s -w "%{http_code}" -X POST "$BASE_URL/test/generate-test-data" \
        -H "Content-Type: application/json" \
        -d "$test_data")
    
    http_code=$(echo "$response" | tail -n1)
    
    if [ "$http_code" = "200" ] || [ "$http_code" = "201" ]; then
        print_success "Test data generated successfully"
        return 0
    else
        print_warning "Test data generation failed (HTTP $http_code) - May already exist"
        return 1
    fi
}

# Show summary
show_summary() {
    echo ""
    echo -e "${BLUE}==================== SUMMARY ====================${NC}"
    echo ""
    echo "✅ Successful tests:"
    echo "   - Server connectivity"
    echo "   - Authentication (Login)"
    echo "   - Protected endpoints (Dashboard, Applicants, Jobs, Applications)"
    echo "   - Public endpoints (Public Jobs)"
    echo "   - Database models"
    echo ""
    echo "⚠️  Optional tests (may fail if services not running):"
    echo "   - WhatsApp integration"
    echo "   - Create operations (may fail due to validation/duplicates)"
    echo ""
    echo "🚀 Next steps:"
    echo "   1. Import Postman collection for detailed testing"
    echo "   2. Test frontend integration"
    echo "   3. Setup WhatsApp gateway if needed"
    echo ""
    echo -e "${GREEN}API is ready for use! 🎉${NC}"
}

# Main execution
main() {
    print_header
    
    # Test basic connectivity
    if ! test_server; then
        exit 1
    fi
    
    # Test models and database
    if ! test_models; then
        print_error "Database or models not working properly"
        exit 1
    fi
    
    # Login to get token
    if ! login; then
        print_error "Cannot proceed without authentication"
        exit 1
    fi
    
    # Test core endpoints
    test_dashboard
    test_applicants
    test_jobs
    test_public_jobs
    test_applications
    
    # Test optional features
    test_whatsapp
    
    # Test data operations
    test_create_applicant
    generate_test_data
    
    # Show final summary
    show_summary
}

# Handle script arguments
if [ $# -eq 0 ]; then
    main
else
    case $1 in
        server)
            test_server
            ;;
        login)
            test_server && login
            ;;
        dashboard)
            test_server && login && test_dashboard
            ;;
        applicants)
            test_server && login && test_applicants
            ;;
        jobs)
            test_server && login && test_jobs
            ;;
        whatsapp)
            test_server && login && test_whatsapp
            ;;
        models)
            test_models
            ;;
        data)
            generate_test_data
            ;;
        all)
            main
            ;;
        *)
            echo "Usage: $0 [server|login|dashboard|applicants|jobs|whatsapp|models|data|all]"
            echo ""
            echo "Commands:"
            echo "  server      Test server connectivity"
            echo "  login       Test authentication"
            echo "  dashboard   Test dashboard endpoint"
            echo "  applicants  Test applicants endpoint"
            echo "  jobs        Test jobs endpoint"
            echo "  whatsapp    Test WhatsApp endpoint"
            echo "  models      Test database models"
            echo "  data        Generate test data"
            echo "  all         Run all tests (default)"
            exit 1
            ;;
    esac
fi
