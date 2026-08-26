@php
if (!function_exists('form_autolink')) {
    function form_autolink(?string $text): string {
        if (!$text) return '';
        $escaped = e($text);
        $pattern = '/\b((https?:\/\/|www\.)[^\s<]+)/i';
        return preg_replace_callback($pattern, function($matches) {
            $url = $matches[0];
            $href = str_starts_with(strtolower($url), 'www.') ? 'https://' . $url : $url;
            return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-underline fw-semibold">' . $url . '</a>';
        }, $escaped);
    }
}
@endphp
<x-frontend.layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">

    <style>
        body {
            background-color: #f8fafc !important;
            background-image: radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.03) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.03) 0%, transparent 40%) !important;
            background-attachment: fixed !important;
        }

        .glass-form-card {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05) !important;
            border-radius: 24px !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-form-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.08) !important;
        }

        .gradient-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            padding: 2.5rem !important;
        }

        .form-control-custom, .form-select-custom {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1.5px solid #e2e8f0 !important;
            color: #1a202c !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease-in-out !important;
        }

        .vue-select-container [data-assembled-select] [data-select-trigger] {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1.5px solid #e2e8f0 !important;
            color: #1a202c !important;
            border-radius: 12px !important;
            min-height: 48px !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease-in-out !important;
        }

        .vue-select-container [data-assembled-select] [data-select-trigger]:focus-within {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
            background: #ffffff !important;
        }

        .vue-select-container [data-select-popover] {
            border-radius: 12px !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            z-index: 1050 !important;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            background: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
            outline: none !important;
        }

        .checkbox-custom-group {
            background: rgba(248, 250, 252, 0.8);
            border: 1.5px solid #edf2f7;
            border-radius: 12px;
            padding: 1.25rem;
        }

        .btn-submit-premium {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 12px !important;
            padding: 0.9rem 2rem !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2) !important;
        }

        .btn-submit-premium:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3) !important;
            filter: brightness(1.05);
        }

        .btn-submit-premium:active {
            transform: translateY(1px);
        }

        /* Input Group with Icons Enhancement */
        .input-group-custom {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 0 0.85rem !important;
            transition: all 0.2s ease-in-out !important;
        }

        .input-group-custom:focus-within {
            background: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
        }

        .input-group-custom .input-icon {
            color: #94a3b8;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        [dir="rtl"] .input-group-custom .input-icon {
            margin-right: 0;
            margin-left: 0.75rem;
        }

        .input-group-custom .form-control-custom,
        .input-group-custom .form-select-custom {
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            padding: 0.75rem 0.25rem !important;
            flex: 1;
            width: 100%;
        }

        /* Custom Checkbox Tile styling */
        .form-check-tile {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.6) !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 0.9rem 1.1rem !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-check-tile:hover {
            background: rgba(59, 130, 246, 0.03) !important;
            border-color: #cbd5e1 !important;
        }

        .form-check-tile:has(input:checked) {
            background: rgba(37, 99, 235, 0.05) !important;
            border-color: #2563eb !important;
        }

        .form-check-tile:has(input:checked) span {
            color: #2563eb !important;
            font-weight: 600;
        }

        .form-check-tile input[type="checkbox"] {
            cursor: pointer;
        }

        /* Direction-based fixes */
        [dir="rtl"] .form-select-custom {
            background-position: left 0.75rem center !important;
            padding-left: 2.5rem !important;
            padding-right: 1rem !important;
        }

        .iti {
            width: 100% !important;
            display: block !important;
            direction: ltr !important;
            text-align: left !important;
            unicode-bidi: embed !important;
        }
        .iti input, .phone-input {
            direction: ltr !important;
            text-align: left !important;
            unicode-bidi: embed !important;
        }
        .iti__selected-dial-code {
            direction: ltr !important;
            unicode-bidi: embed !important;
        }
        .iti__country-list {
            z-index: 1050 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            max-width: 85vw !important;
        }

        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }

        .form-public-container {
            width: 100% !important;
            max-width: 860px !important;
            margin: 0 auto !important;
            box-sizing: border-box !important;
            padding: 0 4px !important;
        }

        .form-field-group {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Mobile Viewport Responsiveness */
        @media (max-width: 575.98px) {
            .glass-form-card {
                border-radius: 16px !important;
                margin: 0 !important;
            }
            .form-header-box {
                padding: 1.75rem 1rem !important;
            }
            .icon-wrapper-header {
                width: 56px !important;
                height: 56px !important;
            }
            .icon-wrapper-header i {
                font-size: 1.75rem !important;
            }
            .form-main-title {
                font-size: 1.35rem !important;
            }
            .card-body-custom {
                padding: 1.25rem 0.85rem !important;
            }
            .form-check-tile {
                padding: 0.75rem 0.85rem !important;
            }
            .btn-submit-premium {
                padding: 0.85rem 1.25rem !important;
                font-size: 1rem !important;
            }
            .table-field-wrapper {
                padding: 0.65rem !important;
            }
        }
    </style>

    <div class="form-public-container py-2 py-sm-3 py-md-4">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 animate__animated animate__fadeIn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 14px;">
                <ul class="mb-0 py-1">
                    @foreach ($errors->all() as $error)
                        <li class="small fw-semibold">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card glass-form-card border-0 w-100">
                    <!-- Premium Light Form Header -->
                    <div class="form-header-box text-center py-4 py-md-5 px-3 px-md-4 border-bottom" style="background: rgba(255, 255, 255, 0.3); border-color: rgba(0, 0, 0, 0.05) !important;">
                        @if ($form->type === 'survey')
                            <div class="icon-wrapper-header mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary shadow-sm" style="width: 68px; height: 68px; background-color: rgba(37, 99, 235, 0.1) !important;">
                                <i class="bi {{ $form->settings['icon'] ?? 'bi-clipboard2-data' }}" style="font-size: 2.1rem;"></i>
                            </div>
                            <h2 class="form-main-title fw-bold mb-2 mb-md-3" style="color: #0f172a !important; font-size: clamp(1.25rem, 3.5vw, 1.85rem); letter-spacing: -0.5px;">{{ $form->title }}</h2>
                            @if (!empty($form->settings['subtitle']))
                                <p class="text-secondary mb-3 small">{{ $form->settings['subtitle'] }}</p>
                            @endif
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: rgba(37, 99, 235, 0.1) !important; border-color: rgba(37, 99, 235, 0.2) !important;">
                                {{ __('messages.Survey') ?? 'Survey' }}
                            </span>
                        @elseif ($form->type === 'service_position_application')
                            <div class="icon-wrapper-header mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 68px; height: 68px; background-color: rgba(99, 102, 241, 0.12) !important; color: #4f46e5;">
                                <i class="bi {{ $form->settings['icon'] ?? 'bi-person-badge' }}" style="font-size: 2.1rem;"></i>
                            </div>
                            <h2 class="form-main-title fw-bold mb-2 mb-md-3" style="color: #0f172a !important; font-size: clamp(1.25rem, 3.5vw, 1.85rem); letter-spacing: -0.5px;">{{ $form->title }}</h2>
                            @if (!empty($form->settings['subtitle']))
                                <p class="text-secondary mb-3 small">{{ $form->settings['subtitle'] }}</p>
                            @endif
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: rgba(99, 102, 241, 0.12) !important; color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.25);">
                                {{ __('messages.Service Position Application') ?? 'Service Position Application' }}
                            </span>
                        @else
                            <div class="icon-wrapper-header mx-auto mb-2 mb-md-3 d-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success shadow-sm" style="width: 68px; height: 68px; background-color: rgba(16, 185, 129, 0.1) !important;">
                                <i class="bi {{ $form->settings['icon'] ?? 'bi-calendar-check' }}" style="font-size: 2.1rem;"></i>
                            </div>
                            <h2 class="form-main-title fw-bold mb-2 mb-md-3" style="color: #0f172a !important; font-size: clamp(1.25rem, 3.5vw, 1.85rem); letter-spacing: -0.5px;">{{ $form->title }}</h2>
                            @if (!empty($form->settings['subtitle']))
                                <p class="text-secondary mb-3 small">{{ $form->settings['subtitle'] }}</p>
                            @endif
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: rgba(16, 185, 129, 0.1) !important; border-color: rgba(16, 185, 129, 0.2) !important;">
                                {{ __('messages.Event Registration') ?? 'Event Registration' }}
                            </span>
                        @endif
                    </div>

                    <!-- Form Input Fields -->
                    <div class="card-body card-body-custom p-3 p-sm-4 p-md-5">
                        <form action="{{ route('forms.submit.public', $form->slug) }}" method="POST" id="public-submission-form" onsubmit="showLoadingState()">
                            @csrf

                            <div class="d-flex flex-column gap-3 gap-md-4 w-100">
                                @php
                                    $sectionOpen = false;
                                @endphp
                                @foreach ($form->fields as $field)
                                    @php
                                        $isStatic = $field->type === 'static_text';
                                    @endphp

                                    @if ($field->type === 'section_header')
                                        @if ($sectionOpen)
                                            </div></div></div></div> <!-- Close previous card body, card, and section item wrapper -->
                                        @endif
                                        <div class="form-section-item w-100 my-2">
                                             <div class="card border-0 rounded-4 shadow-sm w-100" style="background: rgba(255, 255, 255, 0.4); border: 1px solid rgba(0,0,0,0.03) !important;">
                                                <div class="card-header border-0 bg-transparent pt-3 pt-md-4 px-3 px-md-4 pb-0">
                                                    <h5 class="fw-bold mb-1 text-primary d-flex align-items-center gap-2">
                                                        <i class="bi bi-folder2-open"></i> {{ $field->label }}
                                                    </h5>
                                                    @if (!empty($field->options['description']))
                                                        <p class="text-muted small mb-0">{!! form_autolink($field->options['description']) !!}</p>
                                                    @endif
                                                </div>
                                                <div class="card-body p-3 p-md-4">
                                                    <div class="d-flex flex-column gap-3 w-100">
                                        @php
                                            $sectionOpen = true;
                                        @endphp
                                    @else
                                        <div class="form-field-group w-100">
                                            @if (!$isStatic)
                                                <label class="form-label fw-bold small mb-1 d-block" style="color: #0f172a !important;">
                                                    {{ $field->label }}
                                                    @if ($field->required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                @if (!empty($field->options['description']))
                                                    <div class="text-muted small mb-2" style="font-size: 0.8rem; line-height: 1.4;">
                                                        {!! form_autolink($field->options['description']) !!}
                                                    </div>
                                                @endif
                                            @endif

                                            @if ($field->type === 'text')
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-pencil-square"></i></span>
                                                    <input type="text" name="field_{{ $field->id }}" class="form-control form-control-custom" value="{{ old('field_' . $field->id) }}" placeholder="{{ $field->options['placeholder'] ?? '' }}" {{ $field->required ? 'required' : '' }}>
                                                </div>

                                            @elseif ($field->type === 'textarea')
                                                <div class="input-group-custom align-items-start">
                                                    <span class="input-icon mt-2.5"><i class="bi bi-chat-left-text"></i></span>
                                                    <textarea name="field_{{ $field->id }}" rows="4" class="form-control form-control-custom" placeholder="{{ $field->options['placeholder'] ?? '' }}" {{ $field->required ? 'required' : '' }}>{{ old('field_' . $field->id) }}</textarea>
                                                </div>

                                            @elseif ($field->type === 'phone')
                                                <div dir="ltr" style="direction: ltr !important; text-align: left !important; width: 100%;">
                                                    <input type="tel" id="phone_{{ $field->id }}" name="field_{{ $field->id }}" class="form-control form-control-custom phone-input" placeholder="{{ $field->options['placeholder'] ?? '123 456 7890' }}" {{ $field->required ? 'required' : '' }} value="{{ old('field_' . $field->id) }}" style="width: 100%; direction: ltr !important; text-align: left !important; unicode-bidi: embed !important;">
                                                </div>

                                            @elseif ($field->type === 'number')
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-hash"></i></span>
                                                    <input type="number" name="field_{{ $field->id }}" class="form-control form-control-custom" value="{{ old('field_' . $field->id) }}" placeholder="{{ $field->options['placeholder'] ?? '' }}" {{ $field->required ? 'required' : '' }}>
                                                </div>

                                            @elseif ($field->type === 'email')
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                                    <input type="email" name="field_{{ $field->id }}" class="form-control form-control-custom" value="{{ old('field_' . $field->id) }}" placeholder="{{ $field->options['placeholder'] ?? '' }}" {{ $field->required ? 'required' : '' }}>
                                                </div>

                                            @elseif ($field->type === 'date')
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-calendar3"></i></span>
                                                    <input type="date" name="field_{{ $field->id }}" class="form-control form-control-custom" value="{{ old('field_' . $field->id) }}" placeholder="{{ $field->options['placeholder'] ?? '' }}" {{ $field->required ? 'required' : '' }}>
                                                </div>

                                            @elseif ($field->type === 'select')
                                                @php
                                                    $optionNotes = $field->options['option_notes'] ?? [];
                                                @endphp
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-list-ul"></i></span>
                                                    <select name="field_{{ $field->id }}" id="select_field_{{ $field->id }}" class="form-select form-select-custom" onchange="handleDynamicOther(this, {{ $field->id }})" {{ $field->required ? 'required' : '' }}>
                                                        <option value="">{{ $field->options['placeholder'] ?? __('messages.Choose') ?? 'Choose an option...' }}</option>
                                                        @if (is_array($field->options))
                                                            @php
                                                                $choices = isset($field->options['choices']) ? $field->options['choices'] : (is_array($field->options) ? $field->options : []);
                                                                $choices = array_filter($choices, function($val, $key) {
                                                                    return !in_array($key, ['placeholder', 'description', 'bold', 'italic', 'align', 'columns', 'option_notes', 'raw_options']) && !in_array($val, ['placeholder', 'description', 'bold', 'italic', 'align', 'columns']);
                                                                }, ARRAY_FILTER_USE_BOTH);
                                                            @endphp
                                                            @foreach ($choices as $option)
                                                                @php
                                                                    $attachedNote = $optionNotes[$option] ?? '';
                                                                @endphp
                                                                <option value="{{ $option }}" data-note="{{ $attachedNote }}" {{ old('field_' . $field->id) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                            @endforeach
                                                        @endif
                                                        <option value="__other__" {{ old('field_' . $field->id) === '__other__' ? 'selected' : '' }}>{{ __('messages.Other') ?? 'Other' }} ({{ __('messages.Please specify...') ?? 'Specify...' }})</option>
                                                    </select>
                                                </div>
                                                <div id="select_note_container_{{ $field->id }}" class="select-note-container mt-2 p-3 rounded-3 {{ !empty($optionNotes[old('field_' . $field->id)]) ? '' : 'd-none' }} animate__animated animate__fadeIn" style="background: rgba(59, 130, 246, 0.08); border-inline-start: 4px solid #2563eb; color: #1e3a8a; border-radius: 12px !important;">
                                                    <div class="d-flex align-items-start gap-2.5">
                                                        <i class="bi bi-info-circle-fill text-primary flex-shrink-0 mt-0.5" style="font-size: 1.15rem;"></i>
                                                        <div>
                                                            <span class="d-block fw-bold small text-primary mb-0.5" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('messages.Important Note / Guideline') ?? 'Important Note / Guideline' }}</span>
                                                            <div id="select_note_text_{{ $field->id }}" class="small fw-semibold" style="line-height: 1.5; color: #1e293b;">{!! form_autolink($optionNotes[old('field_' . $field->id)] ?? '') !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="other_input_container_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id) === '__other__' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                    <input type="text" name="field_{{ $field->id }}_other" id="other_input_{{ $field->id }}" class="form-control form-control-custom" placeholder="{{ __('messages.Please specify other option...') ?? 'Please specify other option...' }}" value="{{ old('field_' . $field->id . '_other') }}">
                                                </div>

                                            @elseif ($field->type === 'checkbox')
                                                <div class="d-flex flex-column gap-2">
                                                    @if (is_array($field->options))
                                                        @php
                                                            $choices = isset($field->options['choices']) ? $field->options['choices'] : (is_array($field->options) ? $field->options : []);
                                                            $choices = array_filter($choices, function($val, $key) {
                                                                return !in_array($key, ['placeholder', 'description', 'bold', 'italic', 'align', 'columns']) && !in_array($val, ['placeholder', 'description', 'bold', 'italic', 'align', 'columns']);
                                                            }, ARRAY_FILTER_USE_BOTH);
                                                        @endphp
                                                        @foreach ($choices as $option)
                                                            <label class="form-check-tile gap-3">
                                                                <input type="checkbox" name="field_{{ $field->id }}[]" value="{{ $option }}" class="form-check-input flex-shrink-0" style="width: 1.25rem; height: 1.25rem;" id="check_{{ $field->id }}_{{ $loop->index }}" {{ is_array(old('field_' . $field->id)) && in_array($option, old('field_' . $field->id)) ? 'checked' : '' }}>
                                                                <span class="small fw-semibold text-secondary">{{ $option }}</span>
                                                            </label>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            @elseif ($field->type === 'yes_no_textbox')
                                                <div class="yes-no-wrapper p-3 rounded-4" style="background: rgba(248, 250, 252, 0.85); border: 1.5px solid #e2e8f0;" id="yes_no_wrapper_{{ $field->id }}">
                                                    <div class="d-flex gap-2 gap-sm-3 mb-2">
                                                        <label class="form-check-tile flex-fill justify-content-center gap-2 cursor-pointer" style="cursor: pointer;">
                                                            <input type="radio" name="field_{{ $field->id }}_answer" value="yes" class="form-check-input" onchange="toggleYesNoDetails({{ $field->id }}, true)" {{ old('field_' . $field->id . '_answer') === 'yes' ? 'checked' : '' }} {{ $field->required ? 'required' : '' }}>
                                                            <span class="fw-bold">{{ __('messages.yes') ?? 'Yes' }}</span>
                                                        </label>
                                                        <label class="form-check-tile flex-fill justify-content-center gap-2 cursor-pointer" style="cursor: pointer;">
                                                            <input type="radio" name="field_{{ $field->id }}_answer" value="no" class="form-check-input" onchange="toggleYesNoDetails({{ $field->id }}, false)" {{ old('field_' . $field->id . '_answer') === 'no' ? 'checked' : '' }} {{ $field->required ? 'required' : '' }}>
                                                            <span class="fw-bold">{{ __('messages.no') ?? 'No' }}</span>
                                                        </label>
                                                    </div>
                                                    <div id="yes_no_details_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id . '_answer') === 'yes' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">{{ $field->options['placeholder'] ?? __('messages.If yes, please provide details...') ?? 'If yes, please provide details...' }}</label>
                                                        <textarea name="field_{{ $field->id }}_details" rows="3" class="form-control form-control-custom" placeholder="{{ $field->options['placeholder'] ?? __('messages.If yes, please provide details...') ?? 'If yes, please provide details...' }}">{{ old('field_' . $field->id . '_details') }}</textarea>
                                                    </div>
                                                </div>

                                            @elseif ($field->type === 'table')
                                                @php
                                                    $cols = $field->options['columns'] ?? $field->options['choices'] ?? [];
                                                    if (empty($cols) && !empty($field->options['options'])) {
                                                        $cols = array_values(array_filter(array_map('trim', explode(',', $field->options['options']))));
                                                    }
                                                    if (empty($cols)) {
                                                        $cols = [__('messages.Position') ?? 'Position', __('messages.Date Submitted') ?? 'Date'];
                                                    }
                                                @endphp
                                                <div class="table-field-wrapper p-2 p-sm-3 rounded-4" style="background: rgba(248, 250, 252, 0.9); border: 1.5px solid #e2e8f0;" data-field-id="{{ $field->id }}">
                                                    <div class="d-flex justify-content-end mb-1 d-md-none">
                                                        <span class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-arrows-expand-horizontal me-1"></i>{{ __('messages.Swipe horizontally to view all columns') ?? 'Swipe horizontally' }}</span>
                                                    </div>
                                                    <div class="table-responsive mb-2" style="-webkit-overflow-scrolling: touch;">
                                                        <table class="table table-bordered bg-white rounded-3 overflow-hidden align-middle mb-0 shadow-sm" id="table_{{ $field->id }}" style="min-width: 460px;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    @foreach ($cols as $col)
                                                                        <th class="small fw-bold text-dark py-2 px-3">{{ $col }}</th>
                                                                    @endforeach
                                                                    <th style="width: 50px;" class="text-center py-2 px-2">#</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbody_{{ $field->id }}">
                                                                <tr class="table-row-item">
                                                                    @foreach ($cols as $col)
                                                                        <td class="p-1.5 p-sm-2">
                                                                            <input type="text" name="field_{{ $field->id }}[0][{{ $col }}]" class="form-control form-control-sm border" placeholder="{{ $col }}" {{ $field->required && $loop->first ? 'required' : '' }} style="min-width: 100px;">
                                                                        </td>
                                                                    @endforeach
                                                                    <td class="text-center p-1.5 p-sm-2">
                                                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle remove-table-row-btn" onclick="removeTableRow(this)" title="{{ __('messages.Delete Row') ?? 'Delete Row' }}" style="width: 28px; height: 28px;">
                                                                            <i class="bi bi-x-lg"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1 d-inline-flex align-items-center gap-1" onclick="addTableRow({{ $field->id }}, {{ json_encode($cols) }})">
                                                        <i class="bi bi-plus-circle"></i> {{ __('messages.Add Row') ?? 'Add Row' }}
                                                    </button>
                                                </div>

                                            @elseif ($field->type === 'static_text')
                                                @php
                                                    $bold = !empty($field->options['bold']);
                                                    $italic = !empty($field->options['italic']);
                                                    $align = $field->options['align'] ?? 'left';
                                                @endphp
                                                <div style="font-weight: {{ $bold ? 'bold' : 'normal' }}; font-style: {{ $italic ? 'italic' : 'normal' }}; text-align: {{ $align }}; color: #334155; line-height: 1.6; margin-bottom: 0.5rem;">
                                                    {!! nl2br(form_autolink($field->label)) !!}
                                                </div>

                                            @elseif ($field->type === 'groups')
                                                @php
                                                    $groupOptions = [];
                                                    foreach ($entities['groups'] ?? [] as $entity) {
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                        $groupOptions[] = [
                                                            'label' => $name,
                                                            'value' => $name,
                                                        ];
                                                    }
                                                    $groupOptions[] = [
                                                        'label' => (__('messages.Other') ?? 'Other') . ' (' . (__('messages.Please specify...') ?? 'Specify...') . ')',
                                                        'value' => '__other__',
                                                    ];
                                                    $placeholderText = $field->options['placeholder'] ?? (__('messages.Select Group...') ?? 'Select Group...');
                                                @endphp
                                                <div class="vue-select-container position-relative" id="vue_select_container_{{ $field->id }}">
                                                    <div data-vue-app="VueSelectWrapper"
                                                         data-name="field_{{ $field->id }}"
                                                         data-options="{{ json_encode($groupOptions) }}"
                                                         data-placeholder="{{ $placeholderText }}"
                                                         data-value="{{ old('field_' . $field->id, '') }}">
                                                    </div>
                                                    <input type="hidden" name="field_{{ $field->id }}" id="hidden_field_{{ $field->id }}" value="{{ old('field_' . $field->id, '') }}" {{ $field->required ? 'required' : '' }}>
                                                </div>
                                                <div id="other_input_container_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id) === '__other__' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                    <input type="text" name="field_{{ $field->id }}_other" id="other_input_{{ $field->id }}" class="form-control form-control-custom" placeholder="{{ __('messages.Please specify other option...') ?? 'Please specify other option...' }}" value="{{ old('field_' . $field->id . '_other') }}">
                                                </div>

                                            @elseif ($field->type === 'cities')
                                                @php
                                                    $cityOptions = [];
                                                    foreach ($entities['cities'] ?? [] as $entity) {
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                        $cityOptions[] = [
                                                            'label' => $name,
                                                            'value' => $name,
                                                        ];
                                                    }
                                                    $cityOptions[] = [
                                                        'label' => (__('messages.Other') ?? 'Other') . ' (' . (__('messages.Please specify...') ?? 'Specify...') . ')',
                                                        'value' => '__other__',
                                                    ];
                                                    $placeholderText = $field->options['placeholder'] ?? (__('messages.Select City...') ?? 'Select City...');
                                                @endphp
                                                <div class="vue-select-container position-relative" id="vue_select_container_{{ $field->id }}">
                                                    <div data-vue-app="VueSelectWrapper"
                                                         data-name="field_{{ $field->id }}"
                                                         data-options="{{ json_encode($cityOptions) }}"
                                                         data-placeholder="{{ $placeholderText }}"
                                                         data-value="{{ old('field_' . $field->id, '') }}">
                                                    </div>
                                                    <input type="hidden" name="field_{{ $field->id }}" id="hidden_field_{{ $field->id }}" value="{{ old('field_' . $field->id, '') }}" {{ $field->required ? 'required' : '' }}>
                                                </div>
                                                <div id="other_input_container_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id) === '__other__' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                    <input type="text" name="field_{{ $field->id }}_other" id="other_input_{{ $field->id }}" class="form-control form-control-custom" placeholder="{{ __('messages.Please specify other option...') ?? 'Please specify other option...' }}" value="{{ old('field_' . $field->id . '_other') }}">
                                                </div>

                                            @elseif ($field->type === 'neighborhoods')
                                                @php
                                                    $neighborhoodOptions = [];
                                                    foreach ($entities['neighborhoods'] ?? [] as $entity) {
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                        $neighborhoodOptions[] = [
                                                            'label' => $name,
                                                            'value' => $name,
                                                        ];
                                                    }
                                                    $neighborhoodOptions[] = [
                                                        'label' => (__('messages.Other') ?? 'Other') . ' (' . (__('messages.Please specify...') ?? 'Specify...') . ')',
                                                        'value' => '__other__',
                                                    ];
                                                    $placeholderText = $field->options['placeholder'] ?? (__('messages.Select Neighborhood...') ?? 'Select Neighborhood...');
                                                @endphp
                                                <div class="vue-select-container position-relative" id="vue_select_container_{{ $field->id }}">
                                                    <div data-vue-app="VueSelectWrapper"
                                                         data-name="field_{{ $field->id }}"
                                                         data-options="{{ json_encode($neighborhoodOptions) }}"
                                                         data-placeholder="{{ $placeholderText }}"
                                                         data-value="{{ old('field_' . $field->id, '') }}">
                                                    </div>
                                                    <input type="hidden" name="field_{{ $field->id }}" id="hidden_field_{{ $field->id }}" value="{{ old('field_' . $field->id, '') }}" {{ $field->required ? 'required' : '' }}>
                                                </div>
                                                <div id="other_input_container_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id) === '__other__' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                    <input type="text" name="field_{{ $field->id }}_other" id="other_input_{{ $field->id }}" class="form-control form-control-custom" placeholder="{{ __('messages.Please specify other option...') ?? 'Please specify other option...' }}" value="{{ old('field_' . $field->id . '_other') }}">
                                                </div>

                                            @elseif ($field->type === 'committees')
                                                @php
                                                    $committeeOptions = [];
                                                    foreach ($entities['committees'] ?? [] as $entity) {
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                        $committeeOptions[] = [
                                                            'label' => $name,
                                                            'value' => $name,
                                                        ];
                                                    }
                                                    $committeeOptions[] = [
                                                        'label' => (__('messages.Other') ?? 'Other') . ' (' . (__('messages.Please specify...') ?? 'Specify...') . ')',
                                                        'value' => '__other__',
                                                    ];
                                                    $placeholderText = $field->options['placeholder'] ?? (__('messages.Select Committee...') ?? 'Select Committee...');
                                                @endphp
                                                <div class="vue-select-container position-relative" id="vue_select_container_{{ $field->id }}">
                                                    <div data-vue-app="VueSelectWrapper"
                                                         data-name="field_{{ $field->id }}"
                                                         data-options="{{ json_encode($committeeOptions) }}"
                                                         data-placeholder="{{ $placeholderText }}"
                                                         data-value="{{ old('field_' . $field->id, '') }}">
                                                    </div>
                                                    <input type="hidden" name="field_{{ $field->id }}" id="hidden_field_{{ $field->id }}" value="{{ old('field_' . $field->id, '') }}" {{ $field->required ? 'required' : '' }}>
                                                </div>
                                                <div id="other_input_container_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id) === '__other__' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                    <input type="text" name="field_{{ $field->id }}_other" id="other_input_{{ $field->id }}" class="form-control form-control-custom" placeholder="{{ __('messages.Please specify other option...') ?? 'Please specify other option...' }}" value="{{ old('field_' . $field->id . '_other') }}">
                                                </div>

                                            @elseif ($field->type === 'servicebodies')
                                                @php
                                                    $serviceBodyOptions = [];
                                                    foreach ($entities['servicebodies'] ?? [] as $entity) {
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                        $serviceBodyOptions[] = [
                                                            'label' => $name,
                                                            'value' => $name,
                                                        ];
                                                    }
                                                    $serviceBodyOptions[] = [
                                                        'label' => (__('messages.Other') ?? 'Other') . ' (' . (__('messages.Please specify...') ?? 'Specify...') . ')',
                                                        'value' => '__other__',
                                                    ];
                                                    $placeholderText = $field->options['placeholder'] ?? (__('messages.Select Service Body...') ?? 'Select Service Body...');
                                                @endphp
                                                <div class="vue-select-container position-relative" id="vue_select_container_{{ $field->id }}">
                                                    <div data-vue-app="VueSelectWrapper"
                                                         data-name="field_{{ $field->id }}"
                                                         data-options="{{ json_encode($serviceBodyOptions) }}"
                                                         data-placeholder="{{ $placeholderText }}"
                                                         data-value="{{ old('field_' . $field->id, '') }}">
                                                    </div>
                                                    <input type="hidden" name="field_{{ $field->id }}" id="hidden_field_{{ $field->id }}" value="{{ old('field_' . $field->id, '') }}" {{ $field->required ? 'required' : '' }}>
                                                </div>
                                                <div id="other_input_container_{{ $field->id }}" class="mt-2 {{ old('field_' . $field->id) === '__other__' ? '' : 'd-none' }} animate__animated animate__fadeIn">
                                                    <input type="text" name="field_{{ $field->id }}_other" id="other_input_{{ $field->id }}" class="form-control form-control-custom" placeholder="{{ __('messages.Please specify other option...') ?? 'Please specify other option...' }}" value="{{ old('field_' . $field->id . '_other') }}">
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                @if ($sectionOpen)
                                    </div></div></div></div> <!-- Close final card body, card, and section item wrapper -->
                                @endif
                            </div>

                            <div class="d-grid mt-4 mt-md-5">
                                <button type="submit" id="submit-btn" class="btn btn-submit-premium btn-lg shadow d-flex align-items-center justify-content-center gap-2">
                                    <span id="btn-text">{{ __('messages.Send') ?? 'Submit Form' }}</span>
                                    <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>
    <script>
        function showLoadingState() {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('btn-text');
            const spinner = document.getElementById('btn-spinner');

            if (btn && text && spinner) {
                btn.disabled = true;
                text.textContent = 'Submitting...';
                spinner.classList.remove('d-none');
            }
        }

        function autolinkText(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            const escaped = div.innerHTML;
            const urlRegex = /\b((https?:\/\/|www\.)[^\s<]+)/gi;
            return escaped.replace(urlRegex, function(url) {
                const href = url.toLowerCase().startsWith('www.') ? 'https://' + url : url;
                return `<a href="${href}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-underline fw-bold">${url}</a>`;
            });
        }

        // Toggle "Other" text input and attached note for dynamic/standard selects
        function handleDynamicOther(selectEl, fieldId) {
            const container = document.getElementById('other_input_container_' + fieldId);
            const input = document.getElementById('other_input_' + fieldId);
            const noteContainer = document.getElementById('select_note_container_' + fieldId);
            const noteText = document.getElementById('select_note_text_' + fieldId);

            if (container) {
                if (selectEl.value === '__other__') {
                    container.classList.remove('d-none');
                    if (input) input.focus();
                } else {
                    container.classList.add('d-none');
                    if (input) input.value = '';
                }
            }

            if (noteContainer && noteText) {
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                const note = selectedOption ? selectedOption.getAttribute('data-note') : null;
                if (note && note.trim() !== '') {
                    noteText.innerHTML = autolinkText(note);
                    noteContainer.classList.remove('d-none');
                } else {
                    noteContainer.classList.add('d-none');
                    noteText.textContent = '';
                }
            }
        }

        // Toggle Yes/No details textarea
        function toggleYesNoDetails(fieldId, isYes) {
            const detailsBox = document.getElementById('yes_no_details_' + fieldId);
            if (!detailsBox) return;

            if (isYes) {
                detailsBox.classList.remove('d-none');
                const textarea = detailsBox.querySelector('textarea');
                if (textarea) textarea.focus();
            } else {
                detailsBox.classList.add('d-none');
                const textarea = detailsBox.querySelector('textarea');
                if (textarea) textarea.value = '';
            }
        }

        // Table repeatable rows handlers
        function addTableRow(fieldId, columns) {
            const tbody = document.getElementById('tbody_' + fieldId);
            if (!tbody) return;

            const rowIndex = tbody.querySelectorAll('.table-row-item').length;
            const tr = document.createElement('tr');
            tr.className = 'table-row-item animate__animated animate__fadeIn';

            columns.forEach(col => {
                const td = document.createElement('td');
                td.className = 'p-1.5 p-sm-2';
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `field_${fieldId}[${rowIndex}][${col}]`;
                input.className = 'form-control form-control-sm border';
                input.placeholder = col;
                input.style.minWidth = '100px';
                td.appendChild(input);
                tr.appendChild(td);
            });

            const actionTd = document.createElement('td');
            actionTd.className = 'text-center p-1.5 p-sm-2';
            actionTd.innerHTML = `
                <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle remove-table-row-btn" onclick="removeTableRow(this)" title="Delete Row" style="width: 28px; height: 28px;">
                    <i class="bi bi-x-lg"></i>
                </button>
            `;
            tr.appendChild(actionTd);

            tbody.appendChild(tr);
        }

        function removeTableRow(btn) {
            const tr = btn.closest('.table-row-item');
            const tbody = tr.closest('tbody');
            if (tbody.querySelectorAll('.table-row-item').length > 1) {
                tr.remove();
            } else {
                // Clear inputs instead of deleting last remaining row
                tr.querySelectorAll('input').forEach(inp => inp.value = '');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const phoneInputs = document.querySelectorAll('.phone-input');
            const itiInstances = [];

            phoneInputs.forEach(input => {
                const iti = window.intlTelInput(input, {
                    initialCountry: "eg",
                    separateDialCode: true,
                    preferredCountries: ["eg", "sa", "ae", "kw", "qa"],
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js",
                });
                itiInstances.push({ input, iti });
            });

            const form = document.getElementById('public-submission-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    itiInstances.forEach(item => {
                        // Put the E.164 number back into the phone input on submit so Laravel validates and stores the full international number
                        item.input.value = item.iti.getNumber();
                    });
                });
            }

            // Listen for Vue select value change events
            document.addEventListener('picker-change', function(e) {
                const el = e.target.closest('[data-vue-app="VueSelectWrapper"]') || e.target;
                const fieldName = el.getAttribute('data-name');
                if (!fieldName) return;
                const hiddenInput = document.getElementById(`hidden_${fieldName}`);
                if (hiddenInput) {
                    hiddenInput.value = e.detail || '';
                }
                const fieldId = fieldName.replace('field_', '');
                const otherContainer = document.getElementById(`other_input_container_${fieldId}`);
                const otherInput = document.getElementById(`other_input_${fieldId}`);
                if (otherContainer) {
                    if (e.detail === '__other__') {
                        otherContainer.classList.remove('d-none');
                        if (otherInput) otherInput.focus();
                    } else {
                        otherContainer.classList.add('d-none');
                        if (otherInput) otherInput.value = '';
                    }
                }
            });
        });
    </script>
</x-frontend.layout>
