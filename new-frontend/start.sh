#!/bin/bash

echo "🚀 Starting Job Placement System Frontend"
echo "========================================="

# Check if node_modules exists
if [[ ! -d "node_modules" ]]; then
    echo "📦 Dependencies not found. Installing..."
    npm install
fi

# Check if backend is running
echo "🔍 Checking backend connection..."
if curl -s --max-time 5 "http://localhost:8000/api/health" &> /dev/null; then
    echo "✅ Backend is running"
else
    echo "⚠️  Backend not detected at http://localhost:8000"
    echo "   Make sure to start your Laravel backend first:"
    echo "   cd ../new-backend && php artisan serve --port=8000"
fi

echo ""
echo "🌐 Starting Next.js development server..."
echo "📍 Frontend will be available at: http://localhost:3000"
echo "🔑 Demo credentials: admin@example.com / password123"
echo ""

# Start the development server with turbopack for faster builds
npm run dev
