<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBodyAgendaAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_body_agenda_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function serviceBodyAgenda()
    {
        return $this->belongsTo(ServiceBodyAgenda::class);
    }
}
