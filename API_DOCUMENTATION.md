# Event Management System API Documentation

## Base URL
```
http://ems-web.test/api
```

## Authentication
All API endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## Response Format
All responses follow this format:
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {...}
}
```

## Events API

### Get All Events
```
GET /api/events
```

**Query Parameters:**
- `search` (string): Search by title, description, or venue
- `status` (string): Filter by status (pending_approvals, approved, published, completed, cancelled)
- `start_date` (date): Filter events starting from this date
- `end_date` (date): Filter events ending before this date
- `per_page` (integer): Number of results per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Annual Tech Conference",
                "description": "Technology conference...",
                "start_at": "2024-02-20T09:00:00.000000Z",
                "end_at": "2024-02-20T17:00:00.000000Z",
                "formatted_start_at": "Feb 20, 2024 09:00 AM",
                "formatted_end_at": "Feb 20, 2024 05:00 PM",
                "venue": {
                    "id": 1,
                    "name": "Main Auditorium",
                    "address": "123 Main St",
                    "capacity": 500
                },
                "status": "published",
                "formatted_status": "Published",
                "number_of_participants": 300,
                "is_upcoming": true,
                "is_ongoing": false,
                "is_past": false
            }
        ],
        "current_page": 1,
        "total": 10
    }
}
```

### Get Specific Event
```
GET /api/events/{id}
```

### Create Event
```
POST /api/events
```

**Request Body:**
```json
{
    "title": "New Event",
    "description": "Event description",
    "start_at": "2024-02-25T09:00:00Z",
    "end_at": "2024-02-25T17:00:00Z",
    "venue_id": 1,
    "number_of_participants": 100,
    "notes": "Optional notes"
}
```

### Update Event
```
PUT /api/events/{id}
```

### Update Event Status (Admin Only)
```
PATCH /api/events/{id}/status
```

**Request Body:**
```json
{
    "status": "approved",
    "notes": "Approved for publication"
}
```

### Delete Event
```
DELETE /api/events/{id}
```

### Get My Events
```
GET /api/events/my
```

### Get Event Statistics (Admin Only)
```
GET /api/events/statistics
```

### Get Available Venues
```
GET /api/events/venues
```

### Add Participant to Event
```
POST /api/events/{id}/participants
```

**Request Body:**
```json
{
    "employee_id": 1,
    "type": "participant",
    "role": "Attendee"
}
```

### Remove Participant from Event
```
DELETE /api/events/{id}/participants/{participantId}
```

## Logistics API

### Get Event Logistics
```
GET /api/events/{eventId}/logistics
```

### Create Logistics Item
```
POST /api/events/{eventId}/logistics
```

**Request Body:**
```json
{
    "resource_id": 1,
    "description": "Projector for presentation",
    "quantity": 2,
    "unit_price": 2500.00,
    "notes": "Optional notes"
}
```

### Update Logistics Item
```
PUT /api/events/{eventId}/logistics/{logisticsId}
```

### Delete Logistics Item
```
DELETE /api/events/{eventId}/logistics/{logisticsId}
```

### Get Logistics Summary
```
GET /api/events/{eventId}/logistics/summary
```

### Get Available Resources
```
GET /api/logistics/resources
```

## Finance API

### Get Event Finance Request
```
GET /api/events/{eventId}/finance
```

### Create/Update Finance Request
```
POST /api/events/{eventId}/finance
```

**Request Body:**
```json
{
    "logistics_total": 5000.00,
    "equipment_total": 2000.00,
    "other_total": 1000.00,
    "notes": "Budget breakdown details"
}
```

### Update Finance Request Status (Admin Only)
```
PATCH /api/events/{eventId}/finance/{financeRequestId}/status
```

**Request Body:**
```json
{
    "status": "approved",
    "notes": "Budget approved for event"
}
```

### Get Pending Finance Requests (Admin Only)
```
GET /api/finance/pending
```

### Get My Finance Requests
```
GET /api/finance/my
```

### Get Finance Dashboard (Admin Only)
```
GET /api/finance/dashboard
```

## Custodian API

### Get Event Custodian Requests
```
GET /api/events/{eventId}/custodian
```

### Create Custodian Request
```
POST /api/events/{eventId}/custodian
```

**Request Body:**
```json
{
    "custodian_material_id": 1,
    "quantity": 40,
    "notes": "Chairs for attendees"
}
```

### Update Custodian Request
```
PUT /api/events/{eventId}/custodian/{custodianRequestId}
```

### Update Custodian Request Status (Admin/Custodian Only)
```
PATCH /api/events/{eventId}/custodian/{custodianRequestId}/status
```

**Request Body:**
```json
{
    "status": "approved",
    "notes": "Materials available for event"
}
```

### Delete Custodian Request
```
DELETE /api/events/{eventId}/custodian/{custodianRequestId}
```

### Get Custodian Summary
```
GET /api/events/{eventId}/custodian/summary
```

### Get Available Materials
```
GET /api/custodian/materials
```

### Get Pending Custodian Requests (Admin/Custodian Only)
```
GET /api/custodian/pending
```

### Get My Custodian Requests
```
GET /api/custodian/my
```

### Get Custodian Dashboard (Admin/Custodian Only)
```
GET /api/custodian/dashboard
```

## Error Responses

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Resource not found"
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "title": ["The title field is required."],
        "start_at": ["The start at must be a date after now."]
    }
}
```

## Status Values

### Event Statuses
- `pending_approvals`: Event is waiting for approval
- `approved`: Event has been approved but not published
- `published`: Event is visible to all users
- `completed`: Event has finished
- `cancelled`: Event was cancelled

### Finance Request Statuses
- `pending`: Waiting for approval
- `approved`: Budget has been approved
- `rejected`: Budget was rejected

### Custodian Request Statuses
- `pending`: Waiting for approval
- `approved`: Materials have been approved
- `rejected`: Request was rejected

## Participant Types
- `participant`: Regular event attendee
- `committee`: Event committee member
- `speaker`: Event speaker or presenter
