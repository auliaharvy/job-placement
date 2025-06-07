#!/bin/bash

echo "🚀 Testing Job Placement System Frontend"
echo "========================================="

# Navigate to frontend directory
cd /Users/auliaharvy/AI\ Development/job-placement-system/new-frontend

echo "📦 Installing dependencies..."
npm install

echo "🔧 Building the application..."
npm run build

if [ $? -eq 0 ]; then
    echo "✅ Build successful!"
    echo ""
    echo "🌐 Starting development server..."
    echo "Frontend will be available at: http://localhost:3000"
    echo ""
    echo "📱 Available routes:"
    echo "  • http://localhost:3000/dashboard"
    echo "  • http://localhost:3000/applicants"
    echo "  • http://localhost:3000/companies"
    echo "  • http://localhost:3000/jobs"
    echo "  • http://localhost:3000/agent-management"
    echo "  • http://localhost:3000/whatsapp"
    echo "  • http://localhost:3000/reports"
    echo ""
    echo "🔐 Login credentials (if needed):"
    echo "  • Admin: admin@jobplacement.com / admin123"
    echo "  • Agent: agent1@jobplacement.com / agent123"
    echo ""
    echo "Press Ctrl+C to stop the server"
    echo ""
    
    npm run dev
else
    echo "❌ Build failed! Please check the errors above."
    exit 1
fi
