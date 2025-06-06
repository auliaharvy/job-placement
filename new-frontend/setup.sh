#!/bin/bash

echo "🚀 Job Placement System - New Frontend Setup"
echo "============================================="

# Check if we're in the right directory
if [[ ! -f "package.json" ]]; then
    echo "❌ Please run this script from the new-frontend directory"
    exit 1
fi

# Install dependencies
echo "📦 Installing dependencies..."
npm install

if [[ $? -ne 0 ]]; then
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo "✅ Dependencies installed successfully"

# Create .env.local if it doesn't exist
if [[ ! -f ".env.local" ]]; then
    echo "🔧 Creating .env.local file..."
    cat > .env.local << 'EOF'
# Job Placement System Frontend Environment
NEXT_PUBLIC_API_URL=http://localhost:8000/api
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
echo "Tech Stack:"
echo "  • Next.js 15 (App Router)"
echo "  • React 19"
echo "  • TypeScript 5"
echo "  • Tailwind CSS 4"
echo "  • Lucide React Icons"
echo ""
echo "Available commands:"
echo "  npm run dev    - Start development server"
echo "  npm run build  - Build for production"
echo "  npm run start  - Start production server"
echo ""
echo "🌐 After starting:"
echo "  Frontend: http://localhost:3000"
echo "  Login: admin@example.com / password123"
echo ""
echo "📝 Make sure your backend is running on:"
echo "  Backend: http://localhost:8000"
