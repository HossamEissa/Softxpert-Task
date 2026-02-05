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
use Illuminate\Support\Collection;

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


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }


    public function taskDependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id')
            ->withTimestamps();
    }

    public function dependents()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependency_id', 'task_id')->withTimestamps();
    }

    ####################################### End Relations ###############################################

    ################################ Accessors and Mutators #############################################

    ################################ End Accessors and Mutators #########################################

    ####################################### Helper Methods ##############################################


    public function allDependenciesCompleted(): bool
    {
        return $this->dependencies->isEmpty() ? true : $this->dependencies->every(fn($dependency) => $dependency->status === TaskStatusEnum::Completed);
    }

    public function getAllDependenciesRecursively(): Collection
    {
        $collected = collect();
        $visited = collect();

        $this->collectDependencies($this, $collected, $visited);

        return $collected;
    }

    private function collectDependencies(Task $task, Collection $collected, Collection $visited): void
    {
        foreach ($task->dependencies as $dependency) {

            if ($visited->contains($dependency->id)) {
                continue;
            }

            $visited->push($dependency->id);
            $collected->push($dependency);

            $this->collectDependencies($dependency, $collected, $visited);
        }
    }


    public function wouldCreateCircularDependency(int $dependencyId): bool
    {
        if ($this->id === $dependencyId) {
            return true;
        }

        $dependencyTask = static::whereId($dependencyId)->first();
        if (!$dependencyTask) {
            return false;
        }

        $allDependencyIds = $dependencyTask->getAllDependenciesRecursively()->pluck('id');
        return $allDependencyIds->contains($this->id);
    }

    ####################################### End Helper Methods ##########################################
}
