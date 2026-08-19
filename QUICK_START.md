# Quick Start Guide

## Installation (5 Minutes)

### 1. Install Dependencies
```bash
composer install
npm install
npm run build
```

### 2. Setup Environment
```bash
copy .env.example .env
php artisan key:generate
```

### 3. Setup Database
```bash
php artisan migrate
php artisan db:seed
```

### 4. Start Server
```bash
php artisan serve
```

Visit: **http://127.0.0.1:8000**

---

## Login Credentials

### Admin
```
Email: admin@example.com
Password: admin123
```

### Staff
```
Email: staff@example.com
Password: staff123
```

---

## API Quick Start

### 1. Get Token
```bash
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "admin123"
}
```

### 2. List Tickets
```bash
GET http://127.0.0.1:8000/api/tickets
Authorization: Bearer {your_token}
```

### 3. Create Ticket
```bash
POST http://127.0.0.1:8000/api/tickets
Authorization: Bearer {your_token}
Content-Type: application/json

{
    "title": "Test Ticket",
    "description": "Testing API",
    "priority": "high",
    "initial_comment": "First comment"
}
```

---

## Common Issues

### Database Errors
```bash
# Reset database
php artisan migrate:fresh --seed
```

### Permission Errors
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Frontend Not Loading
```bash
# Rebuild assets
npm run build
```

---

## Project URLs

- Login: http://127.0.0.1:8000/login
- Dashboard: http://127.0.0.1:8000/dashboard
- Tickets: http://127.0.0.1:8000/tickets
- Comments: http://127.0.0.1:8000/ticket-comments
- API Base: http://127.0.0.1:8000/api

---

For complete documentation, see [README.md](README.md)
