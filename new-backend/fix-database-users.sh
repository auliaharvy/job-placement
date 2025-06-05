#!/bin/bash

# Job Placement System - Database Fix Script

echo "🔧 Fixing Job Placement System Database..."
echo ""

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found."
    echo "Please run this script from the Laravel backend directory."
    exit 1
fi

echo "📋 Running database migrations..."
php artisan migrate --force

echo ""
echo "🌱 Running database seeders..."
php artisan db:seed --force

echo ""
echo "🧹 Clearing application cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "🔄 Optimizing application..."
php artisan config:cache
php artisan route:cache

echo ""
echo "✅ Database fix completed!"
echo ""
echo "📝 Default users created:"
echo "   Super Admin: admin@jobplacement.com / password123"
echo "   Direktur: direktur@jobplacement.com / password123" 
echo "   HR Staff: hr@jobplacement.com / password123"
echo "   Agent: agent@jobplacement.com / password123"
echo "   Applicant: applicant@jobplacement.com / password123"
echo ""
echo "🚀 You can now test the API with Postman!"
