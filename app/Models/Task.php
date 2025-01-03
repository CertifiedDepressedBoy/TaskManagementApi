<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function project()
    {
        $this->belongsTo(Project::class);
    }
    protected $table = 'tasks';
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'created_by'
    ];
}
