<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class CommitteeReportLog extends Model
{
    public $timestamps = false;

    protected $table = 'committee_report_logs';

    protected $fillable = [
        'committee_report_id',
        'service_committee_id',
        'committee_name',
        'user_id',
        'user_name',
        'user_email',
        'action',
        'summary',
        'details',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function committeeReport()
    {
        return $this->belongsTo(CommitteeReport::class, 'committee_report_id');
    }

    public function serviceCommittee()
    {
        return $this->belongsTo(ServiceCommittee::class, 'service_committee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Record a concise, non-bloated audit log entry for committee reports.
     *
     * @param string $action (created, updated, submitted, approved, returned_to_draft, deleted)
     * @param CommitteeReport|array|object $report
     * @param string $summary
     * @param array $details
     * @return self|null
     */
    public static function record(string $action, $report, string $summary, array $details = []): ?self
    {
        try {
            if (!Schema::hasTable('committee_report_logs')) {
                return null;
            }

            $user = Auth::user();
            $reportId = is_object($report) ? ($report->id ?? null) : ($report['id'] ?? null);
            $committeeId = is_object($report) ? ($report->service_committee_id ?? null) : ($report['service_committee_id'] ?? null);
            
            $committeeName = null;
            if (is_object($report) && isset($report->serviceCommittee)) {
                $committeeName = $report->serviceCommittee->ar_name ?: $report->serviceCommittee->en_name;
            } elseif ($committeeId) {
                $comm = ServiceCommittee::find($committeeId);
                if ($comm) {
                    $committeeName = $comm->ar_name ?: $comm->en_name;
                }
            }

            // Exclude bloated content fields if present
            unset($details['body'], $details['sections'], $details['attended_members']);

            return self::create([
                'committee_report_id' => $reportId,
                'service_committee_id' => $committeeId,
                'committee_name' => $committeeName,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'user_email' => $user ? $user->email : null,
                'action' => $action,
                'summary' => $summary,
                'details' => empty($details) ? null : $details,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string)Request::userAgent(), 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("CommitteeReportLog recording failed: " . $e->getMessage());
            return null;
        }
    }
}

