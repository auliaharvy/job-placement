# Agent Link Management System - Backend Integration

Sistem backend telah diupdate untuk mendukung fitur agent link management dengan analytics lengkap.

## 🆕 Fitur Baru Backend:

### 1. **Database Migration**
- ✅ **agent_link_clicks table** - Tabel baru untuk tracking klik link
- ✅ **Indexes** - Optimasi performance untuk queries analytics
- ✅ **Foreign keys** - Relasi dengan agents table

### 2. **Models & Services**
- ✅ **AgentLinkClick Model** - Model dengan relationships dan scopes
- ✅ **Agent Model** - Updated dengan linkClicks relationship
- ✅ **AgentAnalyticsService** - Service untuk semua logic analytics

### 3. **API Controllers**
- ✅ **AgentController** - CRUD operations untuk agents
- ✅ **AgentAnalyticsController** - Analytics endpoints

### 4. **API Endpoints**

#### **Public Endpoints (No Auth Required):**
```bash
# Get all agents (for form dropdowns)
GET /api/v1/agents?paginate=false

# Get agent by referral code
GET /api/v1/agents/referral/{referralCode}

# Track link clicks
POST /api/v1/analytics/track-click
{
  "agent_id": "123",
  "referral_code": "ABC123",
  "utm_source": "facebook",
  "utm_medium": "social",
  "utm_campaign": "june_recruitment"
}

# Mark conversion
POST /api/v1/analytics/mark-conversion
{
  "session_id": "sess_123",
  "agent_id": "123"
}
```

#### **Protected Endpoints (Auth Required):**
```bash
# Get agent analytics
GET /api/v1/analytics/agents/{agentId}?start_date=2025-05-01&end_date=2025-06-01

# Get all agents analytics
GET /api/v1/analytics/agents

# Get dashboard summary
GET /api/v1/analytics/dashboard?period=month

# Mark conversion by click ID
POST /api/v1/analytics/clicks/{clickId}/convert
```

## 📊 **Analytics Data Structure**

### Agent Analytics Response:
```json
{
  "success": true,
  "data": {
    "agent": {
      "id": 1,
      "name": "John Doe",
      "agent_code": "AGT001",
      "referral_code": "JOHN001",
      "success_rate": 85.5,
      "successful_placements": 42,
      "total_referrals": 50
    },
    "period": {
      "start_date": "2025-05-01",
      "end_date": "2025-06-01",
      "days": 31
    },
    "totals": {
      "total_clicks": 150,
      "unique_clicks": 120,
      "converted_clicks": 18,
      "conversion_rate": 12.0
    },
    "sources": {
      "facebook": 60,
      "whatsapp": 40,
      "email": 30,
      "direct": 20
    },
    "mediums": {
      "social": 80,
      "referral": 40,
      "email": 30
    },
    "campaigns": {
      "june_recruitment": 70,
      "summer_jobs": 50,
      "tech_hiring": 30
    },
    "daily_clicks": {
      "2025-05-01": 5,
      "2025-05-02": 8,
      "...": "..."
    },
    "conversion_funnel": {
      "clicks": 150,
      "conversions": 18,
      "placements": 5,
      "click_to_conversion_rate": 12.0,
      "conversion_to_placement_rate": 27.8,
      "click_to_placement_rate": 3.3
    }
  }
}
```

## 🗄️ **Database Schema**

### agent_link_clicks Table:
```sql
CREATE TABLE agent_link_clicks (
  id BIGINT PRIMARY KEY,
  agent_id INT NOT NULL,
  referral_code VARCHAR(50),
  utm_source VARCHAR(100),
  utm_medium VARCHAR(100), 
  utm_campaign VARCHAR(100),
  user_agent TEXT,
  ip_address VARCHAR(45),
  session_id VARCHAR(255),
  browser_fingerprint VARCHAR(255),
  clicked_at TIMESTAMP NOT NULL,
  converted_at TIMESTAMP NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (agent_id) REFERENCES agents(id),
  INDEX idx_agent_clicked (agent_id, clicked_at),
  INDEX idx_referral_clicked (referral_code, clicked_at),
  INDEX idx_source_clicked (utm_source, clicked_at),
  INDEX idx_session_ip (session_id, ip_address)
);
```

## 🧪 **Test Data**

### Sample Agents Created:
1. **John Doe** (AGT001, JOHN001)
2. **Jane Smith** (AGT002, JANE002)  
3. **Michael Johnson** (AGT003, MICH003)

### Sample Analytics Data:
- **20-100 clicks** per agent over last 30 days
- **Multiple UTM sources**: facebook, instagram, whatsapp, email, linkedin, direct
- **Realistic conversion rates**: 10-20%
- **Time distribution**: Random hours and dates
- **Unique sessions**: Generated session IDs and IP addresses

## 🚀 **Setup Instructions**

### 1. Run Migration:
```bash
cd new-backend
php artisan migrate
```

### 2. Seed Test Data:
```bash
php artisan db:seed --class=AgentLinkClickSeeder
```

### 3. Start Backend:
```bash
php artisan serve
```

### 4. Test Endpoints:
```bash
# Test get agents
curl "http://localhost:8000/api/v1/agents?paginate=false"

# Test get agent by referral
curl "http://localhost:8000/api/v1/agents/referral/JOHN001"

# Test track click
curl -X POST "http://localhost:8000/api/v1/analytics/track-click" \
  -H "Content-Type: application/json" \
  -d '{
    "agent_id": "1",
    "utm_source": "facebook",
    "utm_medium": "social",
    "utm_campaign": "test"
  }'
```

## 🔄 **Frontend Integration**

### Updated Frontend Files:
- ✅ **agent.ts** - Updated API calls
- ✅ **useAgentAnalytics.ts** - Backend integration
- ✅ **agent-analytics-dashboard.tsx** - New data structure
- ✅ **useAgentAutoFill.ts** - Auto-tracking integration

### Frontend Features:
- **Auto-tracking** ketika link diklik
- **Real-time analytics** dari backend
- **Session management** untuk conversion tracking
- **Backup localStorage** untuk offline functionality

## 📈 **Analytics Features**

### 1. **Click Tracking**
- ✅ Automatic tracking ketika agent dipilih dari URL
- ✅ UTM parameter capture
- ✅ Session dan fingerprint tracking
- ✅ Unique visitor detection

### 2. **Conversion Tracking**
- ✅ Mark conversion by session
- ✅ Conversion funnel analysis
- ✅ Click-to-placement tracking
- ✅ Time-based conversion analysis

### 3. **Analytics Dashboard**
- ✅ Real-time metrics
- ✅ Source/medium/campaign breakdown
- ✅ Daily trends
- ✅ Performance comparison
- ✅ Conversion funnel visualization

### 4. **Export & Management**
- ✅ JSON export functionality
- ✅ Data clearing options
- ✅ Date range filtering
- ✅ UTM parameter filtering

## 🧩 **Integration Points**

### Frontend → Backend Flow:
1. **User clicks agent link** → `trackLinkClick()` → POST `/analytics/track-click`
2. **User submits form** → `markConversion()` → POST `/analytics/mark-conversion`
3. **Dashboard loads** → `getAgentAnalytics()` → GET `/analytics/agents/{id}`
4. **Admin views reports** → GET `/analytics/agents` (protected)

### Data Flow:
```
Agent Link Click → AgentLinkClick Model → AgentAnalyticsService → API Response → Frontend Dashboard
```

## 🔐 **Security Considerations**

### Public Endpoints:
- **Rate limiting** recommended untuk tracking endpoints
- **IP whitelisting** untuk admin endpoints
- **CORS** configuration untuk frontend domains

### Data Privacy:
- **IP address hashing** untuk GDPR compliance
- **User agent anonymization** options
- **Data retention** policies configurable

## 📊 **Performance Optimizations**

### Database:
- ✅ **Indexes** on frequently queried columns
- ✅ **Composite indexes** untuk complex queries
- ✅ **Foreign key constraints** untuk data integrity

### API:
- ✅ **Efficient queries** dengan relationships
- ✅ **Pagination** untuk large datasets
- ✅ **Caching** untuk frequently accessed data
- ✅ **Batch processing** untuk bulk operations

## 🎯 **Testing Scenarios**

### 1. **Basic Link Tracking**:
```bash
# Visit: http://localhost:3000/example-form?agent=1
# Expected: Agent auto-selected, click tracked
```

### 2. **UTM Tracking**:
```bash
# Visit: http://localhost:3000/example-form?agent=1&utm_source=facebook&utm_medium=social
# Expected: UTM parameters captured in analytics
```

### 3. **Conversion Tracking**:
```bash
# 1. Click agent link
# 2. Submit form
# Expected: Conversion marked for session
```

### 4. **Analytics Dashboard**:
```bash
# Visit: http://localhost:3000/agent-management
# Expected: Real analytics data from backend
```

## 🚀 **Production Deployment**

### Environment Variables:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job_placement_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

NEXT_PUBLIC_API_BASE_URL=https://your-api.com/api/v1
```

### Recommended Infrastructure:
- **Database**: MySQL 8.0+ atau PostgreSQL 13+
- **Cache**: Redis untuk session management
- **CDN**: CloudFlare untuk static assets
- **Monitoring**: Application insights untuk performance

Sistem sekarang siap untuk production dengan analytics lengkap! 🎉