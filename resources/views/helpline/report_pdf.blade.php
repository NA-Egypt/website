<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير خطوط المساعدة الشهري</title>
    <style>
        body {
            font-family: 'cairo', sans-serif;
            direction: rtl;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.5;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-align: center;
        }

        .header-subtitle {
            font-size: 13px;
            color: #475569;
            text-align: center;
            margin-top: 4px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            background-color: #f1f5f9;
            padding: 6px 10px;
            border-right: 4px solid #1e3a8a;
            margin-top: 18px;
            margin-bottom: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.data-table th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #cbd5e1;
            padding: 7px;
            text-align: right;
        }

        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 11.5px;
            text-align: right;
        }

        .kpi-grid {
            width: 100%;
            margin-bottom: 15px;
        }

        .kpi-cell {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            width: 20%;
        }

        .kpi-num {
            font-size: 18px;
            font-weight: bold;
            color: #1d4ed8;
            margin-top: 4px;
        }

        .kpi-label {
            font-size: 11px;
            color: #64748b;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            font-size: 10.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 20%; text-align: right; vertical-align: middle;">
                @if(file_exists(public_path('assets/images/na.png')))
                    <img src="{{ public_path('assets/images/na.png') }}" style="max-height: 55px; width: auto;" alt="NA Logo">
                @endif
            </td>
            <td style="width: 60%; vertical-align: middle;">
                <div class="header-title">زمالة المدمنين المجهولين - مصر</div>
                <div class="header-subtitle">لجنة العلاقات العامة &bull; مجموعة خطوط المساعدة</div>
                <div class="header-subtitle" style="font-weight: bold; color: #1e40af; margin-top: 6px;">
                    {{ $cycleTitle }}
                </div>
            </td>
            <td style="width: 20%; text-align: left; vertical-align: middle; font-size: 11px; color: #64748b;">
                تاريخ الإصدار:<br>
                <strong>{{ date('Y-m-d') }}</strong>
            </td>
        </tr>
    </table>

    {{-- Period Range --}}
    <p style="margin-bottom: 12px; font-size: 11.5px; color: #475569;">
        <strong>فترة التقرير:</strong> من {{ $start->format('Y-m-d') }} إلى {{ $end->format('Y-m-d') }} (إعادة التعيين التلقائي مع نهاية أول ثلاثاء من الشهر).
    </p>

    {{-- KPI Cards Table --}}
    <table class="kpi-grid">
        <tr>
            <td class="kpi-cell">
                <div class="kpi-label">إجمالي المكالمات</div>
                <div class="kpi-num">{{ $reportData['total_calls'] }}</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-label">أقل من 5 دقائق</div>
                <div class="kpi-num" style="color: #059669;">{{ $reportData['duration_stats']['less_than_5'] }} <span style="font-size: 10px; font-weight: normal;">({{ $reportData['duration_stats']['less_than_5_pct'] }}%)</span></div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-label">أكثر من 5 دقائق</div>
                <div class="kpi-num" style="color: #0284c7;">{{ $reportData['duration_stats']['more_than_5'] }} <span style="font-size: 10px; font-weight: normal;">({{ $reportData['duration_stats']['more_than_5_pct'] }}%)</span></div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-label">تحويل لخطوة 12</div>
                <div class="kpi-num" style="color: #d97706;">{{ $reportData['step_12_count'] }} <span style="font-size: 10px; font-weight: normal;">({{ $reportData['step_12_pct'] }}%)</span></div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-label">مناقشة بالاجتماع</div>
                <div class="kpi-num" style="color: #7c3aed;">{{ $reportData['discuss_meeting_count'] }}</div>
            </td>
        </tr>
    </table>

    {{-- Shift Breakdown --}}
    <div class="section-title">1. توزيع المكالمات على الورديات والتوقيت</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 60%;">الوردية / الفترة الزمنية</th>
                <th style="width: 20%; text-align: center;">عدد المكالمات</th>
                <th style="width: 20%; text-align: center;">النسبة المئوية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['shift_counts'] as $shift => $count)
                @php
                    $pct = $reportData['total_calls'] > 0 ? round(($count / $reportData['total_calls']) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $shift }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $count }}</td>
                    <td style="text-align: center;">{{ $pct }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Caller Types & Referral Sources (Side-by-side) --}}
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <div class="section-title">2. أبرز فئات المتصلين</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>الفئة</th>
                            <th style="text-align: center;">العدد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($reportData['caller_type_counts'], 0, 7, true) as $type => $count)
                            <tr>
                                <td>{{ $type }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align: center; color: #94a3b8;">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width: 2%; border: none;"></td>
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <div class="section-title">3. أبرز مصادر المعرفة بالزمالة</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>المصدر</th>
                            <th style="text-align: center;">العدد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(array_slice($reportData['referral_counts'], 0, 7, true) as $src => $count)
                            <tr>
                                <td>{{ $src }}</td>
                                <td style="text-align: center; font-weight: bold;">{{ $count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align: center; color: #94a3b8;">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    {{-- Calls to discuss in meeting --}}
    @if($reportData['discuss_meeting_count'] > 0)
        <div class="section-title">4. مكالمات تتطلب المناقشة في اجتماع خطوط المساعدة القادم</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">التاريخ</th>
                    <th style="width: 20%;">الوردية</th>
                    <th style="width: 20%;">المتطوع</th>
                    <th style="width: 45%;">نبذة عن المكالمة والموضوع المطلوب مناقشته</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['discuss_calls'] as $dc)
                    <tr>
                        <td>{{ $dc->call_date->format('Y-m-d') }}</td>
                        <td>{{ $dc->call_time_shift }}</td>
                        <td>{{ $dc->effective_volunteer_name }}</td>
                        <td>{{ $dc->call_brief }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        تم استخراج هذا التقرير تلقائياً من نظام إدارة مكالمات خطوط المساعدة - NA Egypt &bull; {{ date('Y-m-d H:i') }}
    </div>

</body>
</html>
