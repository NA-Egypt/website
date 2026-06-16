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
            font-size: 11px;
            line-height: 1.4;
        }
        .day-section {
            margin-bottom: 15px;
        }
        .day-title {
            font-size: 12px;
            font-weight: bold;
            background-color: #f2f7fa;
            color: #00698f;
            padding: 5px 10px;
            border-right: 3px solid #00698f;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: right;
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
        .group-table-section {
            margin-top: 25px;
            page-break-before: always;
        }
        .group-section-title {
            font-size: 13px;
            font-weight: bold;
            color: #00698f;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

    @foreach($meetingsByDay as $dayName => $meetings)
        <div class="day-section">
            <div class="day-title">{{ $dayName }}</div>
            <table>
                <thead>
                    <tr>
                        <th width="35%">اسم المجموعة</th>
                        @if(in_array('topic', $fields))
                            <th width="25%">موضوع الاجتماع</th>
                        @endif
                        @if(in_array('time', $fields))
                            <th width="20%">الوقت</th>
                        @endif
                        @if(in_array('type', $fields))
                            <th width="10%">نوع الاجتماع</th>
                        @endif
                        @if(in_array('lang', $fields))
                            <th width="10%">اللغة</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($meetings as $meeting)
                        <tr>
                            <td><strong>{{ $meeting->group->ar_name ?: $meeting->group->en_name }}</strong></td>
                            @if(in_array('topic', $fields))
                                <td>
                                    @if($meeting->topic)
                                        {{ $meeting->topic->ar_name ?: $meeting->topic->en_name }}
                                    @elseif($meeting->topics->isNotEmpty())
                                        {{ implode('، ', $meeting->topics->pluck('ar_name')->filter()->toArray()) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            @if(in_array('time', $fields))
                                <td style="direction: ltr; text-align: right;">
                                    @php
                                        $startTimeStr = $meeting->formatted_start_time;
                                        $endTimeStr = $meeting->formatted_end_time;
                                        $startTimeAr = str_replace(['AM', 'PM'], ['ص', 'م'], $startTimeStr);
                                        $endTimeAr = str_replace(['AM', 'PM'], ['ص', 'م'], $endTimeStr);
                                    @endphp
                                    من {{ $startTimeAr }} إلى {{ $endTimeAr }}
                                </td>
                            @endif
                            @if(in_array('type', $fields))
                                <td>
                                    {{ $meeting->type === 'open' ? 'مفتوح للزوار' : ($meeting->type === 'closed' ? 'مغلق للمدمنين' : $meeting->type) }}
                                </td>
                            @endif
                            @if(in_array('lang', $fields))
                                <td>
                                    {{ $meeting->lang === 'arabic' ? 'العربية' : ($meeting->lang === 'english' ? 'الإنجليزية' : $meeting->lang) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($groups->isNotEmpty())
        <div class="group-table-section">
            <div class="group-section-title">بيانات وتفاصيل اتصال المجموعات المدرجة</div>
            <table>
                <thead>
                    <tr>
                        <th width="30%">اسم المجموعة</th>
                        <th width="20%">ممثل المجموعة (GSR)</th>
                        <th width="20%">رقم الهاتف</th>
                        <th width="30%">العنوان</th>
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
