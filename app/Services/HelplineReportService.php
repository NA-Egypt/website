<?php

namespace App\Services;

use App\Models\CommitteeReport;
use App\Models\HelplineCall;
use App\Models\HelplineVolunteer;
use App\Models\ServiceCommittee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HelplineReportService
{
    /**
     * Predefined Call Shifts
     */
    public const SHIFTS = [
        '10:00 AM - 12:00 PM',
        '12:00 PM - 2:00 PM',
        '2:00 PM - 4:00 PM',
        '4:00 PM - 6:00 PM',
        '6:00 PM - 8:00 PM',
        '8:00 PM - 10:00 PM',
        '10:00 PM - 12:00 AM',
    ];

    /**
     * Predefined Caller Types
     */
    public const CALLER_TYPES = [
        'أعضاء محتملة',
        'عضو محتمل منعزل',
        'بيانات اجتماعات',
        'عضو حالي',
        'معلومات عن الزمالة',
        'أهالي وأقارب المدمنين',
        'عضو حالي منعزل',
        'معلومات عن زمالات أخرى',
        'مكالمة بالخطأ',
        'محولة للجنة العلاقات العامة',
        'أخرى',
    ];

    /**
     * Predefined Referral Sources
     */
    public const REFERRAL_SOURCES = [
        'جدول الاجتماعات',
        'صديق',
        'الموقع الالكتروني',
        'ملصقات الزمالة',
        'عضو حالي',
        'بحث جوجل',
        'طبيب',
        'مكان علاجي',
        'لجنة المستشفيات',
        'صانع محتوى',
        'Yellow Pages',
        'Facebook',
        'TikTok',
        'ChatGPT',
        'Instagram',
        'YouTube',
        'أخرى',
    ];

    /**
     * Get the first Tuesday of a specific month and year at 23:59:59.
     */
    public function getFirstTuesdayOfMonth(int $year, int $month): Carbon
    {
        $date = Carbon::create($year, $month, 1, 0, 0, 0);
        // Advance to first Tuesday
        while ($date->dayOfWeek !== Carbon::TUESDAY) {
            $date->addDay();
        }
        return $date->setTime(23, 59, 59);
    }

    /**
     * Determine the active cycle window for a given date (default: now).
     * The cycle resets every first Tuesday by the end of that day.
     */
    public function getCurrentCycleWindow(?Carbon $now = null): array
    {
        $now = $now ?: Carbon::now();
        $firstTuesdayThisMonth = $this->getFirstTuesdayOfMonth($now->year, $now->month);

        if ($now->lte($firstTuesdayThisMonth)) {
            // We are before or on the first Tuesday of this month:
            // Active cycle ends on this first Tuesday, and started on previous month's first Tuesday.
            $prevMonth = $now->copy()->subMonth();
            $cycleStart = $this->getFirstTuesdayOfMonth($prevMonth->year, $prevMonth->month);
            $cycleEnd = $firstTuesdayThisMonth;
        } else {
            // We have passed the first Tuesday of this month:
            // Active cycle started on this first Tuesday, and ends on next month's first Tuesday.
            $nextMonth = $now->copy()->addMonth();
            $cycleStart = $firstTuesdayThisMonth;
            $cycleEnd = $this->getFirstTuesdayOfMonth($nextMonth->year, $nextMonth->month);
        }

        return [
            'start' => $cycleStart,
            'end' => $cycleEnd,
            'key' => $cycleStart->format('Y-m-d') . '_' . $cycleEnd->format('Y-m-d'),
            'label' => 'دورة ' . $cycleEnd->translatedFormat('F Y') . ' (حتى ' . $cycleEnd->format('Y-m-d') . ')',
        ];
    }

    /**
     * Get cycle options for the dropdown selector.
     * Always includes the current cycle, and only includes past cycles that have calls.
     */
    public function getAvailableCycles(int $count = 12, bool $onlyWithCalls = true): array
    {
        $cycles = [];
        $current = $this->getCurrentCycleWindow();
        $cursorEnd = $current['end']->copy();

        for ($i = 0; $i < $count; $i++) {
            // End of this cycle
            $end = $cursorEnd->copy();
            // Start is the first Tuesday of previous month
            $prev = $end->copy()->subDays(15); // safely go to previous month
            $start = $this->getFirstTuesdayOfMonth($prev->year, $prev->month);

            $key = $start->format('Y-m-d') . '_' . $end->format('Y-m-d');
            $label = ($i === 0 ? 'الدورة الحالية: ' : 'دورة ') . $end->translatedFormat('F Y') . ' (' . $start->format('d/m') . ' إلى ' . $end->format('d/m/Y') . ')';

            $isCurrent = ($i === 0);

            if ($isCurrent) {
                $cycles[$key] = [
                    'start' => $start,
                    'end' => $end,
                    'key' => $key,
                    'label' => $label,
                    'is_current' => true,
                ];
            } elseif ($onlyWithCalls) {
                $hasCalls = HelplineCall::where('entry_time', '>=', $start)
                    ->where('entry_time', '<=', $end)
                    ->exists();

                if ($hasCalls) {
                    $cycles[$key] = [
                        'start' => $start,
                        'end' => $end,
                        'key' => $key,
                        'label' => $label,
                        'is_current' => false,
                    ];
                }
            } else {
                $cycles[$key] = [
                    'start' => $start,
                    'end' => $end,
                    'key' => $key,
                    'label' => $label,
                    'is_current' => false,
                ];
            }

            // Step backward to previous cycle end
            $cursorEnd = $start->copy();
        }

        return $cycles;
    }

    /**
     * Aggregate report data for a specific date range.
     */
    public function getReportData(?Carbon $start = null, ?Carbon $end = null): array
    {
        $query = HelplineCall::query();
        if ($start) {
            $query->where('entry_time', '>=', $start);
        }
        if ($end) {
            $query->where('entry_time', '<=', $end);
        }

        $calls = $query->with('volunteer')->latest('entry_time')->get();
        $totalCalls = $calls->count();

        // 1. Duration stats
        $durationLess5 = $calls->where('duration', 'less_than_5')->count();
        $durationMore5 = $calls->where('duration', 'more_than_5')->count();
        $durationStats = [
            'less_than_5' => $durationLess5,
            'more_than_5' => $durationMore5,
            'less_than_5_pct' => $totalCalls > 0 ? round(($durationLess5 / $totalCalls) * 100, 1) : 0,
            'more_than_5_pct' => $totalCalls > 0 ? round(($durationMore5 / $totalCalls) * 100, 1) : 0,
        ];

        // 2. Key Flags
        $step12Count = $calls->where('is_step_12', true)->count();
        $discussMeetingCount = $calls->where('discuss_in_meeting', true)->count();

        // 3. Shifts distribution
        $shiftCounts = [];
        foreach (self::SHIFTS as $s) {
            $shiftCounts[$s] = 0;
        }
        foreach ($calls as $call) {
            $s = $call->call_time_shift;
            if (!isset($shiftCounts[$s])) {
                $shiftCounts[$s] = 0;
            }
            $shiftCounts[$s]++;
        }

        // 4. Caller Types distribution
        $callerTypeCounts = [];
        foreach ($calls as $call) {
            $label = $call->effective_caller_type;
            $callerTypeCounts[$label] = ($callerTypeCounts[$label] ?? 0) + 1;
        }
        arsort($callerTypeCounts);

        // 5. Referral Sources distribution
        $referralCounts = [];
        foreach ($calls as $call) {
            $label = $call->effective_referral_source;
            $referralCounts[$label] = ($referralCounts[$label] ?? 0) + 1;
        }
        arsort($referralCounts);

        // 6. Volunteers distribution
        $volunteerCounts = [];
        foreach ($calls as $call) {
            $vName = $call->effective_volunteer_name;
            $volunteerCounts[$vName] = ($volunteerCounts[$vName] ?? 0) + 1;
        }
        arsort($volunteerCounts);

        // 7. Calls flagged to discuss in meeting
        $discussCalls = $calls->where('discuss_in_meeting', true);

        return [
            'total_calls' => $totalCalls,
            'duration_less_than_5' => $durationLess5,
            'duration_more_than_5' => $durationMore5,
            'step_12_calls' => $step12Count,
            'discuss_in_meeting_calls' => $discussMeetingCount,
            'duration_stats' => $durationStats,
            'step_12_count' => $step12Count,
            'step_12_pct' => $totalCalls > 0 ? round(($step12Count / $totalCalls) * 100, 1) : 0,
            'discuss_meeting_count' => $discussMeetingCount,
            'discuss_meeting_pct' => $totalCalls > 0 ? round(($discussMeetingCount / $totalCalls) * 100, 1) : 0,
            'shift_counts' => $shiftCounts,
            'caller_type_counts' => $callerTypeCounts,
            'referral_counts' => $referralCounts,
            'volunteer_counts' => $volunteerCounts,
            'discuss_calls' => $discussCalls,
            'calls' => $calls,
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Create or update a draft Workgroup CommitteeReport for Helplines Workgroup (ID 85),
     * enabling the PR Committee (ID 1) to embed it directly into their official monthly report.
     */
    public function syncToWorkgroupReport(Carbon $start, Carbon $end, ?int $userId = null): CommitteeReport
    {
        // Helplines Workgroup ID: 85 (parent: PR Committee ID 1)
        $helplinesWorkgroup = ServiceCommittee::where('email', 'phone@naegypt.org')->first()
            ?? ServiceCommittee::find(85);

        if (!$helplinesWorkgroup) {
            throw new \RuntimeException('Helplines Workgroup entity not found in database.');
        }

        $reportData = $this->getReportData($start, $end);

        // Format structured body sections for the report
        $bodySections = [
            [
                'headline' => 'تقرير مكالمات خط المساعدة (دورة ' . $end->translatedFormat('F Y') . ')',
                'content' => sprintf(
                    "<p><strong>الفترة من:</strong> %s &nbsp;|&nbsp; <strong>إلى:</strong> %s</p>" .
                    "<ul>" .
                    "<li><strong>إجمالي المكالمات المستلمة:</strong> %d مكالمة</li>" .
                    "<li><strong>المكالمات أقل من 5 دقائق:</strong> %d (%.1f%%)</li>" .
                    "<li><strong>المكالمات أكثر من 5 دقائق:</strong> %d (%.1f%%)</li>" .
                    "<li><strong>مكالمات محولة للخطوة 12:</strong> %d (%.1f%%)</li>" .
                    "<li><strong>مكالمات تتطلب مناقشة بالاجتماع القادم:</strong> %d</li>" .
                    "</ul>",
                    $start->format('Y-m-d'),
                    $end->format('Y-m-d'),
                    $reportData['total_calls'],
                    $reportData['duration_stats']['less_than_5'],
                    $reportData['duration_stats']['less_than_5_pct'],
                    $reportData['duration_stats']['more_than_5'],
                    $reportData['duration_stats']['more_than_5_pct'],
                    $reportData['step_12_count'],
                    $reportData['step_12_pct'],
                    $reportData['discuss_meeting_count']
                ),
            ],
            [
                'headline' => 'توزيع المكالمات حسب الفترات الزمنية (الورديات)',
                'content' => (function () use ($reportData) {
                    $html = '<table class="table table-bordered table-sm"><thead><tr><th>الوردية</th><th>عدد المكالمات</th></tr></thead><tbody>';
                    foreach ($reportData['shift_counts'] as $shift => $cnt) {
                        $html .= "<tr><td>{$shift}</td><td>{$cnt}</td></tr>";
                    }
                    $html .= '</tbody></table>';
                    return $html;
                })(),
            ],
            [
                'headline' => 'أبرز فئات المتصلين ومصادر المعرفة',
                'content' => (function () use ($reportData) {
                    $html = '<p><strong>أعلى فئات المتصلين:</strong></p><ul>';
                    $i = 0;
                    foreach ($reportData['caller_type_counts'] as $type => $cnt) {
                        if ($i++ >= 5) break;
                        $html .= "<li>{$type}: {$cnt} مكالمة</li>";
                    }
                    $html .= '</ul><p><strong>أعلى مصادر معرفة بالزمالة:</strong></p><ul>';
                    $j = 0;
                    foreach ($reportData['referral_counts'] as $ref => $cnt) {
                        if ($j++ >= 5) break;
                        $html .= "<li>{$ref}: {$cnt} مكالمة</li>";
                    }
                    $html .= '</ul>';
                    return $html;
                })(),
            ],
        ];

        // Check if draft report already exists for this meeting date / end date
        $meetingDate = $end->toDateString();
        $report = CommitteeReport::where('service_committee_id', $helplinesWorkgroup->id)
            ->where('meeting_date', $meetingDate)
            ->where('status', 'draft')
            ->first();

        if (!$report) {
            $report = new CommitteeReport();
            $report->service_committee_id = $helplinesWorkgroup->id;
            $report->meeting_date = $meetingDate;
            $report->meeting_day_description = 'اجتماع مجموعة خطوط المساعدة الشهري';
            $report->report_date = now()->toDateString();
            $report->status = 'draft';
        }

        $report->body = json_encode($bodySections, JSON_UNESCAPED_UNICODE);
        $report->footer = 'تم إنشاء التقرير آلياً عبر نظام إدارة مكالمات خط المساعدة NA Egypt';
        $report->save();

        return $report;
    }
}
