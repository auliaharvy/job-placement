# Job Placement System Frontend

## 🚀 Quick Start

### Prerequisites
- Node.js 16+ 
- npm or yarn

### Installation & Setup

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Environment Configuration:**
   ```bash
   cp .env.example .env.local
   ```
   
   Edit `.env.local` with your configuration:
   ```env
   NEXT_PUBLIC_API_URL=http://localhost:8000/api
   NEXT_PUBLIC_WHATSAPP_API_URL=http://brevet.online:8005
   ```

3. **Start development server:**
   ```bash
   npm run dev
   # or use the provided script
   chmod +x start-frontend.sh
   ./start-frontend.sh
   ```

4. **Access the application:**
   - Frontend: http://localhost:3000
   - Login with demo credentials: `admin@example.com` / `password123`

## 📁 Project Structure

```
src/
├── components/          # Reusable components
│   ├── AppHeader.tsx   # Main header with user menu
│   └── AppSidebar.tsx  # Navigation sidebar
├── hooks/              # Custom React hooks
│   └── useAuth.ts      # Authentication hook
├── pages/              # Next.js pages
│   ├── _app.tsx        # App wrapper
│   ├── index.tsx       # Home page (redirects)
│   ├── login.tsx       # Login page
│   └── dashboard.tsx   # Main dashboard
├── services/           # API services
│   ├── api.ts          # Axios configuration
│   └── auth.ts         # Authentication service
└── styles/
    └── globals.css     # Global styles
```

## 🔧 Features Implemented

### ✅ Authentication
- Login page with form validation
- JWT token management
- Automatic token refresh
- Protected routes
- Logout functionality

### ✅ Dashboard Layout
- Responsive sidebar navigation
- Header with user menu
- Basic dashboard with statistics cards
- Menu routing structure

### ✅ UI Components
- Modern design with Ant Design
- Responsive layout
- Loading states
- Error handling
- Toast notifications

## 🛠 Development Features

- **TypeScript** for type safety
- **Ant Design** for UI components
- **Axios** for API communication
- **React Hooks** for state management
- **Next.js** for SSR and routing

## 📱 Pages Overview

### Login Page (`/login`)
- Email/password authentication
- Form validation
- Demo credentials display
- Redirect to dashboard on success

### Dashboard (`/dashboard`)
- Overview statistics
- Navigation menu
- Recent activities
- System status

### Menu Items (Ready for Implementation)
- Dashboard (implemented)
- Applicants (placeholder)
- Companies (placeholder)
- Job Postings (placeholder)
- WhatsApp (placeholder)
- Reports (placeholder)

## 🔐 Authentication Flow

1. User enters credentials on login page
2. Frontend sends POST request to `/api/auth/login`
3. Backend validates and returns JWT token
4. Token stored in localStorage
5. All subsequent requests include Authorization header
6. Automatic redirect to login if token invalid/expired

## 🌐 API Integration

The frontend is configured to connect to:
- **Backend API:** `http://localhost:8000/api`
- **WhatsApp API:** `http://brevet.online:8005`

All API calls go through the configured axios instance with:
- Automatic token injection
- Response interceptors
- Error handling
- Redirect on 401 errors

## 📝 Demo Credentials

For testing purposes:
- **Email:** admin@example.com
- **Password:** password123

*Note: These should match the seeded admin user in your backend*

## 🚀 Next Steps

1. **Backend Connection:** Ensure backend is running on port 8000
2. **User Seeding:** Create test users in backend database
3. **Feature Implementation:** Add actual functionality to menu items
4. **API Integration:** Connect dashboard statistics to real data
5. **WhatsApp Integration:** Implement WhatsApp management features

## 🐛 Troubleshooting

### Common Issues:

1. **Cannot connect to API:**
   - Check if backend is running on port 8000
   - Verify CORS settings in backend
   - Check network connectivity

2. **Login fails:**
   - Verify demo user exists in backend
   - Check API endpoint `/api/auth/login`
   - Review browser console for errors

3. **Build errors:**
   - Clear node_modules and reinstall
   - Check TypeScript errors
   - Verify all dependencies are installed

### Debug Mode:
Set `NEXT_PUBLIC_DEBUG=true` in .env for additional logging.

## 📞 Support

If you encounter issues:
1. Check the browser console for errors
2. Verify backend API is responding
3. Review network requests in browser dev tools
4. Check this README for troubleshooting steps
