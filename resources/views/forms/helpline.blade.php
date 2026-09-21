<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Search Engine Exclusion: Do Not Index or Follow -->
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet, max-image-preview:none">
    <meta name="bingbot" content="noindex, nofollow">

    <title>تسجيل مكالمة خط المساعدة | NA Egypt</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/na-logo32.webp') }}" type="image/webp">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $turnstileSiteKey = trim((string)(config('services.turnstile.site_key') ?: env('TURNSTILE_SITE_KEY')));
    @endphp

    @if(!empty($turnstileSiteKey))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <style>
        :root {
            --primary: #1d4ed8;
            --primary-light: #eff6ff;
            --primary-hover: #1e40af;
            --accent: #059669;
            --accent-light: #ecfdf5;
            --bg-color: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --border-hover: #94a3b8;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 14px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.09);
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px 14px;
        }

        .container {
            max-width: 820px;
            margin: 0 auto;
            width: 100%;
        }

        /* SVG Icons */
        .svg-icon {
            display: inline-block;
            vertical-align: -0.15em;
            fill: currentColor;
            flex-shrink: 0;
        }

        .text-primary { color: var(--primary); }
        .text-muted { color: var(--text-muted); }

        /* Header Card */
        .form-header-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
            color: #ffffff;
            border-radius: var(--radius-lg);
            padding: 24px 22px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .form-header-card::after {
            content: '';
            position: absolute;
            top: -40px;
            left: -40px;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-icon-wrap {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
        }

        .form-title {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .form-subtitle {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 4px;
        }

        /* Form Card */
        .form-body-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 30px 24px;
            box-shadow: var(--shadow-sm);
        }

        /* Field Group */
        .field-group {
            margin-bottom: 24px;
            padding-bottom: 22px;
            border-bottom: 1px solid #f1f5f9;
        }

        .field-group:last-of-type {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 24px;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
        }

        .field-badge-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 0.82rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .required-star {
            color: #ef4444;
            font-weight: bold;
        }

        .optional-badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
            margin-right: 6px;
        }

        .field-description {
            font-size: 0.86rem;
            color: var(--text-muted);
            margin-top: -6px;
            margin-bottom: 12px;
        }

        /* Form Controls - 16px font to prevent iOS Safari auto-zoom */
        .form-control {
            width: 100%;
            min-height: 50px;
            padding: 12px 16px;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            background: #ffffff;
            color: var(--text-main);
            font-size: 16px;
            transition: all 0.2s ease;
            touch-action: manipulation;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            line-height: 1.5;
        }

        /* Mobile-Friendly Options Grid */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 10px;
        }

        .options-grid.options-inline {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }

        /* Option Card with Minimum 52px Touch Target */
        .option-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 52px;
            padding: 14px 16px;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            touch-action: manipulation;
        }

        .option-card:hover {
            border-color: var(--border-hover);
            background: #f8fafc;
        }

        .option-card:active {
            transform: scale(0.985);
        }

        .option-card input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            border: 2px solid #94a3b8;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .option-card input[type="radio"]:checked {
            border-color: var(--primary);
            background-color: var(--primary);
        }

        .option-card input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            width: 8px;
            height: 8px;
            background: #ffffff;
            border-radius: 50%;
        }

        .option-card:has(input[type="radio"]:checked) {
            border-color: var(--primary);
            background: var(--primary-light);
            color: #1e3a8a;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
        }

        .option-text {
            font-size: 0.95rem;
            color: inherit;
            word-break: break-word;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .shift-time-text {
            direction: ltr;
            unicode-bidi: isolate;
            display: inline-block;
            font-variant-numeric: tabular-nums;
        }

        /* Conditional Other input */
        .other-input-container {
            margin-top: 12px;
            display: none;
            animation: fadeIn 0.25s ease-in-out;
        }

        .other-input-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Yes/No Thumb-Friendly Pill Toggle */
        .yes-no-group {
            display: inline-flex;
            width: 100%;
            max-width: 320px;
            background: #e2e8f0;
            padding: 5px;
            border-radius: 14px;
            gap: 6px;
        }

        .yes-no-pill {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            touch-action: manipulation;
        }

        .yes-no-pill input {
            display: none;
        }

        .yes-no-pill.active-no {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 2px 5px rgba(0,0,0,0.12);
        }

        .yes-no-pill.active-yes {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.35);
        }

        /* Error messages */
        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        /* Submit Button - 54px Touch Target */
        .btn-submit {
            width: 100%;
            min-height: 54px;
            padding: 14px 24px;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 800;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
            touch-action: manipulation;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Inline Form Alert Banner */
        .form-alert-banner {
            background: #fef2f2;
            border: 1.5px solid #f87171;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #991b1b;
            animation: fadeIn 0.25s ease-in-out;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
        }

        .alert-icon-wrapper {
            background: #fee2e2;
            color: #dc2626;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-content-wrapper {
            flex-grow: 1;
        }

        .alert-title {
            font-weight: 800;
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .alert-message {
            font-size: 0.88rem;
            line-height: 1.5;
            color: #7f1d1d;
            white-space: pre-line;
        }

        .alert-close-btn {
            background: transparent;
            border: none;
            color: #991b1b;
            font-size: 1.4rem;
            cursor: pointer;
            padding: 0 4px;
            line-height: 1;
        }

        /* Turnstile Center Box */
        .turnstile-wrapper, .recaptcha-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* Fullscreen Success Overlay with 5s countdown */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
            padding: 16px;
        }

        .success-modal {
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 24px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 45px rgba(0,0,0,0.3);
            position: relative;
            transform: scale(0.95);
            animation: popUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes popUp {
            to { transform: scale(1); }
        }

        .checkmark-circle {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #dcfce7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px auto;
            animation: bounceIn 0.5s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); }
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .modal-desc {
            font-size: 0.92rem;
            color: #64748b;
            margin-bottom: 22px;
        }

        .countdown-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #334155;
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .countdown-progress-bar {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
            position: relative;
        }

        .countdown-progress-fill {
            height: 100%;
            width: 100%;
            background: #16a34a;
            transition: width 1s linear;
        }

        .btn-new-call-now {
            margin-top: 18px;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            font-weight: 700;
            min-height: 46px;
            padding: 10px 22px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.92rem;
            width: 100%;
            touch-action: manipulation;
        }

        .btn-new-call-now:active {
            background: #e2e8f0;
        }

        /* Footer */
        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Mobile Adjustments (<= 640px) */
        @media (max-width: 640px) {
            body {
                padding: 12px 10px;
            }
            .form-header-card {
                padding: 20px 16px;
                border-radius: 16px;
                margin-bottom: 14px;
            }
            .form-title {
                font-size: 1.2rem;
            }
            .form-subtitle {
                font-size: 0.82rem;
            }
            .form-body-card {
                padding: 20px 14px;
                border-radius: 16px;
            }
            .field-group {
                margin-bottom: 20px;
                padding-bottom: 18px;
            }
            .options-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .options-grid.options-inline {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
            .option-card {
                min-height: 50px;
                padding: 12px 14px;
            }
            .yes-no-group {
                max-width: 100%;
            }
            .yes-no-pill {
                min-height: 46px;
            }
            .btn-submit {
                font-size: 1.05rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="form-header-card">
        <div class="header-top">
            <div class="header-icon-wrap">
                <svg class="svg-icon" width="26" height="26" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM15.854.146a.5.5 0 0 1 0 .708L11.707 5H14.5a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5v-4a.5.5 0 0 1 1 0v2.793L15.146.146a.5.5 0 0 1 .708 0"/></svg>
            </div>
            <div>
                <h1 class="form-title">تسجيل استجابة مكالمة خط المساعدة</h1>
                <p class="form-subtitle">مجموعة خطوط المساعدة &bull; لجنة العلاقات العامة &bull; زمالة NA مصر</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="form-body-card">
        <form id="helplineForm" action="{{ route('forms.helpline.submit') }}" method="POST">
            @csrf

            <!-- 1. Duration of Call (First Option) -->
            <div class="field-group" id="group_duration">
                <label class="field-label">
                    <span class="field-badge-number">1</span>
                    مدة المكالمة
                    <span class="required-star">*</span>
                </label>
                <div class="options-grid options-inline">
                    <label class="option-card">
                        <input type="radio" name="duration" value="less_than_5" required {{ old('duration') === 'less_than_5' ? 'checked' : '' }}>
                        <span class="option-text">أقل من 5 دقائق</span>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="duration" value="more_than_5" required {{ old('duration') === 'more_than_5' ? 'checked' : '' }}>
                        <span class="option-text">أكثر من 5 دقائق</span>
                    </label>
                </div>
                @error('duration')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 2. Call Date (Today by default, editable) -->
            <div class="field-group" id="group_call_date">
                <label class="field-label" for="call_date">
                    <span class="field-badge-number">2</span>
                    تاريخ المكالمة
                    <span class="required-star">*</span>
                </label>
                <input type="date" id="call_date" name="call_date" class="form-control" value="{{ old('call_date', $today) }}" max="{{ $today }}" required style="max-width: 260px;">
                @error('call_date')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 3. Call Time / Shifts -->
            <div class="field-group" id="group_shifts">
                <label class="field-label">
                    <span class="field-badge-number">3</span>
                    توقيت المكالمة (الوردية)
                    <span class="required-star">*</span>
                </label>
                <div class="options-grid">
                    @foreach($shifts as $shift)
                    <label class="option-card">
                        <input type="radio" name="call_time_shift" value="{{ $shift }}" required {{ old('call_time_shift') === $shift ? 'checked' : '' }}>
                        <span class="option-text">
                            <svg class="svg-icon text-primary" width="16" height="16" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>
                            <span dir="ltr" class="shift-time-text">{{ $shift }}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('call_time_shift')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 4. Types of Callers -->
            <div class="field-group" id="group_caller_type">
                <label class="field-label">
                    <span class="field-badge-number">4</span>
                    فئة المتصل
                    <span class="required-star">*</span>
                </label>
                <div class="options-grid">
                    @foreach($callerTypes as $type)
                    <label class="option-card">
                        <input type="radio" name="caller_type" value="{{ $type }}" required {{ old('caller_type') === $type ? 'checked' : '' }} onchange="handleCallerTypeChange('{{ $type }}')">
                        <span class="option-text">{{ $type }}</span>
                    </label>
                    @endforeach
                </div>
                <div id="caller_type_other_box" class="other-input-container {{ old('caller_type') === 'أخرى' ? 'active' : '' }}">
                    <input type="text" name="caller_type_other" class="form-control" placeholder="يرجى توضيح فئة المتصل هنا..." value="{{ old('caller_type_other') }}">
                    @error('caller_type_other')
                        <div class="error-message">
                            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                @error('caller_type')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 5. How did you know about us? -->
            <div class="field-group" id="group_referral_source">
                <label class="field-label">
                    <span class="field-badge-number">5</span>
                    كيف عرفت عن الزمالة؟
                    <span class="required-star">*</span>
                </label>
                <div class="options-grid">
                    @foreach($referralSources as $source)
                    <label class="option-card">
                        <input type="radio" name="referral_source" value="{{ $source }}" required {{ old('referral_source') === $source ? 'checked' : '' }} onchange="handleReferralSourceChange('{{ $source }}')">
                        <span class="option-text">{{ $source }}</span>
                    </label>
                    @endforeach
                </div>
                <div id="referral_source_other_box" class="other-input-container {{ old('referral_source') === 'أخرى' ? 'active' : '' }}">
                    <input type="text" name="referral_source_other" class="form-control" placeholder="يرجى كتابة مصدر المعرفة هنا..." value="{{ old('referral_source_other') }}">
                    @error('referral_source_other')
                        <div class="error-message">
                            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                <!-- Hospital Name Box (when لجنة المستشفيات is selected) -->
                <div id="hospital_name_box" class="other-input-container {{ old('referral_source') === 'لجنة المستشفيات' ? 'active' : '' }}">
                    <label class="field-label" style="font-size: 0.95rem; margin-bottom: 6px;" for="hospital_name_input">
                        اسم المستشفى
                        <span class="required-star">*</span>
                    </label>
                    <input type="text" id="hospital_name_input" name="hospital_name" class="form-control" placeholder="يرجى كتابة اسم المستشفى هنا..." value="{{ old('hospital_name') }}" {{ old('referral_source') === 'لجنة المستشفيات' ? 'required' : '' }}>
                    @error('hospital_name')
                        <div class="error-message">
                            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                <!-- Poster Location Box (when ملصقات الزمالة is selected) -->
                <div id="poster_location_box" class="other-input-container {{ old('referral_source') === 'ملصقات الزمالة' ? 'active' : '' }}">
                    <label class="field-label" style="font-size: 0.95rem; margin-bottom: 6px;" for="poster_location_input">
                        مكان الملصق
                        <span class="required-star">*</span>
                    </label>
                    <input type="text" id="poster_location_input" name="poster_location" class="form-control" placeholder="يرجى كتابة مكان تواجد الملصق (الشارع، المنطقة، المعلم)..." value="{{ old('poster_location') }}" {{ old('referral_source') === 'ملصقات الزمالة' ? 'required' : '' }}>
                    @error('poster_location')
                        <div class="error-message">
                            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                @error('referral_source')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 6. Volunteer Names (From DB + Other) -->
            <div class="field-group" id="group_volunteer_name">
                <label class="field-label">
                    <span class="field-badge-number">6</span>
                    اسم متلقي المكالمة (المتطوع)
                    <span class="required-star">*</span>
                </label>
                <div class="options-grid">
                    @foreach($volunteers as $v)
                    <label class="option-card">
                        <input type="radio" name="volunteer_name" value="{{ $v->name }}" required {{ old('volunteer_name') === $v->name ? 'checked' : '' }} onchange="handleOtherToggle('volunteer_name', '{{ $v->name }}', 'volunteer_name_other_box')">
                        <span class="option-text">
                            <svg class="svg-icon text-primary" width="16" height="16" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>
                            {{ $v->name }}
                        </span>
                    </label>
                    @endforeach
                    <label class="option-card">
                        <input type="radio" name="volunteer_name" value="أخرى" required {{ old('volunteer_name') === 'أخرى' ? 'checked' : '' }} onchange="handleOtherToggle('volunteer_name', 'أخرى', 'volunteer_name_other_box')">
                        <span class="option-text">
                            <svg class="svg-icon" style="color: #64748b;" width="15" height="15" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/></svg>
                            أخرى
                        </span>
                    </label>
                </div>
                <div id="volunteer_name_other_box" class="other-input-container {{ old('volunteer_name') === 'أخرى' ? 'active' : '' }}">
                    <input type="text" name="volunteer_name_other" class="form-control" placeholder="اكتب اسم المتطوع هنا..." value="{{ old('volunteer_name_other') }}">
                    @error('volunteer_name_other')
                        <div class="error-message">
                            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
                @error('volunteer_name')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 7. Transferred to Step 12 Volunteer (Default: No) -->
            <div class="field-group" id="group_is_step_12">
                <label class="field-label">
                    <span class="field-badge-number">7</span>
                    هل تم تحويل المكالمة إلى متطوع خطوة 12؟
                    <span class="required-star">*</span>
                </label>
                <div class="yes-no-group">
                    <label class="yes-no-pill active-no" id="pill_step12_no">
                        <input type="radio" name="is_step_12" value="0" {{ old('is_step_12', '0') === '0' ? 'checked' : '' }} onchange="updatePillState('step12', 'no')">
                        لا
                    </label>
                    <label class="yes-no-pill" id="pill_step12_yes">
                        <input type="radio" name="is_step_12" value="1" {{ old('is_step_12') === '1' ? 'checked' : '' }} onchange="updatePillState('step12', 'yes')">
                        نعم
                    </label>
                </div>
                @error('is_step_12')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 8. Brief of Call (Long Textbox) -->
            <div class="field-group" id="group_call_brief">
                <label class="field-label" for="call_brief">
                    <span class="field-badge-number">8</span>
                    نبذة عن المكالمة
                    <span class="required-star" id="brief_required_star" style="{{ old('caller_type') === 'عضو حالي' ? 'display: none;' : '' }}">*</span>
                    <span class="optional-badge" id="brief_optional_badge" style="{{ old('caller_type') === 'عضو حالي' ? 'display: inline-flex;' : 'display: none;' }}">(اختياري)</span>
                </label>
                <p class="field-description">سجل ملخصاً واضحاً لما دار في المكالمة وما تم تقديمه للمتصل من معلومات أو مساعدة.</p>
                <textarea id="call_brief" name="call_brief" class="form-control" rows="4" placeholder="اكتب ملخص المكالمة هنا..." {{ old('caller_type') === 'عضو حالي' ? '' : 'required' }}>{{ old('call_brief') }}</textarea>
                @error('call_brief')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 9. Discuss in Coming Meeting (Default: No) -->
            <div class="field-group" id="group_discuss_in_meeting">
                <label class="field-label">
                    <span class="field-badge-number">9</span>
                    هل ترغب في مناقشة هذه المكالمة في الاجتماع القادم؟
                    <span class="required-star">*</span>
                </label>
                <div class="yes-no-group">
                    <label class="yes-no-pill active-no" id="pill_meeting_no">
                        <input type="radio" name="discuss_in_meeting" value="0" {{ old('discuss_in_meeting', '0') === '0' ? 'checked' : '' }} onchange="updatePillState('meeting', 'no')">
                        لا
                    </label>
                    <label class="yes-no-pill" id="pill_meeting_yes">
                        <input type="radio" name="discuss_in_meeting" value="1" {{ old('discuss_in_meeting') === '1' ? 'checked' : '' }} onchange="updatePillState('meeting', 'yes')">
                        نعم
                    </label>
                </div>
                @error('discuss_in_meeting')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- 10. Additional Information (Optional) -->
            <div class="field-group" id="group_additional_info">
                <label class="field-label" for="additional_info">
                    <span class="field-badge-number">10</span>
                    معلومات إضافية
                    <span style="font-size: 0.85rem; font-weight: normal; color: var(--text-muted);">(اختياري)</span>
                </label>
                <textarea id="additional_info" name="additional_info" class="form-control" rows="2" placeholder="أي ملاحظات أو تفاصيل إضافية أخرى إن وجدت...">{{ old('additional_info') }}</textarea>
                @error('additional_info')
                    <div class="error-message">
                        <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- Cloudflare Turnstile Spam Prevention Widget -->
            @if(!empty($turnstileSiteKey))
                <div class="turnstile-wrapper">
                    <div class="cf-turnstile" 
                         data-sitekey="{{ $turnstileSiteKey }}" 
                         data-theme="light"
                         data-refresh-expired="auto"
                         data-response-field-name="cf-turnstile-response"
                         data-expired-callback="onTurnstileExpired"
                         data-error-callback="onTurnstileError"></div>
                    @error('cf-turnstile-response')
                        <div class="error-message mt-2">
                            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            @endif

            <!-- In-Page Alert Banner (Replaces disruptive window.alert popups) -->
            <div id="formAlertBanner" class="form-alert-banner" style="display: none;" role="alert">
                <div class="alert-icon-wrapper">
                    <svg class="svg-icon" width="18" height="18" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
                </div>
                <div class="alert-content-wrapper">
                    <div class="alert-title" id="formAlertTitle">تنبيه</div>
                    <div class="alert-message" id="formAlertMessage"></div>
                </div>
                <button type="button" class="alert-close-btn" onclick="hideFormAlert()" aria-label="إغلاق التنبيه">&times;</button>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="submitBtn" class="btn-submit">
                <svg class="svg-icon" width="22" height="22" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg>
                <span>تسجيل الاستجابة وحفظ المكالمة</span>
            </button>
        </form>
    </div>

    <div class="form-footer">
        &copy; {{ date('Y') }} زمالة المدمنين المجهولين مصر - مجموعة خطوط المساعدة
    </div>
</div>

<!-- 5-Second Success Overlay Modal -->
<div id="successOverlay" class="success-overlay">
    <div class="success-modal">
        <div class="checkmark-circle">
            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <h2 class="modal-title">تم تسجيل الاستجابة بنجاح!</h2>
        <p class="modal-desc">شكراً لخدمتك. تم حفظ تفاصيل المكالمة والوقت بدقة.</p>
        
        <div class="countdown-badge">
            <svg class="svg-icon" width="16" height="16" viewBox="0 0 16 16"><path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/><path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/></svg>
            <span>العودة للنموذج خلال: <strong id="countdownSec">5</strong> ثوانٍ</span>
        </div>

        <div class="countdown-progress-bar">
            <div id="countdownFill" class="countdown-progress-fill"></div>
        </div>

        <div>
            <button type="button" class="btn-new-call-now" onclick="resetFormImmediately()">
                تسجيل مكالمة جديدة الآن
            </button>
        </div>
    </div>
</div>

<script>
    // Other input toggles
    function handleOtherToggle(radioName, value, targetBoxId) {
        const box = document.getElementById(targetBoxId);
        const input = box ? box.querySelector('input') : null;
        if (!box) return;

        if (value === 'أخرى') {
            box.classList.add('active');
            if (input) {
                input.focus();
                if (window.innerWidth <= 640) {
                    setTimeout(() => {
                        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 150);
                }
            }
        } else {
            box.classList.remove('active');
            if (input) input.value = '';
        }
    }

    // Dynamic Caller Type Change
    function handleCallerTypeChange(value) {
        // Toggle 'Other' input
        const otherBox = document.getElementById('caller_type_other_box');
        const otherInput = otherBox ? otherBox.querySelector('input') : null;
        if (value === 'أخرى') {
            if (otherBox) otherBox.classList.add('active');
            if (otherInput) {
                otherInput.focus();
                if (window.innerWidth <= 640) {
                    setTimeout(() => { otherBox.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 150);
                }
            }
        } else {
            if (otherBox) otherBox.classList.remove('active');
            if (otherInput) otherInput.value = '';
        }

        // Toggle call brief optionality for 'عضو حالي'
        const briefTextarea = document.getElementById('call_brief');
        const briefStar = document.getElementById('brief_required_star');
        const briefBadge = document.getElementById('brief_optional_badge');

        if (value === 'عضو حالي') {
            if (briefTextarea) briefTextarea.removeAttribute('required');
            if (briefStar) briefStar.style.display = 'none';
            if (briefBadge) briefBadge.style.display = 'inline-flex';
        } else {
            if (briefTextarea) briefTextarea.setAttribute('required', 'required');
            if (briefStar) briefStar.style.display = 'inline';
            if (briefBadge) briefBadge.style.display = 'none';
        }
    }

    // Dynamic Referral Source Change
    function handleReferralSourceChange(value) {
        const otherBox = document.getElementById('referral_source_other_box');
        const otherInput = otherBox ? otherBox.querySelector('input') : null;

        const hospBox = document.getElementById('hospital_name_box');
        const hospInput = document.getElementById('hospital_name_input');

        const posterBox = document.getElementById('poster_location_box');
        const posterInput = document.getElementById('poster_location_input');

        // Other
        if (value === 'أخرى') {
            if (otherBox) otherBox.classList.add('active');
            if (otherInput) {
                otherInput.focus();
                if (window.innerWidth <= 640) {
                    setTimeout(() => { otherBox.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 150);
                }
            }
        } else {
            if (otherBox) otherBox.classList.remove('active');
            if (otherInput) otherInput.value = '';
        }

        // Hospital Name (لجنة المستشفيات)
        if (value === 'لجنة المستشفيات') {
            if (hospBox) hospBox.classList.add('active');
            if (hospInput) {
                hospInput.setAttribute('required', 'required');
                hospInput.focus();
                if (window.innerWidth <= 640) {
                    setTimeout(() => { hospBox.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 150);
                }
            }
        } else {
            if (hospBox) hospBox.classList.remove('active');
            if (hospInput) {
                hospInput.removeAttribute('required');
                hospInput.value = '';
            }
        }

        // Poster Location (ملصقات الزمالة)
        if (value === 'ملصقات الزمالة') {
            if (posterBox) posterBox.classList.add('active');
            if (posterInput) {
                posterInput.setAttribute('required', 'required');
                posterInput.focus();
                if (window.innerWidth <= 640) {
                    setTimeout(() => { posterBox.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 150);
                }
            }
        } else {
            if (posterBox) posterBox.classList.remove('active');
            if (posterInput) {
                posterInput.removeAttribute('required');
                posterInput.value = '';
            }
        }
    }

    // Yes/No pill updates
    function updatePillState(group, value) {
        const yesPill = document.getElementById(`pill_${group}_yes`);
        const noPill = document.getElementById(`pill_${group}_no`);

        if (value === 'yes') {
            yesPill.className = 'yes-no-pill active-yes';
            noPill.className = 'yes-no-pill';
        } else {
            noPill.className = 'yes-no-pill active-no';
            yesPill.className = 'yes-no-pill';
        }
    }

    // In-Page Alert helpers (replaces disruptive window.alert)
    function showFormAlert(message, title = 'تنبيه', focusElement = null) {
        const banner = document.getElementById('formAlertBanner');
        const titleEl = document.getElementById('formAlertTitle');
        const msgEl = document.getElementById('formAlertMessage');
        if (!banner || !msgEl) return;

        titleEl.textContent = title;
        msgEl.textContent = message;
        banner.style.display = 'flex';

        if (focusElement) {
            focusElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => { focusElement.focus(); }, 200);
        } else {
            banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function hideFormAlert() {
        const banner = document.getElementById('formAlertBanner');
        if (banner) banner.style.display = 'none';
    }

    // Turnstile lifecycle callbacks
    function onTurnstileExpired() {
        console.warn('Turnstile token expired. Resetting widget...');
        if (typeof turnstile !== 'undefined') {
            try { turnstile.reset(); } catch (e) {}
        }
    }

    function onTurnstileError() {
        console.warn('Turnstile error occurred. Resetting widget...');
        if (typeof turnstile !== 'undefined') {
            try { turnstile.reset(); } catch (e) {}
        }
    }

    // Background CSRF recovery for expired sessions
    async function refreshCsrfToken() {
        try {
            const resp = await fetch('{{ route("forms.helpline.show") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const text = await resp.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            const newToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (newToken) {
                document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', newToken);
                const tokenInput = document.querySelector('input[name="_token"]');
                if (tokenInput) tokenInput.value = newToken;
                return newToken;
            }
        } catch (e) {
            console.error('Failed to refresh CSRF token:', e);
        }
        return null;
    }

    // Initialize pill states and dynamic fields on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Dynamic date bounds: initialize max and value to today's local date
        const todayLocal = new Date().toLocaleDateString('en-CA');
        const dateInput = document.getElementById('call_date');
        if (dateInput) {
            dateInput.max = todayLocal;
            if (!dateInput.value) {
                dateInput.value = todayLocal;
            }
        }

        const step12Val = document.querySelector('input[name="is_step_12"]:checked')?.value;
        if (step12Val === '1') updatePillState('step12', 'yes');
        else updatePillState('step12', 'no');

        const meetingVal = document.querySelector('input[name="discuss_in_meeting"]:checked')?.value;
        if (meetingVal === '1') updatePillState('meeting', 'yes');
        else updatePillState('meeting', 'no');

        const checkedCallerType = document.querySelector('input[name="caller_type"]:checked')?.value;
        if (checkedCallerType) {
            handleCallerTypeChange(checkedCallerType);
        }

        const checkedReferralSource = document.querySelector('input[name="referral_source"]:checked')?.value;
        if (checkedReferralSource) {
            handleReferralSourceChange(checkedReferralSource);
        }

        @if(session('success_submitted'))
            triggerSuccessCountdown();
        @endif
    });

    // AJAX Submission with 5s countdown
    const form = document.getElementById('helplineForm');
    const overlay = document.getElementById('successOverlay');
    const submitBtn = document.getElementById('submitBtn');
    const countdownSec = document.getElementById('countdownSec');
    const countdownFill = document.getElementById('countdownFill');
    let timerInterval = null;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        hideFormAlert();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Validate call date bounds (prevent future dates)
        const dateInput = document.getElementById('call_date');
        const todayLocal = new Date().toLocaleDateString('en-CA');
        if (dateInput && dateInput.value && dateInput.value > todayLocal) {
            showFormAlert('لا يمكن تسجيل مكالمة بتاريخ مستقبلي.', 'تنبيه في التاريخ', dateInput);
            return;
        }

        const callerType = document.querySelector('input[name="caller_type"]:checked')?.value;
        if (callerType === 'أخرى' && !document.querySelector('input[name="caller_type_other"]').value.trim()) {
            showFormAlert('يرجى تحديد فئة المتصل في خانة أخرى.', 'حقل مطلوب', document.querySelector('input[name="caller_type_other"]'));
            return;
        }

        const refSource = document.querySelector('input[name="referral_source"]:checked')?.value;
        if (refSource === 'أخرى' && !document.querySelector('input[name="referral_source_other"]').value.trim()) {
            showFormAlert('يرجى توضيح كيف عرف المتصل عن الزمالة في خانة أخرى.', 'حقل مطلوب', document.querySelector('input[name="referral_source_other"]'));
            return;
        }

        if (refSource === 'لجنة المستشفيات' && !document.getElementById('hospital_name_input').value.trim()) {
            showFormAlert('يرجى كتابة اسم المستشفى.', 'حقل مطلوب', document.getElementById('hospital_name_input'));
            return;
        }

        if (refSource === 'ملصقات الزمالة' && !document.getElementById('poster_location_input').value.trim()) {
            showFormAlert('يرجى كتابة مكان الملصق.', 'حقل مطلوب', document.getElementById('poster_location_input'));
            return;
        }

        if (callerType !== 'عضو حالي') {
            const briefInput = document.getElementById('call_brief');
            if (!briefInput.value.trim()) {
                showFormAlert('يرجى كتابة نبذة أو ملخص عن المكالمة.', 'حقل مطلوب', briefInput);
                return;
            }
        }

        const volunteerName = document.querySelector('input[name="volunteer_name"]:checked')?.value;
        if (volunteerName === 'أخرى' && !document.querySelector('input[name="volunteer_name_other"]').value.trim()) {
            showFormAlert('يرجى كتابة اسم المتطوع في خانة أخرى.', 'حقل مطلوب', document.querySelector('input[name="volunteer_name_other"]'));
            return;
        }

        // Validate Cloudflare Turnstile if present
        const turnstileWidget = document.querySelector('.cf-turnstile');
        if (turnstileWidget) {
            let turnstileToken = '';
            if (typeof turnstile !== 'undefined') {
                try {
                    turnstileToken = turnstile.getResponse();
                } catch (e) {}
            }
            if (!turnstileToken) {
                turnstileToken = document.querySelector('[name="cf-turnstile-response"]')?.value || '';
            }
            if (!turnstileToken) {
                showFormAlert('يرجى تأكيد التحقق الأمني (Turnstile) قبل الإرسال.', 'التحقق الأمني');
                return;
            }
        }

        submitBtn.disabled = true;
        const btnText = submitBtn.querySelector('span');
        if (btnText) btnText.textContent = 'جارٍ الحفظ...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            if (response.status === 419) {
                // CSRF Session mismatch / expired: auto-refresh token
                await refreshCsrfToken();
                if (typeof turnstile !== 'undefined') {
                    try { turnstile.reset(); } catch (e) {}
                }
                throw {
                    status: 419,
                    message: 'انتهت صلاحية الجلسة وتم تجديدها تلقائياً. يرجى النقر مرة أخرى على زر الحفظ للمتابعة.'
                };
            }

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                errData.status = response.status;
                throw errData;
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                triggerSuccessCountdown();
            } else {
                showFormAlert('حدث خطأ أثناء الحفظ. يرجى مراجعة البيانات والمحاولة مجدداً.', 'خطأ أثناء الحفظ');
            }
        })
        .catch(err => {
            console.error('Submission error:', err);
            // Always auto-reset Turnstile on failure so volunteer can immediately retry without reloading
            if (typeof turnstile !== 'undefined') {
                try { turnstile.reset(); } catch (e) {}
            }

            if (err.errors) {
                const msg = Object.values(err.errors).flat().join('\n');
                showFormAlert(msg, 'يرجى مراجعة البيانات المدخلة');
            } else if (err.message) {
                showFormAlert(err.message, err.status === 419 ? 'تجديد الجلسة' : 'تنبيه');
            } else {
                showFormAlert('حدث خطأ أثناء الحفظ. يرجى مراجعة البيانات والمحاولة مجدداً.', 'خطأ أثناء الحفظ');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            const btnText = submitBtn.querySelector('span');
            if (btnText) btnText.textContent = 'تسجيل الاستجابة وحفظ المكالمة';
        });
    });

    function triggerSuccessCountdown() {
        overlay.style.display = 'flex';
        let remaining = 5;
        countdownSec.textContent = remaining;
        countdownFill.style.width = '100%';

        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(() => {
            remaining--;
            if (remaining >= 0) {
                countdownSec.textContent = remaining;
                countdownFill.style.width = (remaining / 5 * 100) + '%';
            }
            if (remaining <= 0) {
                clearInterval(timerInterval);
                resetFormImmediately();
            }
        }, 1000);
    }

    function resetFormImmediately() {
        if (timerInterval) clearInterval(timerInterval);
        overlay.style.display = 'none';

        hideFormAlert();
        form.reset();

        if (typeof turnstile !== 'undefined') {
            try { turnstile.reset(); } catch (e) {}
        }

        const freshToday = new Date().toLocaleDateString('en-CA');
        const dateInput = document.getElementById('call_date');
        if (dateInput) {
            dateInput.value = freshToday;
            dateInput.max = freshToday;
        }

        updatePillState('step12', 'no');
        updatePillState('meeting', 'no');

        // Reset call brief optionality state
        const briefTextarea = document.getElementById('call_brief');
        const briefStar = document.getElementById('brief_required_star');
        const briefBadge = document.getElementById('brief_optional_badge');
        if (briefTextarea) briefTextarea.setAttribute('required', 'required');
        if (briefStar) briefStar.style.display = 'inline';
        if (briefBadge) briefBadge.style.display = 'none';

        document.querySelectorAll('.other-input-container').forEach(box => {
            box.classList.remove('active');
            const inp = box.querySelector('input');
            if (inp) {
                inp.value = '';
                inp.removeAttribute('required');
            }
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

</body>
</html>
