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
        <div class="header">
            @if(file_exists(public_path('assets/images/na.png')))
                <img src="{{ public_path('assets/images/na.png') }}" class="logo" alt="Logo">
            @endif
            <div class="title">{{ $report->serviceCommittee->{app()->getLocale() . '_name'} ?? $report->serviceCommittee->ar_name }}</div>
            <div>{{ __('messages.Committee Reports') ?? 'Committee Report' }}</div>
        </div>

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
        <div class="content-box">
            {!! $report->body !!}
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
