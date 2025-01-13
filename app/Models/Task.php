<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function user()
    {
        $this->belongsTo(User::class,'created_by');
    }
    public function project()
    {
        $this->belongsTo(Project::class,'project_id');
    }
    protected $table = 'tasks';
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assign_to',
    ];
}
