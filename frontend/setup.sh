#!/bin/bash

# Quick setup script for frontend development

echo "⚡ Quick Frontend Setup"
echo "======================"

# Make scripts executable
chmod +x start-frontend.sh
chmod +x test-frontend.sh

# Check if we're in the right directory
if [[ ! -f "package.json" ]]; then
    echo "❌ Please run this script from the frontend directory"
    exit 1
fi

# Install dependencies if needed
if [[ ! -d "node_modules" ]]; then
    echo "📦 Installing dependencies..."
    npm install
else
    echo "✅ Dependencies already installed"
fi

# Create .env.local if it doesn't exist
if [[ ! -f ".env.local" ]]; then
    echo "🔧 Creating .env.local..."
    cat > .env.local << EOF
# Job Placement System Frontend Environment
NEXT_PUBLIC_API_URL=http://localhost:8000/api
NEXT_PUBLIC_WHATSAPP_API_URL=http://brevet.online:8005
NEXT_PUBLIC_APP_NAME=Job Placement System
NEXT_PUBLIC_DEBUG=true
EOF
    echo "✅ .env.local created"
else
    echo "✅ .env.local already exists"
fi

echo ""
echo "🎯 Setup Complete!"
echo ""
echo "Available commands:"
echo "  npm run dev          - Start development server"
echo "  ./start-frontend.sh  - Start with environment info"
echo "  ./test-frontend.sh   - Run frontend tests"
echo "  npm run build        - Build for production"
echo ""
echo "🌐 After starting, access:"
echo "  Frontend: http://localhost:3000"
echo "  Login: admin@example.com / password123"
echo ""
echo "📝 Note: Make sure your backend is running on port 8000"
