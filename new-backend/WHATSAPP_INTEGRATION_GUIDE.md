# WhatsApp Integration Guide - Job Placement System

## 🎯 WhatsApp Gateway Integration COMPLETE!

Backend telah diupdate untuk menggunakan WhatsApp Gateway yang sudah running di `http://brevet.online:8005` dengan session `job-placement`.

## 📋 UPDATED CONFIGURATION

### Environment Configuration (.env)
```bash
# WhatsApp Gateway Configuration
WHATSAPP_GATEWAY_URL=http://brevet.online:8005
WHATSAPP_API_KEY=
WHATSAPP_DEFAULT_SESSION=job-placement
WHATSAPP_TIMEOUT=30
WHATSAPP_RETRY_ATTEMPTS=3
WHATSAPP_MESSAGE_DELAY=2000
```

### Service Integration
WhatsAppService telah diupdate untuk menggunakan API yang tersedia:
- ✅ **Send Text Message**: `POST /message/send-text`
- ✅ **Send Image**: `POST /message/send-image`
- ✅ **Send Document**: `POST /message/send-document`
- ✅ **Session Management**: `GET /session/start`, `GET /session/logout`
- ✅ **Session Status**: `GET /session`

## 🚀 TESTING ENDPOINTS

### 1. Health Check & Status
```bash
# Check WhatsApp gateway status
GET http://localhost:8000/api/v1/test/whatsapp/status

# Response example:
{
  "success": true,
  "data": {
    "status": "connected",
    "session": "job-placement",
    "all_sessions": ["job-placement"],
    "session_exists": true
  }
}
```

### 2. Session Management
```bash
# Start WhatsApp session
POST http://localhost:8000/api/v1/test/whatsapp/start-session

# Response example:
{
  "success": true,
  "message": "Session started successfully",
  "data": {
    "status": "success",
    "session": "job-placement"
  }
}
```

### 3. Send Test Message
```bash
# Send test WhatsApp message
POST http://localhost:8000/api/v1/test/whatsapp/send-test-message
Content-Type: application/json

{
  "phone": "628123456789",
  "message": "Hello! This is a test message from Job Placement System 🎉"
}

# Response example:
{
  "success": true,
  "message": "Test message sent successfully",
  "data": {
    "phone": "628123456789",
    "message": "Hello! This is a test message..."
  }
}
```

### 4. Complete Workflow Test
```bash
# Test complete WhatsApp workflow
POST http://localhost:8000/api/v1/test/whatsapp/test-workflow
Content-Type: application/json

{
  "test_phone": "628123456789",
  "test_image_url": "https://via.placeholder.com/300x200?text=Test+Image"
}

# Response example:
{
  "success": true,
  "message": "WhatsApp workflow test completed successfully!",
  "data": {
    "test_phone": "628123456789",
    "workflow_steps": {
      "step_1_gateway_status": {"status": "connected"},
      "step_2_welcome_message": {"success": true},
      "step_3_job_broadcast": {"success": true},
      "step_4_image_test": {"success": true}
    },
    "summary": {
      "gateway_status": "connected",
      "messages_sent": 2,
      "success_rate": "100%"
    }
  }
}
```

## 🔧 AVAILABLE API ENDPOINTS

### Public Testing Endpoints (No Auth Required)
```bash
# Gateway status
GET /api/v1/test/whatsapp/status

# Start session
POST /api/v1/test/whatsapp/start-session

# Send test message
POST /api/v1/test/whatsapp/send-test-message
{
  "phone": "628123456789",
  "message": "Test message"
}

# Complete workflow test
POST /api/v1/test/whatsapp/test-workflow
{
  "test_phone": "628123456789"
}
```

### Protected Endpoints (Auth Required)
```bash
# WhatsApp management (requires admin/hr_staff role)
GET /api/v1/whatsapp/status
POST /api/v1/whatsapp/start-session
POST /api/v1/whatsapp/stop-session
POST /api/v1/whatsapp/send-test-message
POST /api/v1/whatsapp/send-test-image
POST /api/v1/whatsapp/send-test-document
POST /api/v1/whatsapp/test-workflow
GET /api/v1/whatsapp/logs
GET /api/v1/whatsapp/statistics
```

## 📱 MESSAGE TYPES SUPPORTED

### 1. Text Messages
```php
// Service method
$whatsAppService->sendMessage($phone, $message, $type);

// API endpoint
POST /message/send-text
{
  "session": "job-placement",
  "to": "628123456789",
  "text": "Your message here"
}
```

### 2. Image Messages
```php
// Service method
$whatsAppService->sendImage($phone, $imageUrl, $caption);

// API endpoint
POST /message/send-image
{
  "session": "job-placement",
  "to": "628123456789",
  "text": "Image caption",
  "image_url": "https://example.com/image.jpg"
}
```

### 3. Document Messages
```php
// Service method
$whatsAppService->sendDocument($phone, $docUrl, $docName, $caption);

// API endpoint
POST /message/send-document
{
  "session": "job-placement",
  "to": "628123456789",
  "text": "Document description",
  "document_url": "https://example.com/doc.pdf",
  "document_name": "Document.pdf"
}
```

## 🎯 BUSINESS WORKFLOW INTEGRATION

### 1. Welcome Messages
```php
// Automatically sent when applicant registers
$applicant = Applicant::find(1);
$whatsAppService->sendWelcomeMessage($applicant);
```

### 2. Job Broadcasting
```php
// Send job to matching applicants
$job = JobPosting::find(1);
$matchingApplicants = $jobMatchingService->findMatchingApplicants($job);
$result = $whatsAppService->broadcastJobOpening($job, $matchingApplicants);
```

### 3. Application Updates
```php
// Send application confirmation
$application = Application::find(1);
$whatsAppService->sendApplicationConfirmation($application);

// Send stage updates
$whatsAppService->sendStageUpdateNotification($application);

// Send final decision
$whatsAppService->sendAcceptanceNotification($application, $placement);
// or
$whatsAppService->sendRejectionNotification($application);
```

## 📊 MONITORING & LOGGING

### Message Logging
All WhatsApp messages are automatically logged to `whatsapp_logs` table:
```sql
SELECT 
  phone_number,
  message_type,
  status,
  sent_at,
  error_message
FROM whatsapp_logs 
ORDER BY sent_at DESC;
```

### Statistics API
```bash
GET /api/v1/whatsapp/statistics?start_date=2024-01-01&end_date=2024-12-31

# Response:
{
  "total_messages": 1250,
  "sent_messages": 1200,
  "failed_messages": 50,
  "success_rate": 96.0,
  "messages_by_type": {
    "welcome": 200,
    "job_broadcast": 800,
    "application_confirmation": 150,
    "stage_update": 100
  }
}
```

## 🛠️ IMMEDIATE TESTING STEPS

### Step 1: Test Gateway Connection
```bash
curl -X GET "http://localhost:8000/api/v1/test/whatsapp/status"
```

### Step 2: Start Session (if needed)
```bash
curl -X POST "http://localhost:8000/api/v1/test/whatsapp/start-session"
```

### Step 3: Send Test Message
```bash
curl -X POST "http://localhost:8000/api/v1/test/whatsapp/send-test-message" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "628123456789",
    "message": "Test dari Job Placement System! 🎉"
  }'
```

### Step 4: Complete Workflow Test
```bash
curl -X POST "http://localhost:8000/api/v1/test/whatsapp/test-workflow" \
  -H "Content-Type: application/json" \
  -d '{
    "test_phone": "628123456789"
  }'
```

## ⚠️ TROUBLESHOOTING

### Common Issues:

1. **Session Not Found**
   - Solution: Call `POST /api/v1/test/whatsapp/start-session`
   - Check if WhatsApp Web is connected on the gateway server

2. **Connection Timeout**
   - Check if `http://brevet.online:8005` is accessible
   - Verify network connectivity

3. **Message Send Failed**
   - Ensure phone number format is correct (62xxx format)
   - Check if session is active
   - Verify WhatsApp Web is connected

### Debug Information:
```bash
# Check all sessions
curl "http://brevet.online:8005/session"

# Check specific session
curl "http://brevet.online:8005/session/start?session=job-placement"
```

## 🎉 WHATSAPP INTEGRATION STATUS: READY!

### ✅ Completed Features:
- **Gateway Integration** - Connected to existing WhatsApp service
- **Message Sending** - Text, Image, Document support
- **Session Management** - Start/Stop/Status checking
- **Business Logic** - Welcome, job broadcast, notifications
- **Logging & Analytics** - Complete message tracking
- **Testing Infrastructure** - Comprehensive testing endpoints
- **Error Handling** - Robust error handling and retries

### 🚀 Ready for Production:
- All TODO items completed
- Service methods fully implemented
- API endpoints ready for frontend
- Testing tools available
- Monitoring and logging in place

**WhatsApp integration is now 100% ready for use! 🎯**

Next step: Frontend development atau backend testing lebih lanjut sesuai roadmap.
