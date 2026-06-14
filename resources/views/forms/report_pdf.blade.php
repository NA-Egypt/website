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
    <div class="meta">
        {{ __('messages.Type') ?? 'Type' }}: {{ $form->type === 'survey' ? (__('messages.Survey') ?? 'Survey') : (__('messages.Event Registration') ?? 'Event Registration') }} | 
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
                            @if (is_array($val))
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
