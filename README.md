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
✅ **Comprehensive Test Suite** - 101 passing tests with Pest PHP covering all features  

---

## Prerequisites

- Docker & Docker Compose (recommended)
- OR PHP 8.2+, Composer, MySQL 8.0+ (without Docker)

---

## Installation & Setup

### Option 1: Using Docker (Recommended)

#### Quick Setup (Automated)

**Use the automated setup script:**
```bash
./docker-setup.sh
```

This script will automatically:
- Create `.env` file from `.env.example`
- Build and start Docker containers
- Install Composer dependencies
- Generate application key
- Run database migrations
- Create storage symlink
- Set proper permissions

Your application will be ready at `http://localhost:8000`

---

#### Manual Setup (Step by Step)

If you prefer manual setup or need to customize:

#### 1. Clone the Repository
```bash
cd /path/to/your/projects
git clone <repository-url>
cd SoftxpertTask
```

#### 2. Configure Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

Update database configuration in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=softxpert_task
DB_USERNAME=root
DB_PASSWORD=root
```

#### 3. Build and Start Docker Containers
```bash
docker-compose up -d
```

This will start:
- **app**: PHP-FPM 8.2 container
- **db**: MySQL 8.0 container
- **nginx**: Nginx web server
- **phpmyadmin**: Database management UI (optional)

#### 4. Install Dependencies
```bash
docker-compose exec app composer install
```

#### 5. Generate Application Key
```bash
docker-compose exec app php artisan key:generate
```

#### 6. Run Migrations and Seeders
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

#### 7. Access the Application
- **API Base URL**: `http://localhost:8000`
- **phpMyAdmin**: `http://localhost:8080` (root/root)

---

### Option 2: Without Docker (Local PHP)

#### 1. Install Dependencies
```bash
composer install
```

#### 2. Configure Environment
Copy `.env.example` to `.env` and configure database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=softxpert_task
DB_USERNAME=root
DB_PASSWORD=
```

#### 3. Generate Application Key
```bash
php artisan key:generate
```

#### 4. Run Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

#### 5. Start Development Server
```bash
php artisan serve
```

API available at: `http://localhost:8000`

---

## Testing

**101 passing tests** using Pest PHP covering authentication, CRUD operations, dependencies, permissions, and business logic.

### Running Tests

```bash
# With Docker
docker-compose exec app php artisan test

# Without Docker
php artisan test
```

### Test Coverage
- Authentication (8 tests) - Registration, login, logout
- Task CRUD (13 tests) - Create, read, update with permissions
- Dependencies (6 tests) - Assignment, validation, synchronization
- Status Management (10 tests) - Transitions, completion checks
- Circular Dependencies (6 tests) - Prevention and detection
- Role Permissions (24 tests) - RBAC and policy testing
- Unit Tests (26 tests) - Models and services
- Scheduled Tasks (6 tests) - Overdue detection

---

## Docker Commands Reference

### Managing Containers
```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f app

# Restart containers
docker-compose restart
```

### Running Artisan Commands
```bash
# Run any artisan command
docker-compose exec app php artisan [command]

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:list
```

### Database Management
```bash
# Access MySQL CLI
docker-compose exec db mysql -u root -proot softxpert_task

# Backup database
docker-compose exec db mysqldump -u root -proot softxpert_task > backup.sql

# Import database
docker-compose exec -T db mysql -u root -proot softxpert_task < backup.sql
```

### Accessing Container Shell
```bash
# Access app container
docker-compose exec app bash

# Access database container
docker-compose exec db bash
```

---
```bash
php artisan serve
```

API available at: `http://localhost:8000`

---

## Test Users

**Manager**
- Email: `manager@admin.com`
- Password: `12345678`
- Permissions: Full access (create, update, delete, assign, view all tasks)

**User**
- Email: `user@admin.com`
- Password: `12345678`
- Permissions: View assigned tasks, update status (limited)

---

## Database Schema

**Interactive Entity-Relationship Diagram:**

🗂️ **[View Database ERD Diagram](https://dbdiagram.io/d/69843dffbd82f5fce2b8728c)**

The database schema includes:
- **Users** - System users with authentication
- **Tasks** - Task management with status, due dates, assignments
- **Task Dependencies** - Many-to-many self-referencing relationship for task hierarchies
- **Roles & Permissions** - Role-based access control using Spatie Laravel Permission

Key relationships:
- Users can create and be assigned to multiple tasks
- Tasks can have multiple dependencies (and be a dependency for multiple tasks)
- Users have roles with specific permissions

---

## API Documentation

**Complete API documentation with examples is available in Postman:**

📚 **[View Postman Documentation](https://documenter.getpostman.com/view/25142654/2sBXc8nhpU)**

The Postman collection includes:
- All available endpoints with request examples
- Authentication flows
- Task management operations
- Filtering, sorting, and pagination examples
- Error handling scenarios

### Quick Endpoint Reference

**Authentication:**
- `POST /api/register` - Register new user
- `POST /api/login` - Login and get access token
- `POST /api/logout` - Logout (revoke token)

**Tasks:**
- `GET /api/tasks` - List tasks (with pagination, search, filter, sort)
- `POST /api/tasks` - Create new task
- `GET /api/tasks/{id}` - View task details
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task
- `POST /api/tasks/{id}/assign` - Assign task to user
- `PATCH /api/tasks/{id}/status` - Update task status

**Profile:**
- `GET /api/me` - Get current user profile
- `PUT /api/update-profile` - Update profile
- `POST /api/change-password` - Change password

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

### Mark Overdue Tasks (Runs Daily at Midnight)
Automatically marks tasks with `due_date < now()` as "delayed" if they're not completed, cancelled, or already delayed.

```bash
# Manual execution (Docker)
docker-compose exec app php artisan tasks:mark-overdue

# Manual execution (Without Docker)
php artisan tasks:mark-overdue

# Run scheduler in development (Docker)
docker-compose exec app php artisan schedule:work

# Run scheduler in development (Without Docker)
php artisan schedule:work
```

---

## Troubleshooting

### Docker Issues

**Port already in use:**
```bash
# Change ports in docker-compose.yml
ports:
  - "8001:80"  # Change nginx from 8000 to 8001
  - "8081:80"  # Change phpmyadmin from 8080 to 8081
  - "3308:3306"  # Change MySQL from 3307 to 3308
```

**Permission issues:**
```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

**Container not starting:**
```bash
# View logs
docker-compose logs app
docker-compose logs db

# Rebuild containers
docker-compose down
docker-compose up -d --build
```

### Database Issues

**Connection refused:**
- Ensure database container is running: `docker-compose ps`
- Check `.env` DB_HOST is set to `db` (not `127.0.0.1`)
- Wait 10-20 seconds after starting containers for MySQL to initialize

**Migration errors:**
```bash
# Reset database (Docker)
docker-compose exec app php artisan migrate:fresh --seed

# Reset database (Without Docker)
php artisan migrate:fresh --seed
```

### Authentication Issues

**Token expired or invalid:**
- Login again to get a new token
- Check `Authorization: Bearer {token}` header is set correctly

**Email verification not working:**
- Check `.env` has `MAIL_MAILER=log`
- OTP codes are logged to `storage/logs/laravel.log`
- For testing, configure SMTP or use Mailtrap

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
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx
- **Mail**: Log Driver (development) / SMTP (production)

---

**Last Updated**: February 2026

