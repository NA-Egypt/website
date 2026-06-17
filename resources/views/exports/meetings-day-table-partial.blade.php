@php
    $colsCount = 1 + (in_array('topic', $fields) ? 1 : 0) + (in_array('time', $fields) ? 1 : 0) + (in_array('type', $fields) ? 1 : 0) + (in_array('lang', $fields) ? 1 : 0);
@endphp
<div class="day-section">
    <table>
        <thead>
            <tr>
                <th colspan="{{ $colsCount }}" class="day-title-cell">{{ $dayName }}</th>
            </tr>
            <tr>
                <th>اسم المجموعة</th>
                @if(in_array('topic', $fields))
                    <th>موضوع الاجتماع</th>
                @endif
                @if(in_array('time', $fields))
                    <th style="white-space: nowrap;">الوقت</th>
                @endif
                @if(in_array('type', $fields))
                    <th style="white-space: nowrap;">نوع الاجتماع</th>
                @endif
                @if(in_array('lang', $fields))
                    <th style="white-space: nowrap;">اللغة</th>
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
                        <td style="direction: ltr; text-align: right; white-space: nowrap;">
                            @php
                                $startTimeStr = $meeting->formatted_start_time;
                                $startTimeAr = str_replace(['AM', 'PM'], ['ص', 'م'], $startTimeStr);
                            @endphp
                            {{ $startTimeAr }}
                        </td>
                    @endif
                    @if(in_array('type', $fields))
                        <td style="white-space: nowrap;">
                            {{ $meeting->type === 'open' ? 'مفتوح' : ($meeting->type === 'closed' ? 'مغلق' : $meeting->type) }}
                        </td>
                    @endif
                    @if(in_array('lang', $fields))
                        <td style="white-space: nowrap;">
                            {{ $meeting->lang === 'arabic' ? 'العربية' : ($meeting->lang === 'english' ? 'الإنجليزية' : $meeting->lang) }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
