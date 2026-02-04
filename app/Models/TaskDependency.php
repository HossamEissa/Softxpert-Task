<?php

namespace App\Models;

use App\Traits\DynamicPagination;
use App\Traits\Filterable;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDependency extends Model
{
    use HasFactory, DynamicPagination, Filterable, Searchable, Sortable;

    protected $fillable = [
        'task_id',
        'dependency_id',
    ];

####################################### Relations ###################################################

    /**
     * Get the task that has the dependency
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Get the task that is the dependency
     */
    public function dependency(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'dependency_id');
    }

####################################### End Relations ###############################################

################################ Accessors and Mutators #############################################

################################ End Accessors and Mutators #########################################
}
