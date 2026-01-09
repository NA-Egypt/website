<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'dejavu sans', sans-serif; direction: rtl; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; }
        .meta { font-size: 14px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 12px; }
        th { background-color: #f0f0f0; }
        .content { font-size: 14px; line-height: 1.6; }
        .section-title { font-size: 16px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $report->serviceCommittee->ar_name }}</div>
        <div>{{ __('messages.Committee Report') }}</div>
    </div>

    <div class="meta">
        <strong>{{ __('messages.Date') }}:</strong> {{ $report->meeting_date->format('Y-m-d') }} <br>
        <strong>{{ __('messages.Day') }}:</strong> {{ $report->meeting_day_description }}
    </div>

    <div class="section-title">{{ __('messages.Positions Status') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('messages.Position') }}</th>
                <th>{{ __('messages.Status') }}</th>
                <th>{{ __('messages.Election') }}</th>
            </tr>
        </thead>
        <tbody>
            @if($report->positions_status)
                @foreach($report->positions_status as $pos)
                    <tr>
                        <td>{{ $pos['name'] ?? '-' }}</td>
                        <td>{{ $pos['status'] ?? '-' }}</td>
                        <td>{{ !empty($pos['election']) ? __('messages.Open') : '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="section-title">{{ __('messages.Report Body') }}</div>
    <div class="content">
        {!! $report->body !!}
    </div>
</body>
</html>
