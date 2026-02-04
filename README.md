# Task Management System API

A RESTful API for managing tasks with automatic dependency handling, role-based access control, and hierarchical due date validation built with Laravel 12.

## Features

✅ **Automatic Dependency Assignment** - When assigning a task, all its dependencies are automatically assigned to the same user  
✅ **Hierarchical Due Date Validation** - Dependencies must have earlier or equal due dates  
✅ **Circular Dependency Prevention** - System detects and blocks circular dependency chains  
✅ **Completion Validation** - All dependencies must be completed before a task can be marked as completed  
✅ **Role-Based Access Control** - Managers and users have different permissions using Spatie Laravel Permission  
✅ **Status Lifecycle Management** - Managers can modify any status, users have restrictions  
✅ **Transaction Safety** - Database transactions ensure data consistency during assignments  
✅ **Scheduled Overdue Detection** - Daily command automatically marks overdue tasks as delayed  
✅ **Advanced Filtering** - Search, filter, sort on all task endpoints  
✅ **Single-User Assignment** - Each task is assigned to only one user  

---

---

## Installation & Setup

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
Copy `.env.example` to `.env` and configure database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=softxper_task
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

### 5. Start Development Server
```bash
php artisan serve
```

API available at: `http://localhost:8000`

---

## Test Users

**Manager**
- Email: `manager@admin.com`
- Password: `12345678`

**User**
- Email: `user@admin.com`
- Password: `12345678`

---

## Quick Start

### 1. Login as Manager
```bash
POST http://localhost:8000/api/login
{
  "email": "manager@admin.com",
  "password": "12345678",
  "device_token": "test",
  "device_name": "Postman"
}
```

### 2. View All Tasks
```bash
GET http://localhost:8000/api/tasks
Authorization: Bearer {token}
```

### 3. Assign Task with Auto-Dependency Assignment
```bash
POST http://localhost:8000/api/tasks/1/assign
{
  "assignee_id": 2
}
```
**Result**: Task and all its dependencies automatically assigned!

---

## API Documentation

Complete API documentation available in [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

**Key Endpoints:**
- `GET /api/tasks` - List tasks (managers: all, users: assigned only)
- `POST /api/tasks` - Create task with dependencies
- `POST /api/tasks/{id}/assign` - Assign task (auto-assigns dependencies)
- `PATCH /api/tasks/{id}/status` - Update task status
- `PUT /api/tasks/{id}` - Update task details
- `DELETE /api/tasks/{id}` - Delete task

---

## Key Features Explained

### 1. Automatic Dependency Assignment
When you assign Task A (depends on B, C), all unassigned dependencies are automatically assigned to the same user.

### 2. Hierarchical Due Dates
```
Task C (Day 3) ← Task B (Day 5) ← Task A (Day 7)
```
Dependencies must have earlier/equal due dates.

### 3. Completion Validation
Task can only be completed if all dependencies are completed first.

### 4. Circular Dependency Prevention
System prevents: Task A → Task B → Task A

### 5. Role-Based Permissions
- **Managers**: Full control (create, update, assign, delete, view all)
- **Users**: View assigned tasks, update status (with restrictions)

---

## Scheduled Commands

Mark overdue tasks as delayed (runs daily at midnight):
```bash
php artisan tasks:mark-overdue
```

To run scheduler in development:
```bash
php artisan schedule:work
```

---

## Project Structure

```
app/
├── Console/Commands/
│   └── MarkOverdueTasksCommand.php
├── Http/
│   ├── Controllers/API/Task/
│   │   └── TaskController.php
│   ├── Requests/API/Task/
│   │   ├── CreateTaskRequest.php
│   │   ├── UpdateTaskRequest.php
│   │   ├── AssignTaskRequest.php
│   │   └── UpdateTaskStatusRequest.php
│   └── Resources/
│       ├── TaskResource.php
│       └── TaskDependencyResource.php
├── Models/
│   ├── Task.php
│   └── TaskDependency.php
└── Services/
    └── TaskService.php

database/
├── migrations/
│   ├── create_tasks_table.php
│   └── create_task_dependencies_table.php
└── seeders/
    └── TaskSeeder.php
```

---

## Testing Scenarios

### Scenario 1: Automatic Assignment
1. Assign Task A (depends on B, C, D)
2. Verify all 4 tasks assigned to same user

### Scenario 2: Due Date Validation
1. Try to create task with due date before its dependencies
2. Should fail with validation error

### Scenario 3: Completion Validation
1. Try to complete task while dependencies incomplete
2. Should fail with error listing incomplete dependencies

### Scenario 4: Permission Restrictions
1. Login as user
2. Try to create task (should fail - managers only)
3. Try to update unassigned task (should fail)

---

## Tech Stack

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Testing**: Pest PHP

---

## License

This project was developed as part of a technical assessment task.

For detailed API documentation, see [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
