<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{

    protected $fillable = ['ar_name', 'en_name', 'description'];
    public function meetings() {
        return $this->belongsToMany(Meeting::class);
    }
}
