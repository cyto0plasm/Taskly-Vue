<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drawing extends Model
{
       protected $fillable = [
        'data',
        'version',
    ];

    public function drawable()
    {
        return $this->morphTo();
    }

}
