#!/bin/bash

# Quick start script for Job Placement System Frontend

echo "🚀 Starting Job Placement System Frontend..."
echo ""

# Check if we're in the right directory
if [ ! -f "package.json" ]; then
    echo "❌ Error: package.json not found."
    echo "Please run this script from the frontend directory."
    exit 1
fi

# Clean install to avoid version conflicts
if [ -d "node_modules" ]; then
    echo "🧹 Cleaning old node_modules..."
    rm -rf node_modules
    rm -f package-lock.json
fi

echo "📦 Installing dependencies with latest compatible versions..."
npm install

# Check if installation was successful
if [ $? -ne 0 ]; then
    echo "❌ Installation failed. Trying with --legacy-peer-deps..."
    npm install --legacy-peer-deps
fi

# Check if .env.local exists
if [ ! -f ".env.local" ]; then
    echo "⚙️  Setting up environment variables..."
    cat > .env.local << EOF
# Job Placement System Frontend Environment Variables
NEXT_PUBLIC_API_URL=http://localhost:3001/api
NEXT_PUBLIC_WHATSAPP_API_URL=http://localhost:3002
NEXT_PUBLIC_APP_NAME=Job Placement System
NEXT_PUBLIC_NODE_ENV=development
EOF
    echo "✅ Created .env.local"
fi

echo ""
echo "🌐 Starting development server..."
echo "📱 Frontend will be available at: http://localhost:3000"
echo "🔧 Backend API should be running at: http://localhost:3001"
echo ""
echo "📝 Demo Login Credentials:"
echo "   Email: admin@jobplacement.com"
echo "   Password: admin123"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start the development server
npm run dev
