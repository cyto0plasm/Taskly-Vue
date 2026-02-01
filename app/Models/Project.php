<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'creator_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    //  Each project has many tasks
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    //  The user who created / owns the project
    public function owner()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    //  Many users can collaborate on the same project
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}