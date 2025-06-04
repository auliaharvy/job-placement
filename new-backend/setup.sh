#!/bin/bash

# Job Placement System - Quick Setup Script
# Navigate to project directory and run this script

echo "🚀 Starting Job Placement System Setup..."

# Navigate to new-backend directory
cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

echo "📁 Current directory: $(pwd)"

# Check if .env exists, if not copy from example
if [ ! -f .env ]; then
    echo "📋 Creating .env file from example..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "✅ .env file already exists"
fi

# Install composer dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
    echo "✅ Composer dependencies installed"
else
    echo "✅ Vendor directory exists"
fi

# Generate application key if not set
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate:fresh --force

# Seed the database with sample data
echo "🌱 Seeding database with sample data..."
php artisan db:seed --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear all caches
echo "🧹 Clearing application cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "🎉 Setup completed successfully!"
echo ""
echo "🚀 To start the development server, run:"
echo "cd \"/Users/auliaharvy/AI Development/job-placement-system/new-backend\""
echo "php artisan serve"
echo ""
echo "📋 Test endpoints will be available at:"
echo "• Health Check: http://localhost:8000/api/v1/test/health"
echo "• Job Matching: http://localhost:8000/api/v1/test/job-matching"
echo "• WhatsApp Test: http://localhost:8000/api/v1/test/whatsapp"
echo ""
echo "💡 Check TESTING_FIXES.md for detailed testing commands"