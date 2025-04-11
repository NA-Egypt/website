<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['ar_name', 'en_name'];


    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_option');
    }
    
}
