@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
@php($filters = request()->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']))
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="ar" />
    <style>
        body, table, th, td {
            font-family: 'xbriyaz', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #444444;
            padding: 6px;
            text-align: right;
        }
        tbody.each-meeting {
            border-bottom: 2px solid #000;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center">
        {{ __('messages.Recovery Meetings') }} 
        {{ isset($filters['day']) && $filters['day'] !== '' ? trans(strtolower($filters['day'])) : __('messages.All Days') }}
            @if(!empty($filters['group']))
                - {{ __('messages.Group') }}: {{ $filters['group'] }}
            @endif
            @if(!empty($filters['serviceBody']))
                - {{ $filters['serviceBody'] }}
            @endif
            @if(!empty($filters['neighborhood']))
                - {{ $filters['neighborhood'] }}
            @endif
            @if(!empty($filters['type']))
                - {{ __("messages." . $filters['type']) }}
            @endif
            @if(!empty($filters['city']))
                - {{ $filters['city'] }}
            @endif
        
    </h2>
    <table>
        <thead>
            <tr>
                <th>{{__('messages.Group')}}</th>
                <th>{{__('messages.Day')}}</th>
                <th colspan="2" width="24%">{{__('messages.Time')}}</th>
                <th>{{__('messages.contact')}}</th>
                <th width="30%">{{__('messages.Phone')}}</th>
                <th>{{__('messages.Type')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meetings as $meeting)
            <tbody class="each-meeting">
                <tr>
                    <td>{{ $meeting->group->ar_name }}</td>
                    <td>
                        @if(empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence))
                            {{ $meeting->day->ar_name }}
                        @else
                            {{ $meeting->formatted_recurrence }} - {{ $meeting->day->ar_name }}
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</td>
                    <td>{{ $meeting->group->ar_gsr_name }}</td>
                    <td>{{ $meeting->group->phone }}</td>
                    <td>{{ __("messages." . $meeting->type) }}</td>
                </tr>
                <tr>
                    <th colspan="1">{{__('messages.Address')}}</th>
                    <td colspan="6">{{ $meeting->group->ar_address }}</td>
                </tr>
            </tbody>            
            @endforeach
        </tbody>
    </table>
</body>
</html>