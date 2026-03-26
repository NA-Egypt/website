<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    public function serviceBodies() {
        return $this->hasMany(ServiceBody::class);
    }

    public function meetings() {
        return $this->hasMany(Meeting::class);
    }

    public function events() {
        return $this->hasMany(Event::class);
    }
}
