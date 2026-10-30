@php
    $colsCount = 1 + (in_array('topic', $fields) ? 1 : 0) + (in_array('time', $fields) ? 1 : 0) + (in_array('type', $fields) ? 1 : 0) + (in_array('lang', $fields) ? 1 : 0);
    $footnotes = [];
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
                @php
                    $isWeekly = empty($meeting->recurrence) || in_array('weekly', $meeting->recurrence);
                    $hasVariations = $meeting->recurring_variations && $meeting->recurring_variations->isNotEmpty();
                    $groupName = $meeting->group ? ($meeting->group->ar_name ?: $meeting->group->en_name) : '';

                    if ($hasVariations) {
                        $pStart = \Carbon\Carbon::parse($meeting->start_time)->format('H:i');
                        $pEnd = \Carbon\Carbon::parse($meeting->end_time)->format('H:i');
                        foreach ($meeting->recurring_variations as $var) {
                            $vStart = \Carbon\Carbon::parse($var->start_time)->format('H:i');
                            $vEnd = \Carbon\Carbon::parse($var->end_time)->format('H:i');
                            $vTimeDiff = ($pStart !== $vStart || $pEnd !== $vEnd);
                            $vTimeStr = '';
                            if ($vTimeDiff) {
                                $vStartAr = str_replace(['AM', 'PM'], ['ص', 'م'], \Carbon\Carbon::parse($var->start_time)->format('h:i A'));
                                $vEndAr = str_replace(['AM', 'PM'], ['ص', 'م'], \Carbon\Carbon::parse($var->end_time)->format('h:i A'));
                                $vTimeStr = " ({$vStartAr} - {$vEndAr})";
                            }
                            $vTypeStr = $var->type === 'open' ? 'مفتوح' : ($var->type === 'closed' ? 'مغلق' : $var->type);
                            $vTopics = $var->topics && $var->topics->isNotEmpty()
                                ? implode('، ', $var->topics->pluck('ar_name')->filter()->toArray())
                                : ($var->topic ? ($var->topic->ar_name ?: $var->topic->en_name) : '');

                            $descParts = array_filter([$vTypeStr, $vTopics]);
                            $desc = implode(' - ', $descParts);
                            $footnotes[] = "* {$groupName}: {$var->formatted_recurrence}" . ($desc ? ": {$desc}" : '') . $vTimeStr;
                        }
                    }
                @endphp
                <tr>
                    <td>
                        <strong>{{ $groupName }}</strong>
                        @if($hasVariations)
                            <span style="color: #00698f; font-weight: bold;">*</span>
                        @elseif(!$isWeekly && !empty($meeting->recurrence))
                            <span style="font-weight: normal; font-size: 85%; color: #555;">({{ $meeting->formatted_recurrence }})</span>
                        @endif
                    </td>
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
    @if(!empty($footnotes))
        <div class="day-footnotes" style="margin-top: 2px; margin-bottom: 5px; text-align: right; color: #00698f; font-size: {{ $pageSize === 'A5' ? '6.5px' : '7.5px' }}; line-height: 1.3;">
            @foreach($footnotes as $fn)
                <div>{{ $fn }}</div>
            @endforeach
        </div>
    @endif
</div>
