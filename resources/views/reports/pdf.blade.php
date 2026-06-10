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
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .logo { width: 150px; height: auto; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; }
        .meta { font-size: 14px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: {{ $direction === 'rtl' ? 'right' : 'left' }}; font-size: 12px; }
        th { background-color: #f0f0f0; }
        .content-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; min-height: 40px; }
        .section-title { font-size: 16px; font-weight: bold; background-color: #f0f0f0; padding: 5px; margin-top: 15px; margin-bottom: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    @foreach($reports as $report)
        <table style="width: 100%; border: none; margin-bottom: 10px;">
            <tr>
                <td style="width: 30%; border: none; text-align: left; vertical-align: middle;">
                    @if($direction === 'ltr')
                        @if(file_exists(public_path('assets/images/na.png')))
                            <img src="{{ public_path('assets/images/na.png') }}" style="max-height: 60px; width: auto;" alt="NA Logo">
                        @endif
                    @else
                        @if($report->serviceCommittee->logo && file_exists(public_path('storage/' . $report->serviceCommittee->logo)))
                            <img src="{{ public_path('storage/' . $report->serviceCommittee->logo) }}" style="max-height: 60px; width: auto;" alt="Committee Logo">
                        @endif
                    @endif
                </td>
                <td style="width: 40%; border: none; text-align: center; vertical-align: middle;">
                    <div style="font-size: 18px; font-weight: bold;">{{ $report->serviceCommittee->{app()->getLocale() . '_name'} ?? $report->serviceCommittee->ar_name }}</div>
                    <div style="font-size: 14px; color: #555; margin-top: 5px;">{{ __('messages.Committee Reports') ?? 'Committee Report' }}</div>
                </td>
                <td style="width: 30%; border: none; text-align: right; vertical-align: middle;">
                    @if($direction === 'ltr')
                        @if($report->serviceCommittee->logo && file_exists(public_path('storage/' . $report->serviceCommittee->logo)))
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
            <strong>{{ __('messages.Report Date') ?? 'Report Date' }}:</strong> {{ $report->report_date ? $report->report_date->format('Y-m-d') : $report->created_at->format('Y-m-d') }} <br>
            <strong>{{ __('messages.Meeting Date') ?? 'Meeting Date' }}:</strong> {{ $report->meeting_date->format('Y-m-d') }} <br>
            <strong>{{ __('messages.Day') ?? 'Day' }}:</strong> {{ $report->meeting_day_description }}
            @if($report->is_exceptional)
                <br><strong>{{ __('messages.Exceptional Meeting') ?? 'Exceptional Meeting' }}:</strong> <span style="color: red; font-weight: bold;">{{ __('messages.yes') ?? 'Yes' }}</span>
            @endif
        </div>

        <div class="section-title">{{ __('messages.Positions Status') ?? 'Positions Status' }}</div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.Position') ?? 'Position' }}</th>
                    <th>{{ __('messages.Status') ?? 'Status' }}</th>
                    <th>{{ __('messages.Election') ?? 'Election' }}</th>
                </tr>
            </thead>
            <tbody>
                @if($report->positions_status)
                    @foreach($report->positions_status as $pos)
                        <tr>
                            <td>{{ $pos['name'] ?? '-' }}</td>
                            <td>{{ $pos['status'] ?? '-' }}</td>
                            <td>{{ !empty($pos['election']) ? (__('messages.Open') ?? 'Open') : '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3">{{ __('messages.No positions data') ?? 'No positions data' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if($report->attended_members)
            <div class="section-title">{{ __('messages.Attended Members') ?? 'Attended Members' }}</div>
            <div class="content-box">
                {!! nl2br(e($report->attended_members)) !!}
            </div>
        @endif

        <div class="section-title">{{ __('messages.Report Body') ?? 'Report Body' }}</div>
        @foreach($report->body_sections as $section)
            <div class="content-box" style="margin-bottom: 15px;">
                @if(!empty($section['headline']))
                    <div style="font-weight: bold; font-size: 13px; margin-bottom: 5px; border-bottom: 1px solid #ddd; padding-bottom: 2px; color: #333;">
                        {{ $section['headline'] }}
                    </div>
                @endif
                <div>
                    {!! $section['content'] !!}
                </div>
            </div>
        @endforeach

        @if($report->footer || ($report->serviceCommittee && $report->serviceCommittee->default_footer))
            <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; text-align: center; font-style: italic; font-size: 12px; color: #555;">
                {{ $report->footer ?: $report->serviceCommittee->default_footer }}
            </div>
        @endif
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
