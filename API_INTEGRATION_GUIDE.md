# API Integration Guide

## 🚀 Complete Step-by-Step Integration Guide

This guide will help external developers connect their applications to the Event Management System API.

### 📋 Table of Contents
1. [Getting Started](#getting-started)
2. [Authentication](#authentication)
3. [Making Your First Request](#making-your-first-request)
4. [Common Use Cases](#common-use-cases)
5. [Error Handling](#error-handling)
6. [Webhooks](#webhooks)
7. [SDKs and Libraries](#sdks-and-libraries)
8. [Best Practices](#best-practices)
9. [Troubleshooting](#troubleshooting)

---

## 🎯 Getting Started

### Step 1: Access the Developer Portal
Visit: `http://ems-web.test/api/developer`

This portal provides:
- Interactive API documentation
- Code examples in multiple languages
- Registration and token generation
- Real-time testing tools

### Step 2: Register Your Application
1. Click "Register Now" on the developer portal
2. Fill in your application details:
   ```json
   {
     "app_name": "My Event App",
     "description": "Application for event management",
     "contact_email": "developer@example.com",
     "website": "https://myapp.com"
   }
   ```
3. Save your `app_id` and `app_secret` securely

### Step 3: Generate API Token
1. Click "Generate Token" on the developer portal
2. Enter your `app_id` and `app_secret`
3. Receive your access token:
   ```json
   {
     "access_token": "ems_abc123...",
     "token_type": "Bearer",
     "expires_in": 31536000,
     "scope": "read write"
   }
   ```

---

## 🔐 Authentication

### Using Bearer Token
Include your token in the Authorization header:
```http
Authorization: Bearer ems_abc123...
Content-Type: application/json
Accept: application/json
```

### Example in Different Languages

#### JavaScript
```javascript
const headers = {
  'Authorization': 'Bearer YOUR_TOKEN_HERE',
  'Content-Type': 'application/json'
};

fetch('http://ems-web.test/api/events', { headers })
  .then(response => response.json())
  .then(data => console.log(data));
```

#### PHP
```php
$client = new GuzzleHttp\Client();
$response = $client->get('http://ems-web.test/api/events', [
    'headers' => [
        'Authorization' => 'Bearer YOUR_TOKEN_HERE',
        'Content-Type' => 'application/json'
    ]
]);
```

#### Python
```python
import requests

headers = {
    'Authorization': 'Bearer YOUR_TOKEN_HERE',
    'Content-Type': 'application/json'
}

response = requests.get('http://ems-web.test/api/events', headers=headers)
```

---

## 📡 Making Your First Request

### Fetch All Events
```http
GET /api/events
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Annual Tech Conference",
        "status": "published",
        "start_at": "2024-03-01T09:00:00.000000Z",
        "end_at": "2024-03-01T17:00:00.000000Z",
        "venue": {
          "id": 1,
          "name": "Main Auditorium"
        }
      }
    ]
  }
}
```

### Create a New Event
```http
POST /api/events
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "title": "My API Event",
  "description": "Created via API",
  "start_at": "2024-03-01T09:00:00Z",
  "end_at": "2024-03-01T17:00:00Z",
  "venue_id": 1,
  "number_of_participants": 50
}
```

---

## 💼 Common Use Cases

### 1. Event Management Application
```javascript
// Get all events
const getEvents = async () => {
  const response = await fetch('/api/events', {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  return await response.json();
};

// Create new event
const createEvent = async (eventData) => {
  const response = await fetch('/api/events', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(eventData)
  });
  return await response.json();
};

// Update event status
const updateEventStatus = async (eventId, status) => {
  const response = await fetch(`/api/events/${eventId}/status`, {
    method: 'PATCH',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ status })
  });
  return await response.json();
};
```

### 2. Financial Management System
```javascript
// Get finance request for event
const getFinanceRequest = async (eventId) => {
  const response = await fetch(`/api/events/${eventId}/finance`, {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  return await response.json();
};

// Create finance request
const createFinanceRequest = async (eventId, financeData) => {
  const response = await fetch(`/api/events/${eventId}/finance`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(financeData)
  });
  return await response.json();
};
```

### 3. Logistics Management
```javascript
// Get logistics for event
const getLogistics = async (eventId) => {
  const response = await fetch(`/api/events/${eventId}/logistics`, {
    headers: { 'Authorization': `Bearer ${token}` }
  });
  return await response.json();
};

// Add logistics item
const addLogisticsItem = async (eventId, itemData) => {
  const response = await fetch(`/api/events/${eventId}/logistics`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(itemData)
  });
  return await response.json();
};
```

---

## ⚠️ Error Handling

### Standard Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message for this field"]
  }
}
```

### Common HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Error Handling Example
```javascript
const handleApiCall = async (apiCall) => {
  try {
    const response = await apiCall();
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'API request failed');
    }
    
    return await response.json();
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};
```

---

## 🪝 Webhooks

### Setting Up Webhooks
1. Register your webhook URL: `POST /api/integration/webhooks`
2. Select events you want to receive
3. Verify your endpoint

### Webhook Events
- `event.created` - New event created
- `event.updated` - Event updated
- `event.status_changed` - Event status changed
- `finance.requested` - Finance request created
- `finance.approved` - Finance request approved
- `custodian.requested` - Custodian request created

### Webhook Payload Example
```json
{
  "event": "event.created",
  "data": {
    "id": 123,
    "title": "New Event",
    "status": "pending_approvals"
  },
  "timestamp": "2024-02-14T11:53:00Z"
}
```

### Webhook Handler Example
```javascript
// Express.js webhook handler
app.post('/webhook', (req, res) => {
  const signature = req.headers['x-ems-signature'];
  
  // Verify signature (implement your verification logic)
  if (!verifySignature(signature, req.body)) {
    return res.status(401).send('Unauthorized');
  }
  
  const { event, data } = req.body;
  
  switch (event) {
    case 'event.created':
      handleNewEvent(data);
      break;
    case 'event.status_changed':
      handleStatusChange(data);
      break;
    // Handle other events...
  }
  
  res.status(200).send('OK');
});
```

---

## 📚 SDKs and Libraries

### Official SDKs

#### JavaScript/Node.js
```bash
npm install @ems/api-client
```
```javascript
import EMSClient from '@ems/api-client';

const client = new EMSClient('YOUR_TOKEN');
const events = await client.events.list();
```

#### PHP
```bash
composer require ems/api-client
```
```php
use EMS\API\Client;

$client = new Client('YOUR_TOKEN');
$events = $client->events()->list();
```

#### Python
```bash
pip install ems-api-client
```
```python
from ems_api import EMSClient

client = EMSClient('YOUR_TOKEN')
events = client.events.list()
```

---

## ✅ Best Practices

### 1. Security
- Never expose your API token in client-side code
- Use environment variables for sensitive data
- Implement proper error handling
- Validate webhook signatures

### 2. Performance
- Use pagination for large datasets
- Implement caching for frequently accessed data
- Use appropriate HTTP methods (GET for reading, POST for creating)
- Handle rate limits gracefully

### 3. Reliability
- Implement retry logic for failed requests
- Use exponential backoff for retries
- Log API requests and responses for debugging
- Monitor your API usage

### 4. Data Management
- Always validate API responses
- Handle missing or null fields gracefully
- Use appropriate data types in your application
- Keep your local data synchronized

---

## 🔧 Troubleshooting

### Common Issues

#### 1. Authentication Errors
**Problem**: 401 Unauthorized
**Solution**: 
- Check your API token is correct
- Ensure token hasn't expired
- Verify token is included in Authorization header

#### 2. Rate Limiting
**Problem**: 429 Too Many Requests
**Solution**:
- Implement request throttling
- Use caching to reduce API calls
- Monitor your usage statistics

#### 3. Validation Errors
**Problem**: 422 Unprocessable Entity
**Solution**:
- Check request body format
- Ensure all required fields are included
- Validate data types and formats

#### 4. Network Issues
**Problem**: Connection timeout or network errors
**Solution**:
- Implement retry logic
- Check network connectivity
- Use appropriate timeout values

### Debug Mode
Add `?debug=true` to any API request to get detailed information:
```http
GET /api/events?debug=true
```

### Getting Help
- **Documentation**: `/API_DOCUMENTATION.md`
- **Developer Portal**: `/api/developer`
- **Support**: `api-support@ems-web.test`
- **Status Page**: `https://status.ems-web.test`

---

## 🎉 You're Ready!

You now have everything you need to integrate with the Event Management System API. Start building your application and join our developer community!

### Quick Links
- [Developer Portal](http://ems-web.test/api/developer)
- [API Documentation](/API_DOCUMENTATION.md)
- [Code Examples](http://ems-web.test/api/integration/examples)
- [Support & Community](http://ems-web.test/api/integration/support)

Happy coding! 🚀
