<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Project extends Model
{
    use HasApiTokens;
    public function user(){
        $this->belongsTo(User::class,'created_by');
    }
    public function task()
    {
        $this->hasMany(Task::class);
    }
    protected $table = 'projects';
    protected $fillable = [
        'name' ,
        'description' ,
        'deadline' ,
        'created_by'
    ];
}
