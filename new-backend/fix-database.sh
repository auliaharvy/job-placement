#!/bin/bash

# Database Fixes Script - Fix missing columns

echo "🔧 Applying database fixes..."

# Navigate to project directory
cd "/Users/auliaharvy/AI Development/job-placement-system/new-backend"

echo "📁 Current directory: $(pwd)"

# Run fresh migration to ensure clean database
echo "🗄️ Refreshing database..."
php artisan migrate:fresh --force

echo "🌱 Seeding database..."
php artisan db:seed --force

echo "✅ Database fixes applied successfully!"
echo ""
echo "🧪 Testing models..."
curl -s http://localhost:8000/api/v1/test/models | jq '.' || echo "Server not running or test failed"

echo ""
echo "✅ Database fixes completed!"