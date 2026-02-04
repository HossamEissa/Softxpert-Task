<?php

namespace App\Models;

use App\Enum\TaskStatusEnum;
use App\Traits\DynamicPagination;
use App\Traits\Filterable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory, DynamicPagination, Filterable, Searchable, Sortable;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'assignee_id',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'status' => TaskStatusEnum::class,
    ];

    protected $searchableFields = ['title', 'description'];
    protected $sortableFields = ['id', 'title', 'due_date', 'status', 'created_at'];
    protected $filterableFields = ['status', 'assignee_id', 'created_by', 'due_date'];

####################################### Relations ###################################################

    /**
     * Get the user who created this task (manager)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to this task
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * Get all task dependencies (tasks this task depends on)
     */
    public function taskDependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    /**
     * Get all tasks this task depends on
     */
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id')
            ->withTimestamps();
    }

    /**
     * Get all tasks that depend on this task
     */
    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependency_id', 'task_id')
            ->withTimestamps();
    }

####################################### End Relations ###############################################

################################ Accessors and Mutators #############################################

################################ End Accessors and Mutators #########################################

####################################### Helper Methods ##############################################

    /**
     * Check if all dependencies are completed
     */
    public function allDependenciesCompleted(): bool
    {
        $dependencies = $this->dependencies;
        
        if ($dependencies->isEmpty()) {
            return true;
        }

        return $dependencies->every(function ($dependency) {
            return $dependency->status === TaskStatusEnum::Completed;
        });
    }

    /**
     * Get all dependencies recursively (entire dependency tree)
     */
    public function getAllDependenciesRecursively(): array
    {
        $allDependencies = [];
        $this->collectDependenciesRecursively($this, $allDependencies);
        return $allDependencies;
    }

    /**
     * Recursively collect all dependencies
     */
    private function collectDependenciesRecursively(Task $task, array &$collected): void
    {
        $dependencies = $task->dependencies;
        
        foreach ($dependencies as $dependency) {
            // Avoid circular references
            if (!in_array($dependency->id, array_column($collected, 'id'))) {
                $collected[] = $dependency;
                $this->collectDependenciesRecursively($dependency, $collected);
            }
        }
    }

    /**
     * Check if adding a dependency would create a circular dependency
     */
    public function wouldCreateCircularDependency(int $dependencyId): bool
    {
        // If the dependency is the task itself
        if ($this->id === $dependencyId) {
            return true;
        }

        // Check if any of the task's dependents (tasks that depend on this task)
        // would create a circular dependency
        $dependencyTask = static::find($dependencyId);
        if (!$dependencyTask) {
            return false;
        }

        // Get all tasks that depend on the dependency task recursively
        $dependencyDependents = $dependencyTask->getAllDependenciesRecursively();
        
        // Check if current task is in the dependency's dependency tree
        foreach ($dependencyDependents as $dependent) {
            if ($dependent->id === $this->id) {
                return true;
            }
        }

        return false;
    }

####################################### End Helper Methods ##########################################
}
