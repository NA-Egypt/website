@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html dir="{{ $direction }}" lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <style>
        body { 
            font-family: 'xbriyaz', sans-serif; 
            direction: {{ $direction }};
            unicode-bidi: embed;
            font-size: 14px;
        }
        .page-break { page-break-after: always; }
        .keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 150px; height: auto; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; }
        .meta {
            font-size: 14px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        th, td { border: 1px solid #000; padding: 8px; text-align: {{ $direction === 'rtl' ? 'right' : 'left' }}; font-size: 12px; }
        th { background-color: #f0f0f0; }
        .content-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            min-height: 40px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .content-box p,
        .content-box li,
        .content-box blockquote,
        .content-box table,
        .content-box tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px;
            margin-top: 15px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            page-break-inside: avoid;
            page-break-after: avoid;
            break-inside: avoid;
            break-after: avoid;
        }
        .section-headline,
        .content-box h1,
        .content-box h2,
        .content-box h3,
        .content-box h4,
        .content-box h5,
        .content-box h6 {
            page-break-inside: avoid;
            page-break-after: avoid;
            break-inside: avoid;
            break-after: avoid;
        }
        .report-footer {
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            text-align: center;
            font-style: italic;
            font-size: 12px;
            color: #555;
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>
<body>
    @foreach($reports as $report)
        <div class="keep-together">
            <table style="width: 100%; border: none; margin-bottom: 10px;" autosize="1">
                <tr>
                    <td style="width: 30%; border: none; text-align: left; vertical-align: middle;">
                        @if($direction === 'ltr')
                            @if(file_exists(public_path('assets/images/na.png')))
                                <img src="{{ public_path('assets/images/na.png') }}" style="max-height: 60px; width: auto;" alt="NA Logo">
                            @endif
                        @else
                            @if($report->serviceCommittee && $report->serviceCommittee->logo && file_exists(public_path('storage/' . $report->serviceCommittee->logo)))
                                <img src="{{ public_path('storage/' . $report->serviceCommittee->logo) }}" style="max-height: 60px; width: auto;" alt="Committee Logo">
                            @endif
                        @endif
                    </td>
                    <td style="width: 40%; border: none; text-align: center; vertical-align: middle;">
                        <div style="font-size: 18px; font-weight: bold;">{{ $report->serviceCommittee ? ($report->serviceCommittee->{app()->getLocale() . '_name'} ?? $report->serviceCommittee->ar_name) : '' }}</div>
                        <div style="font-size: 14px; color: #555; margin-top: 5px;">
                            {{ ($report->serviceCommittee && $report->serviceCommittee->isWorkgroup()) ? (__('messages.Workgroup Report') ?? 'Workgroup Report') : (__('messages.Committee Reports') ?? 'Committee Report') }}
                        </div>
                    </td>
                    <td style="width: 30%; border: none; text-align: right; vertical-align: middle;">
                        @if($direction === 'ltr')
                            @if($report->serviceCommittee && $report->serviceCommittee->logo && file_exists(public_path('storage/' . $report->serviceCommittee->logo)))
                                <img src="{{ public_path('storage/' . $report->serviceCommittee->logo) }}" style="max-height: 60px; width: auto;" alt="Committee Logo">
                            @endif
                        @else
                            @if(file_exists(public_path('assets/images/na.png')))
                                <img src="{{ public_path('assets/images/na.png') }}" style="max-height: 60px; width: auto;" alt="NA Logo">
                            @endif
                        @endif
                    </td>
                </tr>
            </table>
            <div style="border-bottom: 2px solid #000; margin-bottom: 15px; margin-top: 5px;"></div>

            <div class="meta">
                <strong>{{ __('messages.Report Date') ?? 'Report Date' }}:</strong> {{ $report->report_date ? \App\Services\DateNumberHelper::translatedFormat($report->report_date, 'Y-m-d') : \App\Services\DateNumberHelper::translatedFormat($report->created_at, 'Y-m-d') }} <br>
                <strong>{{ __('messages.Meeting Date') ?? 'Meeting Date' }}:</strong> {{ \App\Services\DateNumberHelper::translatedFormat($report->meeting_date, 'Y-m-d') }} <br>
                <strong>{{ __('messages.Day') ?? 'Day' }}:</strong> {{ $report->meeting_day_description }}
                @if($report->is_exceptional)
                    <br><strong>{{ __('messages.Exceptional Meeting') ?? 'Exceptional Meeting' }}:</strong> <span style="color: red; font-weight: bold;">{{ __('messages.yes') ?? 'Yes' }}</span>
                @endif
            </div>
        </div>

        <div class="keep-together">
            <div class="section-title" keep-with-table="true">{{ __('messages.Positions Status') ?? 'Positions Status' }}</div>
            <table autosize="1">
                <thead>
                    <tr>
                        <th>{{ __('messages.Position') ?? 'Position' }}</th>
                        <th>{{ __('messages.Member Name') ?? 'Member Name' }}</th>
                        <th>{{ __('messages.Status') ?? 'Status' }}</th>
                        <th>{{ __('messages.Election') ?? 'Election' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($report->positions_status)
                        @foreach($report->positions_status as $pos)
                            <tr>
                                <td>{{ $pos['name'] ?? '-' }}</td>
                                <td>{{ $pos['member_name'] ?? '-' }}</td>
                                <td>{{ $pos['status'] ?? '-' }}</td>
                                <td>{{ !empty($pos['election']) ? (__('messages.Open') ?? 'Open') : '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">{{ __('messages.No positions data') ?? 'No positions data' }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($report->attended_members)
            <div class="keep-together">
                <div class="section-title">{{ __('messages.Attended Members') ?? 'Attended Members' }}</div>
                <div class="content-box">
                    <div class="section-body">
                        {!! nl2br(e($report->attended_members)) !!}
                    </div>
                </div>
            </div>
        @endif

        @php
            $bodySections = $report->body_sections;
        @endphp
        @if(!empty($bodySections))
            @foreach($bodySections as $section)
                @if($loop->first)
                    <div class="keep-together">
                        <div class="section-title">{{ __('messages.Report Body') ?? 'Report Body' }}</div>
                        <div class="content-box" style="margin-bottom: 15px;">
                            @if(!empty($section['headline']))
                                <div class="section-headline" style="font-weight: bold; font-size: 13px; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 2px; color: #333;">
                                    {{ $section['headline'] }}
                                </div>
                            @endif
                            <div class="section-body">
                                {!! $section['content'] !!}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="content-box keep-together" style="margin-bottom: 15px;">
                        @if(!empty($section['headline']))
                            <div class="section-headline" style="font-weight: bold; font-size: 13px; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 2px; color: #333;">
                                {{ $section['headline'] }}
                            </div>
                        @endif
                        <div class="section-body">
                            {!! $section['content'] !!}
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="keep-together">
                <div class="section-title">{{ __('messages.Report Body') ?? 'Report Body' }}</div>
            </div>
        @endif

        @if($report->footer || ($report->serviceCommittee && $report->serviceCommittee->default_footer))
            <div class="report-footer keep-together">
                <div>{{ $report->footer ?: $report->serviceCommittee->default_footer }}</div>
            </div>
        @endif
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
