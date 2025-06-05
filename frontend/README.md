# Job Placement System - Frontend

Frontend aplikasi Job Placement System menggunakan Next.js, TypeScript, dan Ant Design.

## Teknologi yang Digunakan

- **Next.js 14** - React framework untuk production
- **TypeScript** - Type-safe JavaScript
- **Ant Design** - UI component library
- **React Query** - Data fetching dan state management
- **Recharts** - Chart library untuk dashboard
- **Axios** - HTTP client
- **Day.js** - Date manipulation library

## Fitur Utama

### Dashboard
- Overview statistik sistem
- Charts dan analytics
- Recent activities
- System alerts

### Manajemen Pelamar
- CRUD pelamar
- Upload CV/Resume
- Filter dan pencarian
- Export data
- WhatsApp integration

### Manajemen Lowongan
- CRUD lowongan kerja
- Job posting management
- Application tracking
- Status management

### Manajemen Lamaran
- Pipeline seleksi
- Status tracking
- Review process
- Bulk operations

### Integrasi WhatsApp
- Send messages
- Broadcast messages
- Delivery tracking
- Template messages

## Instalasi dan Setup

### Prerequisites
- Node.js 16+ 
- npm atau yarn

### Langkah Instalasi

1. **Install dependencies**
   ```bash
   cd frontend
   npm install
   ```

2. **Setup environment variables**
   ```bash
   cp .env.example .env.local
   ```
   
   Edit `.env.local` sesuai konfigurasi:
   ```env
   NEXT_PUBLIC_API_URL=http://localhost:3001/api
   NEXT_PUBLIC_WHATSAPP_API_URL=http://localhost:3002
   ```

3. **Run development server**
   ```bash
   npm run dev
   ```

4. **Build untuk production**
   ```bash
   npm run build
   npm start
   ```

## Struktur Proyek

```
src/
├── components/           # Reusable components
│   ├── AdminLayout.tsx  # Main layout component
│   └── Layout/          # Layout components
├── pages/               # Next.js pages
│   ├── _app.tsx        # App wrapper
│   ├── _document.tsx   # Document template
│   ├── index.tsx       # Home page
│   ├── dashboard.tsx   # Dashboard page
│   ├── login.tsx       # Login page
│   ├── applicants/     # Applicant management
│   └── jobs/           # Job management
├── services/           # API services
│   ├── api.ts         # API client
│   └── hooks.ts       # React Query hooks
├── utils/             # Utility functions
│   └── helpers.ts     # Helper functions
└── styles/           # Global styles
    └── globals.css   # Global CSS
```

## Konfigurasi

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `NEXT_PUBLIC_API_URL` | Backend API URL | `http://localhost:3001/api` |
| `NEXT_PUBLIC_WHATSAPP_API_URL` | WhatsApp Gateway URL | `http://localhost:3002` |
| `NEXT_PUBLIC_MAX_FILE_SIZE` | Max file upload size | `5242880` (5MB) |
| `NEXT_PUBLIC_ALLOWED_FILE_TYPES` | Allowed file types | `pdf,doc,docx,jpg,jpeg,png` |

### Theme Configuration

Aplikasi menggunakan Ant Design theme yang dapat dikustomisasi di `_app.tsx`:

```typescript
const theme = {
  token: {
    colorPrimary: '#1890ff',
    colorSuccess: '#52c41a',
    colorWarning: '#faad14',
    colorError: '#ff4d4f',
    borderRadius: 6,
  },
};
```

## Routing

### Public Routes
- `/login` - Login page

### Protected Routes (require authentication)
- `/dashboard` - Main dashboard
- `/applicants` - Applicant management
- `/jobs` - Job management
- `/applications` - Application management
- `/placements` - Placement management
- `/companies` - Company management
- `/agents` - Agent management
- `/whatsapp` - WhatsApp management
- `/analytics` - Analytics and reports

## API Integration

### Authentication
- JWT token based authentication
- Automatic token refresh
- Role-based access control

### Error Handling
- Global error interceptor
- User-friendly error messages
- Automatic retry for failed requests

### Data Fetching
- React Query untuk caching dan synchronization
- Optimistic updates
- Background refetching

## Komponen Utama

### AdminLayout
Main layout component dengan:
- Sidebar navigation
- Header dengan user menu
- Responsive design
- Role-based menu items

### Dashboard
- Real-time statistics
- Interactive charts
- Activity feed
- System alerts

### Data Tables
- Server-side pagination
- Advanced filtering
- Export functionality
- Bulk operations

## Development Guidelines

### Code Style
- TypeScript strict mode
- ESLint untuk code quality
- Consistent naming conventions
- Component-based architecture

### State Management
- React Query untuk server state
- Local state dengan useState
- Form state dengan react-hook-form

### Error Handling
- Try-catch blocks
- User feedback dengan messages
- Graceful degradation

## Testing

```bash
# Run tests
npm test

# Run tests in watch mode
npm run test:watch

# Generate coverage report
npm run test:coverage
```

## Build dan Deployment

### Development Build
```bash
npm run dev
```

### Production Build
```bash
npm run build
npm start
```

### Docker Build
```bash
docker build -t job-placement-frontend .
docker run -p 3000:3000 job-placement-frontend
```

## Performance Optimization

- Next.js automatic code splitting
- Image optimization
- Bundle analyzer
- Lazy loading components
- Memoization untuk expensive operations

## Security

- XSS protection
- CSRF protection
- Secure headers
- Input validation
- Authentication guards

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Troubleshooting

### Common Issues

1. **Build errors**
   ```bash
   rm -rf .next node_modules
   npm install
   npm run build
   ```

2. **TypeScript errors**
   ```bash
   npm run type-check
   ```

3. **Styling issues**
   - Check Ant Design version compatibility
   - Verify CSS imports order

## Contributing

1. Fork repository
2. Create feature branch
3. Follow coding standards
4. Add tests untuk new features
5. Submit pull request

## License

MIT License - see LICENSE file for details
