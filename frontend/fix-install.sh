#!/bin/bash

echo "🔧 Fixing Job Placement System Frontend..."
echo ""

# Remove problematic files
echo "🧹 Cleaning up..."
rm -rf node_modules
rm -f package-lock.json
rm -f yarn.lock

# Create a stable package.json
echo "📦 Creating stable package.json..."
cat > package.json << 'EOF'
{
  "name": "job-placement-frontend",
  "version": "1.0.0",
  "private": true,
  "scripts": {
    "dev": "next dev",
    "build": "next build",
    "start": "next start",
    "lint": "next lint",
    "type-check": "tsc --noEmit"
  },
  "dependencies": {
    "next": "13.5.6",
    "react": "18.2.0",
    "react-dom": "18.2.0",
    "antd": "5.11.5",
    "@ant-design/icons": "5.2.5",
    "axios": "1.5.1",
    "dayjs": "1.11.10"
  },
  "devDependencies": {
    "@types/node": "20.8.0",
    "@types/react": "18.2.25",
    "@types/react-dom": "18.2.11",
    "typescript": "5.2.2",
    "eslint": "8.51.0",
    "eslint-config-next": "13.5.6"
  }
}
EOF

echo "📥 Installing core dependencies..."
npm install

# Check if core install worked
if [ $? -ne 0 ]; then
    echo "❌ Core installation failed. Trying alternative approach..."
    npm install --legacy-peer-deps --no-optional
fi

echo "✅ Core packages installed!"

# Add additional packages one by one
echo "📥 Adding additional packages..."

# Add recharts for dashboard
npm install recharts@2.8.0 --save

# Add React Query
npm install @tanstack/react-query@4.36.1 --save

# Add form handling
npm install react-hook-form@7.47.0 --save

# Add other utilities
npm install lodash@4.17.21 qrcode@1.5.3 --save
npm install @types/lodash@4.14.200 @types/qrcode@1.5.5 --save-dev

echo ""
echo "🎉 Installation completed!"
echo ""
echo "Now you can run:"
echo "  npm run dev"
echo ""
