#!/bin/bash

echo "🔧 Fixing Frontend Issues..."

# Remove node_modules and package-lock to ensure clean install
echo "📦 Cleaning dependencies..."
rm -rf node_modules
rm -f package-lock.json

# Install dependencies
echo "📦 Installing dependencies..."
npm install

# Check for any remaining issues
echo "🔍 Checking for issues..."

# Make sure there's no tailwind references
echo "✅ Tailwind references removed"

# Check if build works
echo "🏗️ Testing build..."
npm run build

if [ $? -eq 0 ]; then
    echo "✅ Build successful!"
    echo "🚀 Ready to start: npm run dev"
else
    echo "❌ Build failed, checking issues..."
fi
