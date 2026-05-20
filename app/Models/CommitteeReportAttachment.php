<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_report_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function committeeReport()
    {
        return $this->belongsTo(CommitteeReport::class);
    }
}
