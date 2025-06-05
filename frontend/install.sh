#!/bin/bash

echo "🔧 Installing Job Placement System Frontend Dependencies..."
echo ""

# Remove existing node_modules and lock file
if [ -d "node_modules" ]; then
    echo "🧹 Removing old node_modules..."
    rm -rf node_modules
fi

if [ -f "package-lock.json" ]; then
    echo "🧹 Removing package-lock.json..."
    rm -f package-lock.json
fi

# Clear npm cache
echo "🧹 Clearing npm cache..."
npm cache clean --force

# Install with different strategies
echo "📦 Attempting installation..."

# Try normal install first
echo "🔄 Trying: npm install"
if npm install; then
    echo "✅ Installation successful!"
    exit 0
fi

# Try with legacy peer deps
echo "🔄 Trying: npm install --legacy-peer-deps"
if npm install --legacy-peer-deps; then
    echo "✅ Installation successful with legacy peer deps!"
    exit 0
fi

# Try with force
echo "🔄 Trying: npm install --force"
if npm install --force; then
    echo "✅ Installation successful with force!"
    exit 0
fi

# If all fail, try minimal install
echo "🔄 Trying minimal install..."
npm init -y
npm install next@latest react@latest react-dom@latest
npm install antd@latest @ant-design/icons@latest
npm install axios@latest dayjs@latest
npm install typescript@latest @types/node@latest @types/react@latest @types/react-dom@latest

if [ $? -eq 0 ]; then
    echo "✅ Minimal installation successful!"
    echo "⚠️  Some packages may need to be added manually"
else
    echo "❌ All installation methods failed"
    echo "Please check your Node.js version and internet connection"
    exit 1
fi
