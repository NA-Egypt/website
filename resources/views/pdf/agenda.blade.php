@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <style>
        body {
            font-family: 'xbriyaz', sans-serif;
            direction: {{ $direction }};
            unicode-bidi: embed;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .logo {
            width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .agenda-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 5px;
            margin-top: 15px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: {{ $direction === 'rtl' ? 'right' : 'left' }};
            vertical-align: top;
        }

        table th {
            background-color: #f9f9f9;
            width: 30%;
        }
        
        .content-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
            min-height: 40px;
        }

    </style>
</head>
<body>

@foreach($agendas as $index => $agenda)
    <div class="header">
        @if(file_exists(public_path('assets/images/na.png')))
            <img src="{{ public_path('assets/images/na.png') }}" class="logo" alt="Logo">
        @endif
        <div class="agenda-title">
            {{ __('messages.month_year_agenda', ['month' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'F'), 'year' => \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'Y')]) }} - {{ $agenda->group->{app()->getLocale() . '_name'} }}
        </div>
    </div>

    <div class="section-title">{{ __('messages.group_data') ?? 'Group Data' }}</div>
    <table>
        <tbody>
            <tr>
                <th>{{ __('messages.meetings_per_week') ?? 'Meetings per week' }}</th>
                <td>{{ $agenda->meetings_per_week }}</td>
                <th>{{ __('messages.agenda_date') ?? 'Agenda Date' }}</th>
                <td>{{ \App\Services\DateNumberHelper::translatedFormat($agenda->agenda_date, 'd M Y') }}</td>
            </tr>
            <tr>
                <th>{{ __('messages.service_position') ?? 'Service Position' }}</th>
                <td>{{ $agenda->translated_service_position }}</td>
                <th>{{ __('messages.submitter_name') ?? 'Submitter Name' }}</th>
                <td>{{ $agenda->submitter_name ?: '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('messages.alt_gsr_name') ?? 'Alt. GSR Name' }}</th>
                <td colspan="3">
                    @if($agenda->alt_gsr_position)
                        ({{ $agenda->translated_alt_gsr_position }}) 
                    @endif
                    {{ $agenda->alt_gsr_name ?: '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">{{ __('messages.group_news') ?? 'Group News' }}</div>
    <table>
        <tbody>
            <tr>
                <th>{{ __('messages.new_comers') ?? 'Newcomers' }}</th>
                <td>{{ $agenda->new_comers }}</td>
                <th>{{ __('messages.next_business_meeting') ?? 'Next Business Meeting' }}</th>
                <td>{{ $agenda->next_business_meeting ? \App\Services\DateNumberHelper::translatedFormat($agenda->next_business_meeting, 'd M Y h:i A') : '-' }}</td>
            </tr>
            <tr>
                <th>{{ __('messages.recovery_meetings_changes') ?? 'Recovery Meetings Changes' }}</th>
                <td colspan="3">{{ $agenda->recovery_meetings_changes ? (__('messages.yes') ?? 'Yes') : (__('messages.no') ?? 'No') }}</td>
            </tr>
            <tr>
                <th>{{ __('messages.open_positions') ?? 'Open Positions' }}</th>
                <td colspan="3">{{ $agenda->open_positions ?: '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">{{ __('messages.the_agenda') ?? 'The Agenda' }}</div>
    
    <strong>{{ __('messages.recovery_atmosphere') ?? 'Recovery Atmosphere' }}:</strong>
    <div class="content-box">
        @if(is_array($agenda->recovery_atmosphere))
            {!! implode('<br/><br/>', array_map('nl2br', array_map('e', $agenda->recovery_atmosphere))) ?: '-' !!}
        @else
            {!! nl2br(e($agenda->recovery_atmosphere)) ?: '-' !!}
        @endif
    </div>

    <strong>{{ __('messages.trusted_servants') ?? 'Trusted Servants' }}:</strong>
    <div class="content-box">
        @if(is_array($agenda->trusted_servants))
            {!! implode('<br/><br/>', array_map('nl2br', array_map('e', $agenda->trusted_servants))) ?: '-' !!}
        @else
            {!! nl2br(e($agenda->trusted_servants)) ?: '-' !!}
        @endif
    </div>

    <strong>{{ __('messages.financial_issues') ?? 'Financial Issues' }}:</strong>
    <div class="content-box">
        @if(is_array($agenda->financial_issues))
            {!! implode('<br/><br/>', array_map('nl2br', array_map('e', $agenda->financial_issues))) ?: '-' !!}
        @else
            {!! nl2br(e($agenda->financial_issues)) ?: '-' !!}
        @endif
    </div>

    <strong>{{ __('messages.other_topics') ?? 'Other Topics' }}:</strong>
    <div class="content-box">
        @if(is_array($agenda->other_topics))
            @php
                $pdfLines = [];
                foreach($agenda->other_topics as $item) {
                    if (is_array($item) && isset($item['title'])) {
                        $pdfLines[] = '<strong>' . e($item['title']) . ':</strong><br/>' . nl2br(e($item['content']));
                    } else {
                        $pdfLines[] = nl2br(e($item));
                    }
                }
            @endphp
            {!! implode('<br/><br/>', $pdfLines) ?: '-' !!}
        @else
            {!! nl2br(e($agenda->other_topics)) ?: '-' !!}
        @endif
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
