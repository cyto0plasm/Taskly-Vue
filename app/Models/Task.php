<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'type',
        'creator_id',
        'project_id',
    ];
    protected $casts = [
    'due_date' => 'date',
];

    public function creator()
{
    return $this->belongsTo(User::class, 'creator_id');
}

public function project()
{
    return $this->belongsTo(Project::class, 'project_id');
}
public function drawing()
{
    return $this->morphOne(Drawing::class, 'drawable');
}
}
