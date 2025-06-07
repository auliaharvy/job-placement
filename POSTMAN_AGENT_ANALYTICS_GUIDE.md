# 📮 Postman Collection - Agent Analytics API

Complete Postman collection untuk testing Agent Link Management dan Analytics System.

## 📦 **Files Created:**
- ✅ `Job-Placement-Agent-Analytics.postman_collection.json` - Main collection
- ✅ `Job-Placement-Agent-Analytics.postman_environment.json` - Environment variables

## 🚀 **Quick Setup**

### 1. **Import Collections**
```bash
# Import ke Postman:
1. Open Postman
2. Click "Import" 
3. Drag & drop kedua file JSON
4. Select environment: "Job Placement - Agent Analytics Environment"
```

### 2. **Start Backend Server**
```bash
cd new-backend
php artisan serve
# Server running at: http://localhost:8000
```

### 3. **Run Authentication**
```bash
# First, run "Login" request to get auth token
# Token will be automatically saved to environment
```

## 📋 **Collection Structure**

### 🚀 **Authentication**
- **Login** - Get auth token (auto-saves to environment)
- **Get Profile** - Verify authentication

### 👥 **Agents (Public)**
- **Get All Agents** - List semua agents (for dropdown)
- **Get All Agents (Paginated)** - With pagination & sorting
- **Get Agent by Referral Code** - Find agent by referral code
- **Get Agent by ID** - Get specific agent

### 📊 **Analytics (Public)**
- **Track Link Click** - Track agent link clicks
- **Track Click (Agent ID Only)** - Minimal tracking
- **Track Click (Referral Code Only)** - Track by referral code
- **Mark Conversion** - Mark session as converted

### 📈 **Analytics (Protected)**
- **Get Agent Analytics** - Comprehensive agent analytics
- **Get Agent Analytics (Last 7 Days)** - Recent analytics
- **Get Agent Analytics (Facebook Only)** - Filtered by source
- **Get All Agents Analytics** - Analytics for all agents
- **Get Dashboard Summary** - Dashboard overview
- **Mark Conversion by Click ID** - Admin conversion marking

### 🧪 **Testing Scenarios**
- **Test Complete Flow** - End-to-end testing (4 steps)
- **Bulk Click Testing** - Multiple traffic sources

### 🔧 **Utilities**
- **Health Check** - API status check
- **Generate Test Data** - Create additional test data

## 🎯 **Key Testing Flows**

### **Flow 1: Basic Agent Lookup**
```bash
1. Run: "Get Agent by Referral Code" (JOHN001)
2. Verify: Agent data returned
3. Note: agent_id for next tests
```

### **Flow 2: Link Tracking**
```bash
1. Run: "Track Link Click" 
2. Check: Response 201 with click data
3. Run: "Mark Conversion"
4. Check: Response 200 success
```

### **Flow 3: Analytics Retrieval**
```bash
1. Login first to get auth token
2. Run: "Get Agent Analytics" 
3. Check: Comprehensive analytics data
4. Verify: totals, sources, mediums, daily_clicks
```

### **Flow 4: Complete End-to-End Test**
```bash
1. Run entire "Test Complete Flow" folder
2. Each step auto-feeds data to next step
3. Validates: Agent lookup → Click tracking → Conversion → Analytics
```

## 📊 **Sample Responses**

### **Get Agent by Referral Code:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "agent_code": "AGT001",
    "referral_code": "JOHN001",
    "user": {
      "full_name": "John Doe",
      "email": "john.agent@example.com"
    },
    "success_rate": "85.5",
    "successful_placements": 42
  }
}
```

### **Track Link Click:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "agent_id": 1,
    "utm_source": "facebook",
    "utm_medium": "social",
    "clicked_at": "2025-06-06T10:30:00Z"
  }
}
```

### **Agent Analytics:**
```json
{
  "success": true,
  "data": {
    "agent": {
      "id": 1,
      "name": "John Doe",
      "agent_code": "AGT001"
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
      "email": 30
    },
    "conversion_funnel": {
      "clicks": 150,
      "conversions": 18,
      "placements": 5,
      "click_to_placement_rate": 3.3
    }
  }
}
```

## 🔧 **Environment Variables**

### **Auto-Generated:**
- `current_date` - Today's date
- `start_date_7_days_ago` - 7 days ago
- `start_date_30_days_ago` - 30 days ago
- `auth_token` - From login (auto-saved)
- `test_session_id` - Unique session ID
- `test_agent_id` - From test flow
- `test_referral_code` - From test flow

### **Pre-Configured:**
- `base_url` - http://localhost:8000/api/v1
- `agent_id` - 1 (default test agent)
- `referral_code` - JOHN001 (default)
- `click_id` - 1 (default)

## 🧪 **Advanced Testing**

### **1. Bulk Traffic Source Testing:**
```bash
# Run "Bulk Click Testing" folder
# Tests: Facebook, Instagram, WhatsApp, Email
# Each with different UTM parameters
```

### **2. Date Range Analytics:**
```bash
# Modify dates in "Get Agent Analytics":
# ?start_date=2025-05-01&end_date=2025-06-01
```

### **3. UTM Filtering:**
```bash
# Test filtered analytics:
# ?utm_source=facebook
# ?utm_medium=social  
# ?utm_campaign=june_recruitment
```

### **4. Dashboard Periods:**
```bash
# Test different periods:
# ?period=today
# ?period=week
# ?period=month
# ?period=quarter
# ?period=year
```

## 🎯 **Test Scenarios**

### **Scenario 1: Social Media Campaign**
```bash
1. Track clicks from Facebook, Instagram
2. Mark some as conversions
3. Get analytics filtered by social medium
4. Verify conversion funnel
```

### **Scenario 2: Email Marketing**
```bash
1. Track clicks from email newsletter
2. Test different campaigns
3. Get analytics filtered by email source
4. Compare conversion rates
```

### **Scenario 3: WhatsApp Referrals**
```bash
1. Track clicks from WhatsApp sharing
2. Test referral medium
3. Get analytics for referral traffic
4. Verify referral effectiveness
```

## 🔍 **Debugging Tips**

### **Common Issues:**

**401 Unauthorized:**
```bash
# Solution: Run "Login" request first
# Check: auth_token in environment
```

**404 Not Found:**
```bash
# Check: Backend server running (php artisan serve)
# Check: Correct base_url in environment
```

**422 Validation Error:**
```bash
# Check: Required fields in request body
# Check: agent_id exists in database
```

**500 Internal Server Error:**
```bash
# Check: Database connection
# Check: Migration completed
# Check: Seeder ran successfully
```

### **Verification Steps:**
```bash
1. Health Check - API is running
2. Login - Authentication working
3. Get Agents - Database populated
4. Track Click - Analytics working
5. Get Analytics - Data processing
```

## 📈 **Performance Testing**

### **Load Testing:**
```bash
# Use Postman Collection Runner:
1. Select "Bulk Click Testing" folder
2. Set iterations: 10-100
3. Set delay: 100ms
4. Monitor response times
```

### **Stress Testing:**
```bash
# Test high volume clicks:
1. Run "Track Link Click" multiple times
2. Vary UTM parameters
3. Check database performance
4. Monitor memory usage
```

## 🚀 **Production Setup**

### **Environment for Production:**
```json
{
  "base_url": "https://your-api-domain.com/api/v1",
  "auth_token": "your-production-token"
}
```

### **Security Testing:**
```bash
# Test protected endpoints without auth
# Test with expired tokens
# Test with invalid agent IDs
# Test SQL injection in parameters
```

## 📝 **Collection Features**

### **Auto-Tests:**
- ✅ Response time validation (< 5 seconds)
- ✅ JSON format validation
- ✅ Status code verification
- ✅ Data structure validation

### **Auto-Variables:**
- ✅ Dynamic date generation
- ✅ Session ID generation
- ✅ Token auto-save from login
- ✅ Agent data auto-extraction

### **Test Coverage:**
- ✅ All public endpoints (no auth)
- ✅ All protected endpoints (with auth)
- ✅ Error scenarios (404, 422, 500)
- ✅ Edge cases (missing data, invalid IDs)

Ready untuk comprehensive testing! 🎯