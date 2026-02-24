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
        'end_date'   => 'date',
    ];

    // tasks inside this project
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // project owner (creator)
    public function owner()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // collaborators (many-to-many)
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }
    public function drawing()
{
    return $this->morphOne(Drawing::class, 'drawable');
}
}
