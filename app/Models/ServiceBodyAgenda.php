<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatedDateTime;

class ServiceBodyAgenda extends Model
{
    use FormatedDateTime;

    protected $fillable = [
        'service_body_id',
        'agenda_date',
        'meeting_date',
        'groups_joined',
        'body',
        'status',
        'is_exceptional',
    ];

    protected $casts = [
        'agenda_date' => 'date',
        'meeting_date' => 'date',
        'groups_joined' => 'array',
        'body' => 'array',
        'is_exceptional' => 'boolean',
    ];

    public function serviceBody()
    {
        return $this->belongsTo(ServiceBody::class);
    }
}
