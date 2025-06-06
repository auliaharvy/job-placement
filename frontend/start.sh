#!/bin/bash

echo "🚀 Quick Start Frontend"
echo "======================"

# Check if we're in frontend directory
if [[ ! -f "package.json" ]]; then
    echo "❌ Please run from frontend directory"
    exit 1
fi

# Remove problematic files
echo "🧹 Cleaning up conflicting files..."
rm -f tailwind.config.js 2>/dev/null
rm -rf src/pages/dashboard/index.tsx 2>/dev/null

# Clean install if node_modules has issues
if [[ ! -d "node_modules" ]] || [[ -f "node_modules/.package-lock.json" ]]; then
    echo "📦 Fresh install..."
    rm -rf node_modules package-lock.json
    npm install
fi

echo "✅ Cleanup complete"
echo ""
echo "🌐 Starting development server..."
echo "Access: http://localhost:3000"
echo "Login: admin@example.com / password123"
echo ""

npm run dev
