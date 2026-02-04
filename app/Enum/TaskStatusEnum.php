<?php

namespace App\Enum;

enum TaskStatusEnum: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in-progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Delayed = 'delayed';

}
