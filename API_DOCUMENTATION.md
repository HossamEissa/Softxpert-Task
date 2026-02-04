# Task Management System API Documentation

## Overview
RESTful API for managing tasks with automatic dependency handling, role-based access control, and hierarchical due date validation.

## Authentication
All endpoints require `auth:sanctum` authentication. Include the bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Test Users
- **Manager**: manager@admin.com / 12345678
- **User**: user@admin.com / 12345678

## Roles & Permissions

### Manager Role
- `task.create` - Create new tasks
- `task.update` - Update task details
- `task.assign` - Assign tasks to users
- `task.view-all` - View all tasks
- `task.view` - View tasks
- `task.update-status` - Update task status (including completed/delayed/cancelled)

### User Role
- `task.view` - View assigned tasks only
- `task.update-status` - Update status of assigned tasks (cannot change completed/delayed/cancelled)

## API Endpoints

### 1. Get All Tasks
**GET** `/api/tasks`

**Description**: Managers see all tasks, users see only their assigned tasks.

**Query Parameters**:
- `search` - Search in title and description
- `filter[status]` - Filter by status (pending, assigned, in-progress, completed, cancelled, delayed)
- `filter[assignee_id]` - Filter by assignee
- `filter[due_date]` - Filter by due date
- `sort_by` - Sort field (id, title, due_date, status, created_at)
- `sort_order` - Sort order (asc, desc)
- `pagination` - Set to 'none' to get all records

**Example Request**:
```bash
curl -X GET "http://localhost:8000/api/tasks?sort_by=due_date&sort_order=asc" \
  -H "Authorization: Bearer {token}"
```

---

### 2. Get Specific Task
**GET** `/api/tasks/{id}`

**Description**: Get details of a specific task including dependencies and dependents.

**Response** includes:
- Task details
- All dependencies (tasks this task depends on)
- All dependents (tasks that depend on this task)
- Dependency completion status

---

### 3. Create Task
**POST** `/api/tasks`

**Permission Required**: `task.create` (Manager only)

**Request Body**:
```json
{
  "title": "Implement Frontend Integration",
  "description": "Connect frontend to backend API",
  "due_date": "2026-02-14 23:59:59",
  "dependency_ids": [2, 3]
}
```

**Validation Rules**:
- `title` - Required, max 255 characters
- `description` - Optional
- `due_date` - Required, must be today or future date
- `dependency_ids` - Optional array of task IDs
- **Hierarchical Validation**: All dependencies must have `due_date <= this task's due_date`
- **Circular Dependency Prevention**: Cannot create circular dependency chains

**Example**:
```bash
curl -X POST "http://localhost:8000/api/tasks" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Deploy to Production",
    "description": "Deploy application to production server",
    "due_date": "2026-02-20 23:59:59",
    "dependency_ids": [1, 2]
  }'
```

---

### 4. Update Task
**PUT** `/api/tasks/{id}`

**Permission Required**: `task.update` (Manager only)

**Request Body** (all fields optional):
```json
{
  "title": "Updated Title",
  "description": "Updated description",
  "due_date": "2026-02-18 23:59:59",
  "dependency_ids": [3, 4]
}
```

**Validation Rules**:
- When updating `due_date`:
  - Must not violate hierarchy (dependents must have later or equal due dates)
  - Dependencies must have earlier or equal due dates
- When updating `dependency_ids`:
  - Validates circular dependencies
  - Validates due date hierarchy
- **Replaces all dependencies** if `dependency_ids` is provided

---

### 5. Assign Task
**POST** `/api/tasks/{id}/assign`

**Permission Required**: `task.assign` (Manager only)

**Request Body**:
```json
{
  "assignee_id": 2
}
```

**Automatic Behavior**:
- Recursively finds all dependencies
- Automatically assigns all **unassigned** dependencies to the same user
- Skips dependencies already assigned to other users
- Uses database transaction - rolls back all if any assignment fails
- Changes task status to `assigned`

**Example Scenario**:
```
Task A depends on B and C
Task B depends on D
Task C has no dependencies

When assigning Task A to User X:
- Task D will be assigned to User X (if unassigned)
- Task B will be assigned to User X (if unassigned)
- Task C will be assigned to User X (if unassigned)
- Task A will be assigned to User X
```

**Response**:
```json
{
  "status": true,
  "message": "Task assigned successfully. 4 task(s) assigned in total.",
  "data": {
    "task": { /* Task resource */ },
    "assigned_tasks": [
      {
        "id": 1,
        "title": "Implement Frontend Integration",
        "due_date": "2026-02-14 23:59:59"
      },
      {
        "id": 2,
        "title": "Create API Endpoints",
        "due_date": "2026-02-13 23:59:59"
      }
    ]
  }
}
```

---

### 6. Update Task Status
**PATCH** `/api/tasks/{id}/status`

**Permission Required**: `task.update-status`

**Request Body**:
```json
{
  "status": "in-progress"
}
```

**Allowed Status Values**:
- `in-progress`
- `completed`
- `cancelled`
- `delayed`

**Validation Rules**:
- **Users**: Can only update status of tasks assigned to them
- **Users**: Cannot change status FROM `completed`, `delayed`, or `cancelled`
- **Managers**: Can change any status including completed/delayed/cancelled
- **Completion Validation**: When marking as `completed`, all dependencies must be completed first

**Example**:
```bash
curl -X PATCH "http://localhost:8000/api/tasks/5/status" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "completed"
  }'
```

**Error Response** (if dependencies not completed):
```json
{
  "status": 0,
  "message": "Cannot mark task as completed. The following dependency tasks are not completed: Setup Database, Configure Auth"
}
```

---

### 7. Delete Task
**DELETE** `/api/tasks/{id}`

**Permission Required**: `task.create` (Manager only)

**Validation**:
- Cannot delete a task if other tasks depend on it
- Automatically deletes all dependencies of this task

**Error Response** (if has dependents):
```json
{
  "status": 0,
  "message": "Cannot delete this task. The following tasks depend on it: Frontend Integration, API Testing"
}
```

---

## Task Status Lifecycle

```
pending → assigned → in-progress → completed
                ↓         ↓
            cancelled   delayed
```

### Status Descriptions:
- **pending** - Task created but not assigned
- **assigned** - Task assigned to a user
- **in-progress** - User is actively working on the task
- **completed** - Task finished successfully
- **cancelled** - Task cancelled by manager
- **delayed** - Task overdue (set automatically by scheduled command or manually by manager)

---

## Scheduled Commands

### Mark Overdue Tasks
**Command**: `php artisan tasks:mark-overdue`

**Schedule**: Runs daily at midnight (configured in `routes/console.php`)

**Behavior**:
- Finds all tasks where `due_date < now()`
- Excludes tasks already `completed`, `cancelled`, or `delayed`
- Marks them as `delayed`
- Works independently for each task (doesn't cascade to dependents)

**Manual Execution**:
```bash
php artisan tasks:mark-overdue
```

---

## Sample Task Data

After running `php artisan migrate:fresh --seed`, the following tasks are created:

### Dependency Chain Example:
```
Task A: "Implement Frontend Integration" (due: +10 days)
  ├─ depends on → Task B: "Create API Endpoints" (due: +7 days)
  │   └─ depends on → Task D: "Setup Database Schema" (due: +3 days)
  └─ depends on → Task C: "Setup Authentication System" (due: +5 days)
```

**Assigning Task A** will automatically assign Tasks B, C, and D.

### Other Sample Tasks:
- Task E: Independent task (no dependencies)
- Task F: Already assigned to user@admin.com
- Task G: In-progress task
- Task H: Overdue task (will be marked as delayed)
- Task I & J: Completed tasks with dependencies
- Task K: Pending task depending on completed task

---

## Error Responses

### Circular Dependency Error:
```json
{
  "status": 0,
  "message": "Cannot add dependency 'Task X' as it would create a circular dependency"
}
```

### Due Date Hierarchy Error:
```json
{
  "status": 0,
  "message": "The due date must be equal to or after the due dates of all dependency tasks. Invalid dependencies: Task B, Task C"
}
```

### Permission Error:
```json
{
  "status": 0,
  "message": "You do not have permission to perform this action."
}
```

---

## Testing Flow

### 1. Login as Manager
```bash
POST /api/login
{
  "email": "manager@admin.com",
  "password": "12345678",
  "device_token": "test-token",
  "device_name": "Postman"
}
```

### 2. View All Tasks
```bash
GET /api/tasks
```

### 3. Assign Task A (Frontend Integration)
```bash
POST /api/tasks/1/assign
{
  "assignee_id": 2
}
```
**Result**: Tasks A, B, C, and D all assigned to user (assignee_id: 2)

### 4. Login as User
```bash
POST /api/login
{
  "email": "user@admin.com",
  "password": "12345678",
  "device_token": "test-token",
  "device_name": "Postman"
}
```

### 5. View Assigned Tasks
```bash
GET /api/tasks
```

### 6. Update Task Status to In-Progress
```bash
PATCH /api/tasks/4/status
{
  "status": "in-progress"
}
```

### 7. Try to Complete Task (Should Fail if Dependencies Not Completed)
```bash
PATCH /api/tasks/1/status
{
  "status": "completed"
}
```

### 8. Complete Dependencies First
```bash
PATCH /api/tasks/4/status
{ "status": "completed" }

PATCH /api/tasks/2/status
{ "status": "completed" }

PATCH /api/tasks/3/status
{ "status": "completed" }
```

### 9. Now Complete Main Task
```bash
PATCH /api/tasks/1/status
{ "status": "completed" }
```

---

## Implementation Features

✅ **Automatic Dependency Assignment**: Recursively assigns all unassigned dependencies  
✅ **Hierarchical Due Date Validation**: Dependencies must have earlier/equal due dates  
✅ **Circular Dependency Prevention**: Detects and blocks circular references  
✅ **Completion Validation**: Requires all dependencies completed before task completion  
✅ **Role-Based Access Control**: Managers and users have different permissions  
✅ **Status Lifecycle Management**: Managers can modify any status, users have restrictions  
✅ **Transaction Safety**: Uses database transactions for assignment operations  
✅ **Scheduled Overdue Detection**: Daily command marks overdue tasks as delayed  
✅ **Comprehensive Filtering**: Search, filter, sort on all task endpoints  
✅ **Single-User Assignment**: Each task assigned to only one user  

---

## Database Schema

### tasks
- `id` - Primary key
- `title` - Task title
- `description` - Task description
- `due_date` - Deadline timestamp
- `status` - Enum (pending, assigned, in-progress, completed, cancelled, delayed)
- `assignee_id` - Foreign key to users (nullable)
- `created_by` - Foreign key to users (manager who created the task)
- `created_at` / `updated_at` - Timestamps

### task_dependencies
- `id` - Primary key
- `task_id` - Foreign key to tasks (the task that has dependencies)
- `dependency_id` - Foreign key to tasks (the task that is depended upon)
- `created_at` / `updated_at` - Timestamps

**Relationship**: A task can have multiple dependencies, and a task can be a dependency for multiple tasks.
