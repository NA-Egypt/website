<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_committee_id',
        'meeting_date',
        'meeting_day_description',
        'body',
        'positions_status',
        'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'positions_status' => 'array', // automatically serialize/unserialize JSON
    ];

    public function serviceCommittee()
    {
        return $this->belongsTo(ServiceCommittee::class);
    }

    public function attachments()
    {
        return $this->hasMany(CommitteeReportAttachment::class);
    }
}
