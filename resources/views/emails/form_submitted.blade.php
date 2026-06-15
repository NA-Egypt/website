<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background: #1e293b; color: #fff; padding: 15px; border-radius: 6px 6px 0 0; text-align: center; }
        .content { padding: 20px; }
        .field { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #edf2f7; }
        .label { font-weight: bold; color: #4a5568; font-size: 0.9rem; }
        .value { margin-top: 5px; color: #1a202c; font-size: 1rem; }
        .badge { display: inline-block; background: #edf2f7; padding: 3px 8px; border-radius: 12px; font-size: 0.85rem; margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">New Form Submission</h2>
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">Form: {{ $form->title }}</p>
        </div>
        <div class="content">
            <p><strong>Submitted At:</strong> {{ $submission->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>Submitted By:</strong> {{ $submission->user ? $submission->user->name : 'Guest' }}</p>
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">

            @foreach ($form->fields as $field)
                @if ($field->type !== 'static_text')
                    <div class="field">
                        <div class="label">{{ $field->label }}</div>
                        <div class="value">
                            @php
                                $val = $submission->data[$field->id] ?? '-';
                            @endphp
                            @if (is_array($val))
                                @foreach($val as $item)
                                    <span class="badge">{{ $item }}</span>
                                @endforeach
                            @else
                                {{ $val }}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</body>
</html>
