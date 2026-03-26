<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function day() {
        return $this->belongsTo(Day::class);
    }

    public function servicebody() {
        return $this->belongsTo(ServiceBody::class);
    }
}
