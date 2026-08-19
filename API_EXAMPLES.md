# API Testing Examples

## Prerequisites
- Application running at http://127.0.0.1:8000
- Database seeded with demo users

## Tools You Can Use
- **Postman** (Recommended)
- **Insomnia**
- **Thunder Client** (VS Code extension)
- **cURL** (Command line)

---

## 1. Authentication

### Login (Get Token)

**cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@example.com\",\"password\":\"admin123\"}"
```

**PowerShell:**
```powershell
$body = @{
    email = "admin@example.com"
    password = "admin123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method Post -Body $body -ContentType "application/json"
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "role": "admin"
        },
        "token": "1|abcdef123456..."
    }
}
```

**⚠️ Save the token for subsequent requests!**

---

## 2. Get Current User

**cURL:**
```bash
curl -X GET http://127.0.0.1:8000/api/user ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/user" -Method Get -Headers $headers
```

---

## 3. List All Tickets

**cURL:**
```bash
curl -X GET http://127.0.0.1:8000/api/tickets ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**With Filters:**
```bash
curl -X GET "http://127.0.0.1:8000/api/tickets?status=open&priority=high&sort_by=created_at&sort_order=desc" ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets" -Method Get -Headers $headers
```

---

## 4. Get Single Ticket

**cURL:**
```bash
curl -X GET http://127.0.0.1:8000/api/tickets/1 ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets/1" -Method Get -Headers $headers
```

---

## 5. Create Ticket

### Basic Ticket

**cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/tickets ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -H "Content-Type: application/json" ^
  -d "{\"title\":\"API Test Ticket\",\"description\":\"Testing ticket creation via API\",\"priority\":\"high\"}"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

$body = @{
    title = "API Test Ticket"
    description = "Testing ticket creation via API"
    priority = "high"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets" -Method Post -Headers $headers -Body $body
```

### Ticket with Initial Comment

**cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/tickets ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -H "Content-Type: application/json" ^
  -d "{\"title\":\"System Error\",\"description\":\"500 error on dashboard\",\"priority\":\"urgent\",\"initial_comment\":\"Needs immediate investigation\"}"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

$body = @{
    title = "System Error"
    description = "500 error on dashboard"
    priority = "urgent"
    initial_comment = "Needs immediate investigation"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets" -Method Post -Headers $headers -Body $body
```

### Ticket with Assignment

**cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/tickets ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -H "Content-Type: application/json" ^
  -d "{\"title\":\"Bug Report\",\"description\":\"UI issue on login page\",\"priority\":\"medium\",\"assigned_to\":2}"
```

---

## 6. Update Ticket

**cURL:**
```bash
curl -X PUT http://127.0.0.1:8000/api/tickets/1 ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -H "Content-Type: application/json" ^
  -d "{\"status\":\"in_progress\",\"priority\":\"urgent\"}"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

$body = @{
    status = "in_progress"
    priority = "urgent"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets/1" -Method Put -Headers $headers -Body $body
```

---

## 7. Delete Ticket (Admin Only)

**cURL:**
```bash
curl -X DELETE http://127.0.0.1:8000/api/tickets/1 ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets/1" -Method Delete -Headers $headers
```

---

## 8. Logout

**cURL:**
```bash
curl -X POST http://127.0.0.1:8000/api/logout ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**PowerShell:**
```powershell
$token = "YOUR_TOKEN_HERE"
$headers = @{
    "Authorization" = "Bearer $token"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/logout" -Method Post -Headers $headers
```

---

## Complete Testing Workflow

### Step-by-Step Example

```powershell
# 1. Login as Admin
$loginBody = @{
    email = "admin@example.com"
    password = "admin123"
} | ConvertTo-Json

$loginResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method Post -Body $loginBody -ContentType "application/json"
$token = $loginResponse.data.token

Write-Host "Logged in successfully. Token: $token"

# 2. Create headers with token
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

# 3. Create a new ticket
$ticketBody = @{
    title = "Test from PowerShell"
    description = "This is a test ticket created via API"
    priority = "high"
    initial_comment = "First comment via API"
} | ConvertTo-Json

$newTicket = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets" -Method Post -Headers $headers -Body $ticketBody

Write-Host "Created ticket: $($newTicket.data.ticket_number)"

# 4. List all tickets
$tickets = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets" -Method Get -Headers $headers

Write-Host "Total tickets: $($tickets.data.total)"

# 5. Update the ticket status
$ticketId = $newTicket.data.id
$updateBody = @{
    status = "in_progress"
} | ConvertTo-Json

$updated = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets/$ticketId" -Method Put -Headers $headers -Body $updateBody

Write-Host "Updated ticket status to: $($updated.data.status)"

# 6. Get single ticket details
$ticketDetails = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/tickets/$ticketId" -Method Get -Headers $headers

Write-Host "Ticket has $($ticketDetails.data.comments.Count) comment(s)"

# 7. Logout
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/logout" -Method Post -Headers $headers

Write-Host "Logged out successfully"
```

---

## Testing Different User Roles

### Admin Capabilities
```powershell
# Login as Admin
$adminLogin = @{
    email = "admin@example.com"
    password = "admin123"
} | ConvertTo-Json

$adminResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method Post -Body $adminLogin -ContentType "application/json"

# Admin can see ALL tickets
# Admin can delete tickets
# Admin can assign tickets
```

### Staff Capabilities
```powershell
# Login as Staff
$staffLogin = @{
    email = "staff@example.com"
    password = "staff123"
} | ConvertTo-Json

$staffResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method Post -Body $staffLogin -ContentType "application/json"

# Staff can only see assigned tickets
# Staff cannot delete tickets
# Staff cannot change assignment
```

---

## Common Error Responses

### 401 Unauthorized (No Token)
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

### 403 Forbidden (No Permission)
```json
{
    "success": false,
    "message": "Unauthorized to delete tickets. Only admins can delete tickets."
}
```

### 404 Not Found
```json
{
    "success": false,
    "message": "Ticket not found"
}
```

### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": ["The title field is required."],
        "priority": ["The priority field is required."]
    }
}
```

### 422 Business Rule Violation
```json
{
    "success": false,
    "message": "Cannot move from OPEN directly to CLOSED. Recommended flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED"
}
```

---

## Postman Collection

If you're using Postman, you can create a collection with:

1. **Environment Variables:**
   - `base_url`: http://127.0.0.1:8000
   - `token`: (will be set automatically after login)

2. **Pre-request Script for Login:**
```javascript
// Save token after login
pm.test("Save token", function () {
    var jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
    }
});
```

3. **Authorization Header (for other requests):**
```
Authorization: Bearer {{token}}
```

---

## Tips for Testing

1. **Always login first** to get a fresh token
2. **Check response status codes** (200, 201, 401, 403, 404, 422)
3. **Test both admin and staff** accounts to verify permissions
4. **Try invalid data** to test validation
5. **Test business rules** (e.g., try moving OPEN to CLOSED)
6. **Check pagination** by creating multiple tickets
7. **Test filters** with different status/priority values
8. **Verify comment counts** in ticket listings

---

For more details, see [README.md](README.md)
