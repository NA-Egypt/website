<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{

    protected $fillable = ['title', 'description'];
    public function meetings() {
        return $this->hasMany(Meeting::class);
    }
}
