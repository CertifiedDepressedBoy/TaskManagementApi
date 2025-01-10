<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class task_assignment extends Model
{
    protected $table = 'task_assignments';
    protected $fillable = [
        'task_id',
        'user_id',
        'status'
    ];
}
