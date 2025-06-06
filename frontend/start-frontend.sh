#!/bin/bash

# Frontend Start Script for Job Placement System

echo "🚀 Starting Job Placement System Frontend..."

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found, using default configuration"
fi

echo "🔧 Environment Configuration:"
echo "API URL: ${NEXT_PUBLIC_API_URL:-http://localhost:8000/api}"
echo "WhatsApp API URL: ${NEXT_PUBLIC_WHATSAPP_API_URL:-http://brevet.online:8005}"

echo ""
echo "🌐 Starting Next.js development server..."
echo "📍 Frontend will be available at: http://localhost:3000"
echo "🔑 Demo login credentials: admin@example.com / password123"
echo ""

# Start the development server
npm run dev
