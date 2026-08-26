@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
$textAlign = app()->getLocale() === 'ar' ? 'right' : 'left';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <title>{{ $form->title }} - #{{ $submission->id }}</title>
    <style>
        body, table, th, td {
            font-family: 'xbriyaz', 'Cairo', sans-serif;
            direction: {{ $direction }};
            unicode-bidi: embed;
            color: #1e293b;
            font-size: 11px;
        }
        body {
            margin: 0;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-table td {
            border: none;
            padding: 4px;
        }
        .form-title {
            font-size: 18px;
            font-weight: bold;
            line-height: 1.5;
            color: #0f172a;
            margin: 0 0 6px 0;
            text-align: {{ $textAlign }};
        }
        .form-subtitle {
            font-size: 12px;
            line-height: 1.8;
            color: #475569;
            margin: 4px 0 10px 0;
            text-align: {{ $textAlign }};
        }
        .badge-type-container {
            text-align: center;
            margin-top: 10px;
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
        .response-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .response-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: {{ $textAlign }};
            font-size: 11px;
        }
        .response-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: {{ $textAlign }};
            vertical-align: top;
            font-size: 11px;
        }
        .col-header {
            width: 35%;
            font-weight: bold;
            background-color: #f8fafc;
            color: #475569;
        }
        .col-response {
            width: 65%;
            color: #0f172a;
        }
        .section-row td {
            background-color: #e2e8f0;
            color: #1e293b;
            font-weight: bold;
            font-size: 12px;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
        }
        .nested-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .nested-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 10px;
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
        }
        .nested-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 75%; vertical-align: middle;">
                <div class="form-title">{{ $form->title }}</div>
                @if (!empty($form->settings['subtitle']))
                    <div class="form-subtitle">{{ $form->settings['subtitle'] }}</div>
                @endif
            </td>
            <td style="width: 25%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; vertical-align: middle;">
                @if (file_exists(public_path('assets/images/logo.png')))
                    <img src="{{ public_path('assets/images/logo.png') }}" style="max-height: 50px; width: auto;" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="badge-type-container">
        <span class="badge-type">
            {{ $form->type === 'survey' ? (__('messages.Survey') ?? 'Survey') : ($form->type === 'service_position_application' ? (__('messages.Service Position Application') ?? 'Service Position Application') : (__('messages.Event Registration') ?? 'Event Registration')) }}
        </span>
    </div>

    <table class="response-table">
        <thead>
            <tr>
                <th style="width: 35%;">{{ __('messages.Question / Field') ?? 'Question / Field' }}</th>
                <th style="width: 65%;">{{ __('messages.Response / Answer') ?? 'Response / Answer' }}</th>
            </tr>
        </thead>
        <tbody>
            <!-- Metadata Rows -->
            <tr>
                <td class="col-header">{{ __('messages.Submission ID') ?? 'Submission ID' }}</td>
                <td class="col-response"><strong>#{{ $submission->id }}</strong></td>
            </tr>
            <tr>
                <td class="col-header">{{ __('messages.Submitted At') ?? 'Submitted At' }}</td>
                <td class="col-response">{{ $submission->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td class="col-header">{{ __('messages.Submitted By') ?? 'Submitted By' }}</td>
                <td class="col-response">{{ $submission->user ? $submission->user->name : (__('messages.Guest') ?? 'Guest') }}</td>
            </tr>

            <!-- Fields Rows -->
            @foreach ($form->fields as $field)
                @if ($field->type === 'section_header')
                    <tr class="section-row">
                        <td colspan="2">
                            {{ $field->label }}
                            @if (!empty($field->options['description']))
                                <span style="font-size: 9px; font-weight: normal; color: #64748b; margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 6px;">({{ $field->options['description'] }})</span>
                            @endif
                        </td>
                    </tr>
                @elseif ($field->type === 'static_text')
                    <!-- Static text informational display -->
                    <tr>
                        <td class="col-header" style="font-style: italic; color: #64748b;">{{ __('messages.Static Text Block') ?? 'Information' }}</td>
                        <td class="col-response" style="color: #475569;">{!! nl2br(e($field->label)) !!}</td>
                    </tr>
                @else
                    <tr>
                        <td class="col-header">{{ $field->label }}</td>
                        <td class="col-response">
                            @php
                                $val = $submission->data[$field->id] ?? '-';
                            @endphp

                            @if ($field->type === 'table')
                                @if (!empty($val) && is_array($val))
                                    <table class="nested-table">
                                        <thead>
                                            <tr>
                                                @php $headers = !empty($val[0]) && is_array($val[0]) ? array_keys($val[0]) : []; @endphp
                                                @foreach ($headers as $h)
                                                    <th>{{ $h }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($val as $row)
                                                @if (is_array($row))
                                                    <tr>
                                                        @foreach ($row as $cell)
                                                            <td>{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif

                            @elseif ($field->type === 'yes_no_textbox')
                                @php
                                    $ans = is_array($val) ? ($val['answer'] ?? null) : $val;
                                    $det = is_array($val) ? ($val['details'] ?? null) : null;
                                @endphp
                                @if ($ans === 'yes')
                                    <strong>{{ __('messages.yes') ?? 'Yes' }}</strong>
                                    @if (!empty($det))
                                        <div style="margin-top: 3px; color: #475569; font-size: 10px; font-style: italic;">{{ __('messages.Details / Explanation') ?? 'Details' }}: {{ $det }}</div>
                                    @endif
                                @elseif ($ans === 'no')
                                    <strong>{{ __('messages.no') ?? 'No' }}</strong>
                                @else
                                    <span style="color: #94a3b8;">-</span>
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
                                <span>{{ $val }}</span> <span style="color: #2563eb; font-weight: bold; margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 4px;">{{ $elapsedStr }}</span>

                            @elseif ($field->type === 'phone')
                                @if ($val !== '-' && $val !== '' && $val !== null)
                                    <span dir="ltr" style="direction: ltr !important; text-align: left !important; unicode-bidi: embed; display: inline-block;">{{ $val }}</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif

                            @elseif (is_array($val))
                                {{ implode(', ', $val) }}

                            @else
                                @if ($val === '-' || $val === '' || $val === null)
                                    <span style="color: #94a3b8;">-</span>
                                @else
                                    {!! nl2br(e($val)) !!}
                                @endif
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ config('app.name', 'NA Egypt') }} &copy; {{ date('Y') }} - {{ __('messages.Generated automatically') ?? 'Generated automatically' }}
    </div>

</body>
</html>
