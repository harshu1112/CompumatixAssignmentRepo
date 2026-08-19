# Ticket Management System

A comprehensive ticket management system built with Laravel, featuring role-based access control, RESTful API, and modern UI with AJAX functionality.

## Table of Contents

- [Project Overview](#project-overview)
- [Requirements](#requirements)
- [Features](#features)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Demo Login Credentials](#demo-login-credentials)
- [API Documentation](#api-documentation)
- [Assumptions and Limitations](#assumptions-and-limitations)
- [License](#license)

---

## Project Overview

This Ticket Management System allows organizations to efficiently manage support tickets with distinct roles for administrators and staff members. The system includes:

- **Role-based Access Control**: Admin and Staff roles with specific permissions
- **Ticket Management**: Full CRUD operations for tickets with priority and status tracking
- **Comment System**: Add, update, and delete comments on tickets
- **Dashboard**: Real-time statistics and recent tickets overview
- **RESTful API**: Token-based authentication with comprehensive ticket endpoints
- **AJAX Features**: Dynamic status updates and comment submission without page refresh
- **Query Optimization**: Efficient database queries to avoid N+1 problems

---

## Requirements

### PHP and Laravel Versions

- **PHP**: ^8.2
- **Laravel Framework**: ^12.0
- **Database**: SQLite (default) or MySQL/PostgreSQL

### Key Dependencies

- **Laravel Sanctum**: ^4.3 (API authentication)
- **Laravel UI**: ^4.6 (Authentication scaffolding)
- **Spatie Laravel Permission**: ^6.25 (Role and permission management)
- **Bootstrap**: 5.3 (Frontend framework)
- **Font Awesome**: 6.0 (Icons)

### Server Requirements

- Apache/Nginx web server
- Composer 2.x
- Node.js & NPM (for frontend assets)

---

## Features

### User Roles and Permissions

#### Admin Permissions
- View all tickets
- Create tickets
- Update any ticket
- Delete tickets
- Assign tickets to staff
- Change ticket status
- Access admin dashboard

#### Staff Permissions
- View tickets assigned to them
- Update tickets assigned to them
- Add/update/delete own comments
- Cannot delete tickets
- Cannot modify tickets assigned to others

### Ticket Features

- **Unique Ticket Numbers**: Auto-generated (e.g., TKT-Y83VPJDG)
- **Priority Levels**: Low, Medium, High, Urgent
- **Status Flow**: Open → In Progress → Resolved → Closed
- **Assignment**: Assign tickets to staff members
- **Search & Filters**: Search by ticket number, title, or description
- **Sorting**: Sort by created date, status, priority, etc.
- **Pagination**: 10 tickets per page

### Business Rules

1. Recommended status flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED
2. Cannot move OPEN ticket directly to CLOSED status
3. Staff cannot modify tickets assigned to other staff members
4. Staff cannot delete tickets (admin-only)
5. Tickets cannot be assigned to invalid/non-existent users

---

## Installation

### Step 1: Clone or Download the Project

```bash
cd c:\xampp\htdocs
# If cloning from repository:
git clone <repository-url> Compumatrix_Assignment
cd Compumatrix_Assignment
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Build Frontend Assets

```bash
npm run build
```

For development with auto-rebuild:
```bash
npm run dev
```

---

## Database Setup

### Step 1: Configure Environment File

Copy the example environment file:

```bash
copy .env.example .env
```

### Step 2: Configure .env File

Open `.env` file and configure the following settings:

#### For SQLite (Default - Recommended for Development)

```env
APP_NAME="Ticket Management System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite
# DB_DATABASE will use database/database.sqlite
```

#### For MySQL (Alternative)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticket_management
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Generate Application Key

```bash
php artisan key:generate
```

### Step 4: Create SQLite Database File (if using SQLite)

The database file should already exist at `database/database.sqlite`. If not, create it:

```bash
type nul > database\database.sqlite
```

### Step 5: Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `users` (with role field: admin | staff)
- `tickets` (ticket management)
- `ticket_comments` (comments on tickets)
- `roles` and `permissions` (Spatie permission tables)
- `personal_access_tokens` (Sanctum API tokens)
- `cache`, `jobs`, `sessions` (Laravel system tables)

### Step 6: Run Seeders

```bash
php artisan db:seed
```

This will populate:
- **Roles**: admin, staff
- **Permissions**: All necessary permissions for both roles
- **Users**: Admin, staff, and test users with hashed passwords

#### Seeder Details

**RolePermissionSeeder** creates:
- Admin role with permissions: view all tickets, create tickets, update tickets, delete tickets, assign tickets, change ticket status, access dashboard
- Staff role with permissions: view assigned tickets, update assigned tickets, change ticket status, add comments, update own comments, delete own comments

**AdminSeeder** creates:
```
Main Admin:
- Email: admin@gmail.com
- Password: admin@123
- Role: admin

Additional Admins:
- Email: ramesh.kumar@company.com
- Password: password123
- Role: admin

- Email: kavita.mehta@company.com
- Password: password123
- Role: admin
```

**StaffSeeder** creates 10 staff members:
```
Staff Members:
- Email: raj.kumar@company.com, Password: password123
- Email: priya.sharma@company.com, Password: password123
- Email: amit.patel@company.com, Password: password123
- Email: sneha.singh@company.com, Password: password123
- Email: rahul.verma@company.com, Password: password123
- Email: anjali.gupta@company.com, Password: password123
- Email: vikram.reddy@company.com, Password: password123
- Email: pooja.rao@company.com, Password: password123
- Email: arjun.nair@company.com, Password: password123
- Email: neha.iyer@company.com, Password: password123
```

---

## Running the Application

### Start the Development Server

```bash
php artisan serve
```

The application will be available at: **http://127.0.0.1:8000**

### Access the Application

1. Open your browser and navigate to `http://127.0.0.1:8000`
2. You'll be redirected to the login page
3. Use the demo credentials below to log in

### Key URLs

- **Login**: http://127.0.0.1:8000/login
- **Dashboard**: http://127.0.0.1:8000/dashboard
- **Tickets**: http://127.0.0.1:8000/tickets
- **Ticket Comments**: http://127.0.0.1:8000/ticket-comments

---

## Demo Login Credentials

### Administrator Account
```
Email: admin@gmail.com
Password: admin@123
```

**Capabilities:**
- View all tickets
- Create, update, and delete tickets
- Assign tickets to staff
- Change ticket status
- View all comments
- Access full dashboard

### Staff Accounts

#### Staff Member 1
```
Email: raj.kumar@company.com
Password: password123
```

#### Staff Member 2
```
Email: priya.sharma@company.com
Password: password123
```

#### Staff Member 3
```
Email: amit.patel@company.com
Password: password123
```

**Additional Staff Members:**
- sneha.singh@company.com (password123)
- rahul.verma@company.com (password123)
- anjali.gupta@company.com (password123)
- vikram.reddy@company.com (password123)
- pooja.rao@company.com (password123)
- arjun.nair@company.com (password123)
- neha.iyer@company.com (password123)

**Staff Capabilities:**
- View tickets assigned to them only
- Update tickets assigned to them (including status)
- Add comments only on assigned tickets
- Update and delete own comments
- Cannot reassign tickets
- Cannot delete tickets

---

## API Documentation

### Base URL

```
http://127.0.0.1:8000/api
```

### Authentication

The API uses **Laravel Sanctum** for token-based authentication.

#### Login (Get Token)

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
    "email": "admin@gmail.com",
    "password": "admin@123"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Suresh Admin",
            "email": "admin@gmail.com",
            "role": "admin"
        },
        "token": "1|abcdef123456..."
    }
}
```

**Use the token in subsequent requests:**
```
Authorization: Bearer {token}
```

---

### API Endpoints

All endpoints below require authentication (Bearer token in Authorization header).

#### 1. Get Current User

**Endpoint:** `GET /api/user`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "Suresh Admin",
        "email": "admin@gmail.com",
        "role": "admin"
    }
}
```

---

#### 2. List Tickets

**Endpoint:** `GET /api/tickets`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters (all optional):**
- `status`: Filter by status (open|in_progress|resolved|closed)
- `priority`: Filter by priority (low|medium|high|urgent)
- `assigned_to`: Filter by assigned user ID
- `sort_by`: Sort field (ticket_number|title|priority|status|created_at|updated_at)
- `sort_order`: Sort direction (asc|desc)
- `per_page`: Items per page (default: 10)

**Example:**
```
GET /api/tickets?status=open&priority=high&sort_by=created_at&sort_order=desc
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Tickets retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "ticket_number": "TKT-ABC12345",
                "title": "Login Issue",
                "description": "Cannot log into the system",
                "priority": "high",
                "status": "open",
                "created_by": 1,
                "assigned_to": 2,
                "created_at": "2026-08-18T10:30:00.000000Z",
                "updated_at": "2026-08-18T10:30:00.000000Z",
                "creator": {
                    "id": 1,
                    "name": "Admin User",
                    "email": "admin@example.com"
                },
                "assignee": {
                    "id": 2,
                    "name": "Staff User",
                    "email": "staff@example.com"
                },
                "comments_count": 3
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

---

#### 3. Get Single Ticket

**Endpoint:** `GET /api/tickets/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Ticket retrieved successfully",
    "data": {
        "id": 1,
        "ticket_number": "TKT-ABC12345",
        "title": "Login Issue",
        "description": "Cannot log into the system",
        "priority": "high",
        "status": "open",
        "created_by": 1,
        "assigned_to": 2,
        "created_at": "2026-08-18T10:30:00.000000Z",
        "updated_at": "2026-08-18T10:30:00.000000Z",
        "creator": {...},
        "assignee": {...},
        "comments": [
            {
                "id": 1,
                "ticket_id": 1,
                "user_id": 1,
                "comment": "Investigating this issue",
                "created_at": "2026-08-18T10:35:00.000000Z",
                "user": {...}
            }
        ]
    }
}
```

**Error Response (404 Not Found):**
```json
{
    "success": false,
    "message": "Ticket not found"
}
```

---

#### 4. Create Ticket

**Endpoint:** `POST /api/tickets`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "title": "System Error",
    "description": "Getting 500 error on dashboard",
    "priority": "high",
    "status": "open",
    "assigned_to": 2,
    "initial_comment": "This needs immediate attention"
}
```

**Required Fields:**
- `title` (string, max 255 chars)
- `description` (string)
- `priority` (low|medium|high|urgent)

**Optional Fields:**
- `status` (open|in_progress|resolved) - default: open - Note: Cannot create with "closed" status
- `assigned_to` (user ID)
- `initial_comment` (string) - Creates a comment automatically with the ticket

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Ticket created successfully",
    "data": {
        "id": 2,
        "ticket_number": "TKT-XYZ98765",
        "title": "System Error",
        "description": "Getting 500 error on dashboard",
        "priority": "high",
        "status": "open",
        "created_by": 1,
        "assigned_to": 2,
        "created_at": "2026-08-18T11:00:00.000000Z",
        "updated_at": "2026-08-18T11:00:00.000000Z",
        "comments_count": 1
    }
}
```

**Error Response (422 Validation Error):**
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

---

#### 5. Update Ticket

**Endpoint:** `PUT /api/tickets/{id}` or `PATCH /api/tickets/{id}`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body (all fields optional):**
```json
{
    "title": "Updated Title",
    "description": "Updated description",
    "priority": "urgent",
    "status": "in_progress",
    "assigned_to": 3
}
```

**Business Rules:**
- Admins can update all fields
- Staff can only update tickets assigned to them
- Staff cannot change status or assigned_to
- Cannot move from "open" directly to "closed"

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Ticket updated successfully",
    "data": {
        "id": 1,
        "ticket_number": "TKT-ABC12345",
        "title": "Updated Title",
        ...
    }
}
```

**Error Response (403 Forbidden):**
```json
{
    "success": false,
    "message": "You can only update tickets assigned to you"
}
```

**Error Response (422 Validation Error):**
```json
{
    "success": false,
    "message": "Cannot move from OPEN directly to CLOSED. Recommended flow: OPEN → IN_PROGRESS → RESOLVED → CLOSED"
}
```

---

#### 6. Delete Ticket

**Endpoint:** `DELETE /api/tickets/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Permission:** Admin only

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Ticket deleted successfully"
}
```

**Error Response (403 Forbidden):**
```json
{
    "success": false,
    "message": "Unauthorized to delete tickets. Only admins can delete tickets."
}
```

---

#### 7. Logout

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

---

### API Error Responses

#### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

#### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized to perform this action"
}
```

#### 404 Not Found
```json
{
    "success": false,
    "message": "Ticket not found"
}
```

#### 422 Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

#### 500 Internal Server Error
```json
{
    "success": false,
    "message": "An error occurred",
    "error": "Error details..."
}
```

---

## Assumptions and Limitations

### Assumptions

1. **SQLite Database**: The application uses SQLite by default for simplicity. For production, MySQL or PostgreSQL is recommended.

2. **XAMPP Environment**: Development instructions assume XAMPP on Windows. Can be adapted for other environments.

3. **Ticket Assignment**: Tickets can only be assigned to users with the "staff" role.

4. **Comment Ownership**: Staff can only edit/delete their own comments. Admins have no restrictions.

5. **Status Flow**: While the recommended flow is OPEN → IN_PROGRESS → RESOLVED → CLOSED, the system only prevents OPEN → CLOSED direct transition. Other transitions are allowed for flexibility.

6. **Unique Ticket Numbers**: Generated using 8 random uppercase alphanumeric characters (TKT-XXXXXXXX). Collision chance is minimal but theoretically possible.

7. **Session-based Web Auth**: Web interface uses Laravel's session-based authentication.

8. **Token-based API Auth**: API uses Sanctum token authentication. Tokens don't expire by default.

9. **Single Database Transaction**: Only ticket creation with initial comment uses transactions. Other operations don't require transactional integrity.

10. **N+1 Query Optimization**: Implemented using `withCount('comments')` for efficient comment counting.

### Limitations

1. **No Email Notifications**: The system doesn't send email notifications for ticket updates or assignments.

2. **No File Attachments**: Tickets and comments don't support file uploads.

3. **No Ticket History**: Changes to tickets aren't logged. No audit trail of modifications.

4. **No Real-time Updates**: UI doesn't auto-refresh. Users must manually refresh to see updates from others.

5. **Basic Search**: Search only covers ticket number, title, and description. No advanced search or full-text indexing.

6. **No Export Functionality**: Cannot export tickets to CSV, PDF, or other formats.

7. **No Ticket Categories**: Tickets don't have categories or departments.

8. **Limited Dashboard**: Dashboard shows basic statistics only. No charts or graphs.

9. **No SLA Management**: No service level agreement tracking or due dates.

10. **No Multi-tenant Support**: Single organization only. No tenant isolation.

11. **No API Rate Limiting**: API endpoints don't have rate limiting implemented.

12. **Basic Permissions**: Only two roles (admin/staff). No custom role creation.

13. **Comment Threading**: Comments are flat. No replies or nested comments.

14. **No Notification System**: No in-app notifications for ticket updates.

15. **Pagination Fixed**: Pagination is fixed at 10 items per page (not configurable by users).

### Future Enhancements

- Email notifications for ticket assignments and status changes
- File attachment support for tickets and comments
- Advanced search with Elasticsearch integration
- Real-time updates using Laravel Echo and WebSockets
- Ticket history and audit trail
- Export functionality (CSV, PDF, Excel)
- Ticket categories and departments
- Enhanced dashboard with charts and analytics
- SLA management with due dates and reminders
- Multi-tenant architecture
- API rate limiting and throttling
- Custom role and permission builder
- Threaded comment system
- In-app notification system
- User-configurable pagination
- Mobile-responsive improvements
- Two-factor authentication
- Dark mode support

---

## Project Structure

```
Compumatrix_Assignment/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── API/
│   │       │   ├── AuthController.php
│   │       │   └── TicketApiController.php
│   │       ├── Auth/
│   │       ├── DashboardController.php
│   │       ├── TicketController.php
│   │       └── TicketCommentController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Ticket.php
│   │   └── TicketComment.php
│   └── Providers/
├── config/
│   ├── auth.php
│   ├── permission.php
│   └── sanctum.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RolePermissionSeeder.php
│   │   └── UserSeeder.php
│   └── database.sqlite
├── public/
│   └── assets/
│       └── admin/
│           └── js/
│               └── main.js
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── dashboard/
│   │   │   ├── layouts/
│   │   │   │   └── partials/
│   │   │   ├── tickets/
│   │   │   └── ticketcomments/
│   │   └── auth/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── api.php
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## Technology Stack

- **Backend**: Laravel 12.0, PHP 8.2
- **Authentication**: Laravel UI, Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Frontend**: Bootstrap 5.3, Font Awesome 6.0
- **Database**: SQLite (development), MySQL/PostgreSQL compatible
- **JavaScript**: Vanilla JS with AJAX
- **Build Tools**: Vite

---

## Support

For issues, questions, or contributions, please refer to the project documentation or contact the development team.

---

## License

This project is licensed under the MIT License.
