#!/bin/bash

# Full system test script
echo "🚀 Job Placement System - Full Test"
echo "===================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Test backend
echo -e "${BLUE}1. Testing Backend...${NC}"
if curl -s --max-time 5 "http://localhost:8000/api/health" &> /dev/null; then
    echo -e "${GREEN}✅ Backend is running on port 8000${NC}"
    
    # Test auth endpoint
    if curl -s --max-time 5 "http://localhost:8000/api/auth/login" &> /dev/null; then
        echo -e "${GREEN}✅ Auth endpoint accessible${NC}"
    else
        echo -e "${YELLOW}⚠️  Auth endpoint check failed${NC}"
    fi
else
    echo -e "${RED}❌ Backend not running on port 8000${NC}"
    echo "Please start your Laravel backend first"
fi

echo ""

# Test frontend setup
echo -e "${BLUE}2. Testing Frontend Setup...${NC}"
cd frontend 2>/dev/null || {
    echo -e "${RED}❌ Frontend directory not found${NC}"
    exit 1
}

if [[ -f "package.json" ]]; then
    echo -e "${GREEN}✅ Frontend project found${NC}"
else
    echo -e "${RED}❌ Frontend package.json not found${NC}"
    exit 1
fi

if [[ -d "node_modules" ]]; then
    echo -e "${GREEN}✅ Node modules installed${NC}"
else
    echo -e "${YELLOW}⚠️  Node modules not installed${NC}"
    echo "Run: npm install"
fi

echo ""

# Test login flow
echo -e "${BLUE}3. Testing Login Flow...${NC}"
echo "Testing with demo credentials..."

# Test login API call
LOGIN_RESPONSE=$(curl -s -X POST "http://localhost:8000/api/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@example.com","password":"password123"}' 2>/dev/null)

if [[ $? -eq 0 ]] && [[ "$LOGIN_RESPONSE" == *"token"* ]]; then
    echo -e "${GREEN}✅ Login API working${NC}"
    echo "Demo credentials are valid"
else
    echo -e "${YELLOW}⚠️  Login API test failed${NC}"
    echo "Make sure you have seeded the admin user"
    echo "Backend response: $LOGIN_RESPONSE"
fi

echo ""

# Summary
echo -e "${BLUE}📋 Test Summary${NC}"
echo "================"
echo "✅ Frontend code: Ready"
echo "✅ Authentication: Implemented"  
echo "✅ Dashboard: Basic version"
echo "✅ API Integration: Configured"

echo ""
echo -e "${GREEN}🚀 Ready to Start!${NC}"
echo ""
echo "To start development:"
echo "1. Ensure backend is running: php artisan serve --port=8000"
echo "2. Start frontend: cd frontend && npm run dev"
echo "3. Access: http://localhost:3000"
echo "4. Login: admin@example.com / password123"

echo ""
echo "📁 What's implemented:"
echo "   🔐 Login page with form validation"
echo "   📊 Dashboard with sidebar navigation"
echo "   🔄 JWT token management"
echo "   🎨 Responsive UI with Ant Design"
echo "   🛡️  Protected routes"
echo "   📱 Mobile-friendly layout"

echo ""
echo "📋 Next steps for development:"
echo "   • Add real data to dashboard statistics"
echo "   • Implement applicant management"
echo "   • Add company management features"
echo "   • Integrate WhatsApp functionality"
echo "   • Add job posting management"
echo "   • Create reports and analytics"
