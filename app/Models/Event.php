<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'service_body_id',
        'day_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function servicebody()
    {
        return $this->belongsTo(ServiceBody::class, 'service_body_id');
    }
}

