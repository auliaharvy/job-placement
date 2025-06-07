#!/bin/bash

# Agent Link Analytics Setup Script
echo "🚀 Setting up Agent Link Analytics..."

# Navigate to backend directory
cd /Users/auliaharvy/AI\ Development/job-placement-system/new-backend

# Install dependencies if needed
echo "📦 Installing/updating PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate

# Run seeders (including new AgentLinkClickSeeder)
echo "🌱 Seeding database with test data..."
php artisan db:seed --class=AgentLinkClickSeeder

# Clear caches
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Generate API documentation (if available)
echo "📖 Generating API documentation..."
php artisan route:list --path=api

echo "✅ Agent Link Analytics setup complete!"
echo ""
echo "📋 New API Endpoints Available:"
echo "   GET /api/v1/agents (public)"
echo "   GET /api/v1/agents/referral/{code} (public)"
echo "   POST /api/v1/analytics/track-click (public)"
echo "   POST /api/v1/analytics/mark-conversion (public)"
echo "   GET /api/v1/analytics/agents/{id} (protected)"
echo "   GET /api/v1/analytics/agents (protected)"
echo "   GET /api/v1/analytics/dashboard (protected)"
echo ""
echo "🧪 Test URLs:"
echo "   Frontend: http://localhost:3000/example-form"
echo "   Agent Management: http://localhost:3000/agent-management"
echo "   Test with agent: http://localhost:3000/example-form?agent=1"
echo "   Test with referral: http://localhost:3000/example-form?ref=JOHN001"
echo ""
echo "📊 Sample Analytics Data:"
echo "   • 3 test agents created (John Doe, Jane Smith, Michael Johnson)"
echo "   • 20-100 random clicks per agent over last 30 days"
echo "   • 10-20% conversion rate simulation"
echo "   • Multiple UTM sources: facebook, instagram, whatsapp, email, etc."
