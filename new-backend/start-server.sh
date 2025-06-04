#!/bin/bash

# Job Placement System - Server Start Script

echo "🚀 Starting Laravel Development Server..."

# Navigate to project directory
cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "❌ Dependencies not installed. Please run setup.sh first"
    exit 1
fi

# Start Laravel development server
echo "🌐 Server starting at http://localhost:8000"
echo "Press Ctrl+C to stop the server"
echo ""

php artisan serve