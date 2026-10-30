@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
$filters = request()->only(['day', 'serviceBody', 'group', 'neighborhood', 'type', 'city']);
@endphp
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
                @auth
                <th>{{__('messages.contact')}}</th>
                <th width="30%">{{__('messages.Phone')}}</th>
                @endauth
                <th>{{__('messages.Type')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meetings as $meeting)
            @php
                $isWeekly = empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence);
                $hasVariations = $meeting->recurring_variations && $meeting->recurring_variations->isNotEmpty();
                $groupName = $meeting->group ? ($meeting->group->ar_name ?: $meeting->group->en_name) : '';
            @endphp
            <tbody class="each-meeting">
                <tr>
                    <td>
                        <strong>{{ $groupName }}</strong>
                        @if($hasVariations)
                            <span style="color: #00698f; font-weight: bold;">*</span>
                        @elseif(!$isWeekly && !empty($meeting->recurrence))
                            <span style="font-weight: normal; font-size: 85%; color: #555;">({{ $meeting->formatted_recurrence }})</span>
                        @endif
                    </td>
                    <td>
                        {{ $meeting->day->ar_name }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</td>
                    @auth
                    <td>{{ $meeting->group->ar_gsr_name }}</td>
                    <td>{{ $meeting->group->phone }}</td>
                    @endauth
                    <td>{{ __("messages." . $meeting->type) }}</td>
                </tr>
                <tr>
                    <th colspan="1">{{__('messages.Address')}}</th>
                    <td colspan="{{ auth()->check() ? 6 : 4 }}">{{ $meeting->group->ar_address }}</td>
                </tr>
                @if($hasVariations)
                    @php
                        $pStart = \Carbon\Carbon::parse($meeting->start_time)->format('H:i');
                        $pEnd = \Carbon\Carbon::parse($meeting->end_time)->format('H:i');
                    @endphp
                    @foreach($meeting->recurring_variations as $var)
                        @php
                            $vStart = \Carbon\Carbon::parse($var->start_time)->format('H:i');
                            $vEnd = \Carbon\Carbon::parse($var->end_time)->format('H:i');
                            $vTimeDiff = ($pStart !== $vStart || $pEnd !== $vEnd);
                            $vTimeStr = '';
                            if ($vTimeDiff) {
                                $vTimeStr = " (" . \Carbon\Carbon::parse($var->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($var->end_time)->format('h:i A') . ")";
                            }
                            $vTypeStr = __("messages." . $var->type);
                            $vTopics = $var->topics && $var->topics->isNotEmpty() 
                                ? implode('، ', $var->topics->pluck('ar_name')->filter()->toArray())
                                : ($var->topic ? ($var->topic->ar_name ?: $var->topic->en_name) : '');
                            $descParts = array_filter([$vTypeStr, $vTopics]);
                            $desc = implode(' - ', $descParts);
                        @endphp
                        <tr style="background-color: #f2f7fa;">
                            <td colspan="{{ auth()->check() ? 7 : 5 }}" style="font-size: 11px; color: #00698f; text-align: right; padding: 4px 8px;">
                                * {{ $groupName }}: {{ $var->formatted_recurrence }}{{ $desc ? (': ' . $desc) : '' }}{{ $vTimeStr }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>            
            @endforeach
        </tbody>
    </table>
</body>
</html>