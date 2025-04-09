<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_option');
    }
    
}
