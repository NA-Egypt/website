<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>جدول اجتماعات التعافي</title>
    <style>
        body {
            font-family: 'xbriyaz', 'cairo', 'amiri', sans-serif;
            direction: rtl;
            text-align: right;
            color: #333;
            @if($pageSize === 'A5')
                font-size: 7px;
                line-height: 1.2;
            @else
                font-size: 8.5px;
                line-height: 1.3;
            @endif
        }
        .day-section {
            @if($pageSize === 'A5')
                margin-bottom: 6px;
            @else
                margin-bottom: 10px;
            @endif
        }
        .day-title-cell {
            @if($pageSize === 'A5')
                font-size: 11px;
                padding: 3px 6px;
                /* border-right: 2px solid #00698f; */
            @else
                font-size: 13px;
                padding: 4px 8px;
                /* border-right: 3px solid #00698f; */
            @endif
            font-weight: bold;
            background-color: #f2f7fa;
            color: #00698f;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            @if($pageSize === 'A5')
                margin-bottom: 5px;
            @else
                margin-bottom: 8px;
            @endif
        }
        th, td {
            border: 1px solid #ccc;
            @if($pageSize === 'A5')
                padding: 3px 4px;
            @else
                padding: 4px 6px;
            @endif
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        tr {
            page-break-inside: avoid;
        }
        .group-table-section {
            page-break-inside: avoid;
            @if($pageSize === 'A5')
                margin-top: 15px;
            @else
                margin-top: 20px;
            @endif
        }
        .group-section-title {
            @if($pageSize === 'A5')
                font-size: 9px;
                padding-bottom: 2px;
                margin-bottom: 5px;
            @else
                font-size: 11px;
                padding-bottom: 3px;
                margin-bottom: 8px;
            @endif
            font-weight: bold;
            color: #00698f;
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>

    @php
        $satMeetings = $meetingsByDay['السبت'] ?? collect();
        $sunMeetings = $meetingsByDay['الأحد'] ?? collect();
        $monMeetings = $meetingsByDay['الإثنين'] ?? collect();
        $tueMeetings = $meetingsByDay['الثلاثاء'] ?? collect();
        $wedMeetings = $meetingsByDay['الأربعاء'] ?? collect();
        $thuMeetings = $meetingsByDay['الخميس'] ?? collect();
        $friMeetings = $meetingsByDay['الجمعة'] ?? collect();

        // Calculate total meetings count to balance heights
        $totalCount = $satMeetings->count() + $sunMeetings->count() + $monMeetings->count() + $tueMeetings->count() + $wedMeetings->count() + $thuMeetings->count() + $friMeetings->count();
        $half = (int)($totalCount / 2);

        // Sat + Sun + Mon count
        $beforeTueCount = $satMeetings->count() + $sunMeetings->count() + $monMeetings->count();

        // Tuesday splitting index
        $tueCount = $tueMeetings->count();
        $k = 0;
        if ($tueCount > 0) {
            $k = $half - $beforeTueCount;
            if ($k < 1) {
                // If Sat + Sun + Mon is already more than half, Tuesday starts col 2 (so split is 0, Tuesday goes entirely to Col 2)
                $k = 0;
            } elseif ($k >= $tueCount) {
                // If all Tuesday fits in Col 1, keep 1 meeting on Col 2 so Tuesday "continues" to Col 2 as requested
                $k = $tueCount - 1;
            }
        }

        $tuePart1 = $tueCount > 0 && $k > 0 ? $tueMeetings->take($k) : collect();
        $tuePart2 = $tueCount > 0 ? ($k > 0 ? $tueMeetings->skip($k) : $tueMeetings) : collect();
    @endphp

    <table style="width: 100%; border: none; margin: 0; padding: 0; table-layout: fixed;" cellpadding="0" cellspacing="0">
        <tr>
            <!-- Column 1 (Right): Sat, Sun, Mon, Tue Part 1 -->
            <td width="49%" style="vertical-align: top; border: none; padding: 0 0 0 5px; text-align: center;">
                @if($satMeetings->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'السبت', 'meetings' => $satMeetings])
                @endif
                @if($sunMeetings->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'الأحد', 'meetings' => $sunMeetings])
                @endif
                @if($monMeetings->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'الإثنين', 'meetings' => $monMeetings])
                @endif
                @if($tuePart1->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'الثلاثاء', 'meetings' => $tuePart1])
                @endif
            </td>
            <!-- Column spacing gap -->
            <td width="2%" style="border: none;"></td>
            <!-- Column 2 (Left): Tue Part 2, Wed, Thu, Fri -->
            <td width="49%" style="vertical-align: top; border: none; padding: 0 5px 0 0; text-align: center">
                @if($tuePart2->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => $k > 0 ? 'الثلاثاء (تابع)' : 'الثلاثاء', 'meetings' => $tuePart2])
                @endif
                @if($wedMeetings->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'الأربعاء', 'meetings' => $wedMeetings])
                @endif
                @if($thuMeetings->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'الخميس', 'meetings' => $thuMeetings])
                @endif
                @if($friMeetings->isNotEmpty())
                    @include('exports.meetings-day-table-partial', ['dayName' => 'الجمعة', 'meetings' => $friMeetings])
                @endif
            </td>
        </tr>
    </table>

    @if($groups->isNotEmpty())
        <div class="group-table-section">
            <div class="group-section-title">بيانات وتفاصيل اتصال المجموعات المدرجة</div>
            <table>
                <thead>
                    <tr>
                        <th width="10%">المجموعة</th>
                        <th width="10%">ممثل المجموعة</th>
                        <th width="10%">رقم الهاتف</th>
                        <th width="70%">العنوان</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td><strong>{{ $group->ar_name ?: $group->en_name }}</strong></td>
                            <td>{{ $group->ar_gsr_name ?: $group->en_gsr_name ?: '-' }}</td>
                            <td style="direction: ltr; text-align: right;">{{ $group->phone ?: '-' }}</td>
                            <td>{{ $group->ar_address ?: $group->en_address ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
