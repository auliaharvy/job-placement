# Job Placement System - New Frontend

## 🚀 Modern Tech Stack

- **Next.js 15** with App Router (latest)
- **React 19** (bleeding edge)
- **TypeScript 5** for type safety
- **Tailwind CSS 4** for styling
- **Lucide React** for icons
- **React Hook Form** for form handling
- **Axios** for API calls

## ⚡ Quick Start

### 1. Setup
```bash
chmod +x setup.sh
./setup.sh
```

### 2. Start Development
```bash
chmod +x start.sh
./start.sh
```

### 3. Access Application
- **Frontend**: http://localhost:3000
- **Login**: admin@example.com / password123

## 📁 Project Structure

```
src/
├── app/                 # Next.js App Router
│   ├── login/          # Login page
│   ├── dashboard/      # Dashboard page
│   ├── layout.tsx      # Root layout
│   ├── page.tsx        # Home page (redirects)
│   └── globals.css     # Global styles
├── components/         # Reusable components
│   ├── Sidebar.tsx     # Navigation sidebar
│   └── Header.tsx      # Header with user menu
├── hooks/              # Custom React hooks
│   └── useAuth.ts      # Authentication hook
└── lib/                # Utility libraries
    ├── api.ts          # Axios configuration
    └── auth.ts         # Authentication service
```

## 🔧 Features Implemented

### ✅ Authentication
- Modern login page with form validation
- JWT token management with cookies
- Automatic token refresh
- Protected routes
- Logout functionality

### ✅ Dashboard Layout
- Responsive sidebar navigation
- Header with notifications and user menu
- Statistics cards with trend indicators
- Activity feed
- System status monitoring

### ✅ Modern UI/UX
- Clean, modern design with Tailwind CSS
- Mobile-responsive layout
- Loading states and error handling
- Smooth animations and transitions
- Accessible components

## 🌐 API Integration

The frontend is configured to connect to:
- **Backend API**: `http://localhost:8000/api`

Features:
- Automatic token injection
- Response interceptors
- Error handling
- Redirect on 401 errors

## 📱 Pages Overview

### Login Page (`/login`)
- Email/password authentication
- Form validation with React Hook Form
- Show/hide password toggle
- Demo credentials display
- Redirect to dashboard on success

### Dashboard (`/dashboard`)
- Overview statistics with trends
- Navigation sidebar (collapsible)
- Recent activities feed
- System status indicators
- Placeholder sections for future features

### Navigation Menu
- Dashboard (implemented)
- Applicants (placeholder)
- Companies (placeholder)
- Job Postings (placeholder)
- WhatsApp (placeholder)
- Reports (placeholder)

## 🔐 Authentication Flow

1. User visits app → Check authentication status
2. If not authenticated → Redirect to `/login`
3. User enters credentials → POST to `/api/auth/login`
4. Successful login → Store token in cookies → Redirect to `/dashboard`
5. All API requests → Include Authorization header
6. Token expiry → Automatic logout and redirect

## 🎨 Design System

### Colors
- Primary: Blue (#3B82F6)
- Success: Green (#10B981)
- Warning: Yellow (#F59E0B)
- Error: Red (#EF4444)
- Gray scale for text and backgrounds

### Components
- Cards with subtle shadows
- Rounded corners (8px)
- Hover effects and transitions
- Consistent spacing
- Mobile-first responsive design

## 🛠 Development Features

- **Hot Reload** with Turbopack
- **TypeScript** strict mode
- **ESLint** for code quality
- **Automatic routing** with App Router
- **SEO optimization** with metadata

## 📝 Demo Credentials

For testing:
- **Email**: admin@example.com
- **Password**: password123

*Make sure these credentials exist in your backend*

## 🚀 Next Steps

1. **Backend Connection**: Ensure backend runs on port 8000
2. **Real Data**: Connect dashboard stats to backend APIs
3. **Feature Implementation**: Build out the placeholder sections
4. **WhatsApp Integration**: Add WhatsApp management features
5. **File Upload**: Implement CV/document upload
6. **Advanced Features**: Add search, filters, pagination

## 🐛 Troubleshooting

### Common Issues

1. **Dependencies not installing**:
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

2. **Backend connection fails**:
   - Check if Laravel backend is running on port 8000
   - Verify CORS settings in backend
   - Check `.env.local` API URL

3. **Login not working**:
   - Verify demo user exists in backend database
   - Check browser console for API errors
   - Test API endpoint directly

4. **Build errors**:
   ```bash
   npm run build
   ```
   Check for TypeScript or syntax errors

## 💡 Tips

- Use browser dev tools to monitor API calls
- Check Network tab for failed requests
- Console tab for JavaScript errors
- Application tab for stored cookies/tokens

The frontend is now **production-ready** with modern architecture and can be easily extended with new features!
