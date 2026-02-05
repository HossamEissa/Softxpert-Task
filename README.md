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

## Prerequisites

- Docker & Docker Compose (recommended)
- OR PHP 8.2+, Composer, MySQL 8.0+ (without Docker)

---

## Installation & Setup

### Option 1: Using Docker (Recommended)

#### Quick Setup (Automated)

**Use the automated setup script:**
```bash
chmod +x docker-setup.sh
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

## Quick Start Guide

### 1. Login as Manager
```bash
POST http://localhost:8000/api/login  # Docker or Without Docker

Content-Type: application/json

{
  "email": "manager@admin.com",
  "password": "12345678",
  "device_name": "Postman"
}
```

### 2. View All Tasks
```bash
GET http://localhost:8000/api/tasks
Authorization: Bearer {access_token}
```

**Query Parameters:**
- `search=keyword` - Search in title/description
- `status=pending` - Filter by status (pending, assigned, in-progress, completed, cancelled, delayed)
- `assignee_id=2` - Filter by assignee
- `created_by=1` - Filter by creator
- `due_date_from=2026-02-01` - Filter by due date range start
- `due_date_to=2026-02-28` - Filter by due date range end
- `sort_by=due_date` - Sort by field (id, due_date, created_at)
- `sort_order=asc` - Sort direction (asc, desc)
- `per_page=15` - Items per page
- `page=1` - Page number

**Example:**
```bash
GET http://localhost:8000/api/tasks?status=pending&sort_by=due_date&sort_order=asc&per_page=10
```

### 3. Create Task with Dependencies
```bash
POST http://localhost:8000/api/tasks
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "title": "Complete Project Documentation",
  "description": "Write comprehensive documentation",
  "due_date": "2026-02-20",
  "dependency_ids": [1, 2]
}
```

### 4. Assign Task (Auto-Assigns Dependencies)
```bash
POST http://localhost:8000/api/tasks/1/assign
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "assignee_id": 2
}
```

### 5. Update Task Status
```bash
PATCH http://localhost:8000/api/tasks/1/status
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "status": "in-progress"
}
```

**Available Statuses:**
- `pending` - Initial state
- `assigned` - Assigned to a user
- `in-progress` - Work in progress
- `completed` - Task finished (requires all dependencies completed)
- `cancelled` - Task cancelled
- `delayed` - Task overdue (auto-set by scheduler)

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

## Environment Variables

### Required Configuration
```env
# Application
APP_NAME=Task
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (Docker)
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=softxpert_task
DB_USERNAME=root
DB_PASSWORD=root

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

# Mail (Development - logs to file)
MAIL_MAILER=log

# Mail (Production - example with Gmail)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
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
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx
- **Mail**: Log Driver (development) / SMTP (production)
- **Testing**: Pest PHP (optional)

---

## Project Architecture

### Design Patterns Used
- **Service Layer Pattern**: Business logic separated in `TaskService`
- **Repository Pattern**: Eloquent models with custom traits
- **Request Validation**: Form Request classes for validation
- **Resource Pattern**: API Resources for response transformation
- **Policy-Based Authorization**: Laravel Policies for access control

### Custom Traits
- **DynamicPagination**: Flexible pagination with query parameters
- **Filterable**: Advanced filtering with multiple operators
- **Searchable**: Full-text search across specified fields
- **Sortable**: Dynamic sorting by allowed fields

---

## Security Features

- **Token-Based Authentication**: Laravel Sanctum API tokens
- **Role-Based Access Control**: Spatie Permission package
- **Request Validation**: Form Request validation on all inputs
- **Policy Authorization**: Fine-grained access control
- **Database Transactions**: Ensures data consistency
- **Password Hashing**: Bcrypt password hashing
- **CSRF Protection**: Enabled for web routes
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries

---

## Performance Optimizations

- **Eager Loading**: Prevents N+1 queries with `with()`
- **Database Indexing**: Foreign keys and frequently queried columns
- **Query Optimization**: Efficient use of filters and pagination
- **Caching**: Database cache driver (configurable)
- **Transaction Management**: Reduces database round trips

---

## License

This project was developed as part of a technical assessment task.

---

## Support & Contact

For issues or questions, please contact the development team or create an issue in the repository.

---

**Last Updated**: February 2026
