@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <title>{{ $form->title }} - {{ __('messages.Submissions Report') ?? 'Submissions Report' }}</title>
    <style>
        body, table, th, td {
            font-family: 'xbriyaz', 'Cairo', sans-serif;
            direction: {{ $direction }};
            unicode-bidi: embed;
            color: #333;
            font-size: 11px;
        }
        body {
            margin: 0;
            padding: 5px;
        }
        h2 {
            font-size: 18px;
            color: #1a202c;
            margin-bottom: 5px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        .meta {
            margin-bottom: 20px;
            color: #4a5568;
            font-size: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #cbd5e0;
            padding: 8px 6px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge-type-container {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 12px;
            width: 100%;
        }
        .badge-type {
            display: inline-block;
            padding: 4px 16px;
            font-size: 10px;
            font-weight: bold;
            color: #1d4ed8;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 16px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #718096;
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>{{ $form->title }}</h2>
    @if (!empty($form->settings['subtitle']))
        <div style="font-size: 12px; line-height: 1.8; color: #475569; margin: 4px 0 10px 0;">{{ $form->settings['subtitle'] }}</div>
    @endif
    <div class="badge-type-container">
        <span class="badge-type">
            {{ $form->type === 'survey' ? (__('messages.Survey') ?? 'Survey') : ($form->type === 'service_position_application' ? (__('messages.Service Position Application') ?? 'Service Position Application') : (__('messages.Event Registration') ?? 'Event Registration')) }}
        </span>
    </div>
    <div class="meta">
        {{ __('messages.Views') ?? 'Views' }}: {{ $form->views }} | 
        {{ __('messages.Submissions') ?? 'Submissions' }}: {{ $form->submissions->count() }} | 
        {{ __('messages.Conversion Rate') ?? 'Conversion Rate' }}: {{ $form->conversion_rate }}% | 
        {{ __('messages.Date') ?? 'Date' }}: {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('messages.Submission ID') ?? 'ID' }}</th>
                <th>{{ __('messages.Date') ?? 'Submitted At' }}</th>
                <th>{{ __('messages.Submitted By') ?? 'Submitted By' }}</th>
                @foreach ($form->fields as $field)
                    <th>{{ $field->label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($form->submissions as $submission)
                <tr>
                    <td>#{{ $submission->id }}</td>
                    <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest') }}</td>
                    @foreach ($form->fields as $field)
                        <td>
                            @php
                                $val = $submission->data[$field->id] ?? '-';
                            @endphp
                            @if ($field->type === 'table' && is_array($val))
                                @foreach ($val as $r)
                                    @if (is_array($r))
                                        <div>[{{ implode(' | ', $r) }}]</div>
                                    @endif
                                @endforeach
                            @elseif ($field->type === 'yes_no_textbox')
                                @php
                                    $ans = is_array($val) ? ($val['answer'] ?? null) : $val;
                                    $det = is_array($val) ? ($val['details'] ?? null) : null;
                                @endphp
                                @if ($ans === 'yes')
                                    {{ __('messages.yes') ?? 'Yes' }}{{ !empty($det) ? ' (' . $det . ')' : '' }}
                                @elseif ($ans === 'no')
                                    {{ __('messages.no') ?? 'No' }}
                                @else
                                    -
                                @endif
                            @elseif ($field->type === 'date' && !empty($val) && $val !== '-' && strtotime($val))
                                @php
                                    $submittedDate = new \DateTime($val);
                                    $now = new \DateTime();
                                    $interval = $submittedDate->diff($now);
                                    $locale = app()->getLocale();
                                    if ($submittedDate > $now) {
                                        $elapsedStr = $locale === 'ar' ? 'في المستقبل' : 'in the future';
                                    } else {
                                        $elapsedStr = sprintf(
                                            $locale === 'ar' ? '(%d سنة، %d شهر، %d يوم)' : '(%d years, %d months, %d days)',
                                            $interval->y,
                                            $interval->m,
                                            $interval->d
                                        );
                                    }
                                @endphp
                                <span>{{ $val }}</span> <span style="color: #2563eb; font-weight: bold;">{{ $elapsedStr }}</span>
                            @elseif ($field->type === 'phone')
                                @if ($val !== '-' && $val !== '' && $val !== null)
                                    <span dir="ltr" style="direction: ltr !important; text-align: left !important; unicode-bidi: embed; display: inline-block;">{{ $val }}</span>
                                @else
                                    -
                                @endif
                            @elseif (is_array($val))
                                {{ implode(', ', $val) }}
                            @else
                                {{ $val }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ __('messages.Forms Builder') ?? 'Forms Builder' }}
    </div>

</body>
</html>
