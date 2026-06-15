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
        }
        .phone-input {
            direction: ltr !important;
            text-align: left !important;
        }
        .iti__country-list {
            z-index: 1050 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 animate__animated animate__fadeIn" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border-radius: 14px;">
                        <ul class="mb-0 py-1">
                            @foreach ($errors->all() as $error)
                                <li class="small fw-semibold">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card glass-form-card border-0">
                    <!-- Premium Light Form Header -->
                    <div class="text-center py-5 px-4 border-bottom" style="background: rgba(255, 255, 255, 0.3); border-color: rgba(0, 0, 0, 0.05) !important;">
                        @if ($form->type === 'survey')
                            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary shadow-sm" style="width: 72px; height: 72px; background-color: rgba(37, 99, 235, 0.1) !important;">
                                <i class="bi {{ $form->settings['icon'] ?? 'bi-clipboard2-data' }}" style="font-size: 2.25rem;"></i>
                            </div>
                            <h2 class="fw-bold mb-3" style="color: #0f172a !important; font-size: 1.85rem; letter-spacing: -0.5px;">{{ $form->title }}</h2>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: rgba(37, 99, 235, 0.1) !important; border-color: rgba(37, 99, 235, 0.2) !important;">
                                {{ __('messages.Survey') ?? 'Survey' }}
                            </span>
                        @else
                            <h2 class="fw-bold mb-3" style="color: #0f172a !important; font-size: 1.85rem; letter-spacing: -0.5px;">{{ $form->title }}</h2>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: rgba(16, 185, 129, 0.1) !important; border-color: rgba(16, 185, 129, 0.2) !important;">
                                {{ __('messages.Event Registration') ?? 'Event Registration' }}
                            </span>
                        @endif
                    </div>

                    <!-- Form Input Fields -->
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('forms.submit.public', $form->slug) }}" method="POST" id="public-submission-form" onsubmit="showLoadingState()">
                            @csrf

                            <div class="row g-4">
                                @foreach ($form->fields as $field)
                                    @php
                                        // All fields span full width (col-12) to match clean mobile vertical stacking
                                        $colClass = 'col-12';
                                        $isStatic = $field->type === 'static_text';
                                    @endphp
                                    <div class="{{ $colClass }}">
                                        @if (!$isStatic)
                                            <label class="form-label fw-bold small mb-1 d-block" style="color: #0f172a !important;">
                                                {{ $field->label }}
                                                @if ($field->required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            @if (!empty($field->options['description']))
                                                <div class="text-muted small mb-2" style="font-size: 0.8rem; line-height: 1.4;">
                                                    {{ $field->options['description'] }}
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
                                            <div>
                                                <input type="tel" id="phone_{{ $field->id }}" name="field_{{ $field->id }}" class="form-control form-control-custom phone-input" placeholder="{{ $field->options['placeholder'] ?? '123 456 7890' }}" {{ $field->required ? 'required' : '' }} value="{{ old('field_' . $field->id) }}" style="width: 100%;">
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
                                            <div class="input-group-custom">
                                                <span class="input-icon"><i class="bi bi-list-ul"></i></span>
                                                <select name="field_{{ $field->id }}" class="form-select form-select-custom" {{ $field->required ? 'required' : '' }}>
                                                    <option value="">{{ $field->options['placeholder'] ?? __('messages.Choose') ?? 'Choose an option...' }}</option>
                                                    @if (is_array($field->options))
                                                        @php
                                                            $choices = isset($field->options['choices']) ? $field->options['choices'] : (is_array($field->options) ? $field->options : []);
                                                            $choices = array_filter($choices, function($val, $key) {
                                                                return !in_array($key, ['placeholder', 'description', 'bold', 'italic', 'align']) && !in_array($val, ['placeholder', 'description', 'bold', 'italic', 'align']);
                                                            }, ARRAY_FILTER_USE_BOTH);
                                                        @endphp
                                                        @foreach ($choices as $option)
                                                            <option value="{{ $option }}" {{ old('field_' . $field->id) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                        @elseif ($field->type === 'checkbox')
                                            <div class="d-flex flex-column gap-2">
                                                @if (is_array($field->options))
                                                    @php
                                                        $choices = isset($field->options['choices']) ? $field->options['choices'] : (is_array($field->options) ? $field->options : []);
                                                        $choices = array_filter($choices, function($val, $key) {
                                                            return !in_array($key, ['placeholder', 'description', 'bold', 'italic', 'align']) && !in_array($val, ['placeholder', 'description', 'bold', 'italic', 'align']);
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

                                        @elseif ($field->type === 'static_text')
                                            @php
                                                $bold = !empty($field->options['bold']);
                                                $italic = !empty($field->options['italic']);
                                                $align = $field->options['align'] ?? 'left';
                                            @endphp
                                            <div style="font-weight: {{ $bold ? 'bold' : 'normal' }}; font-style: {{ $italic ? 'italic' : 'normal' }}; text-align: {{ $align }}; color: #334155; line-height: 1.6; margin-bottom: 0.5rem;">
                                                {!! nl2br(e($field->label)) !!}
                                            </div>

                                        @elseif ($field->type === 'groups')
                                            <select name="field_{{ $field->id }}" class="form-select form-select-custom select2" {{ $field->required ? 'required' : '' }}>
                                                <option value="">{{ $field->options['placeholder'] ?? __('messages.Select Group...') ?? 'Select Group...' }}</option>
                                                @foreach ($entities['groups'] ?? [] as $entity)
                                                    @php
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                    @endphp
                                                    <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>

                                        @elseif ($field->type === 'cities')
                                            <select name="field_{{ $field->id }}" class="form-select form-select-custom select2" {{ $field->required ? 'required' : '' }}>
                                                <option value="">{{ $field->options['placeholder'] ?? __('messages.Select City...') ?? 'Select City...' }}</option>
                                                @foreach ($entities['cities'] ?? [] as $entity)
                                                    @php
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                    @endphp
                                                    <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>

                                        @elseif ($field->type === 'neighborhoods')
                                            <select name="field_{{ $field->id }}" class="form-select form-select-custom select2" {{ $field->required ? 'required' : '' }}>
                                                <option value="">{{ $field->options['placeholder'] ?? __('messages.Select Neighborhood...') ?? 'Select Neighborhood...' }}</option>
                                                @foreach ($entities['neighborhoods'] ?? [] as $entity)
                                                    @php
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                    @endphp
                                                    <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>

                                        @elseif ($field->type === 'committees')
                                            <select name="field_{{ $field->id }}" class="form-select form-select-custom select2" {{ $field->required ? 'required' : '' }}>
                                                <option value="">{{ $field->options['placeholder'] ?? __('messages.Select Committee...') ?? 'Select Committee...' }}</option>
                                                @foreach ($entities['committees'] ?? [] as $entity)
                                                    @php
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                    @endphp
                                                    <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>

                                        @elseif ($field->type === 'servicebodies')
                                            <select name="field_{{ $field->id }}" class="form-select form-select-custom select2" {{ $field->required ? 'required' : '' }}>
                                                <option value="">{{ $field->options['placeholder'] ?? __('messages.Select Service Body...') ?? 'Select Service Body...' }}</option>
                                                @foreach ($entities['servicebodies'] ?? [] as $entity)
                                                    @php
                                                        $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                    @endphp
                                                    <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" id="submit-btn" class="btn btn-submit-premium btn-lg shadow d-flex align-items-center justify-content-center gap-2">
                                    <span id="btn-text">{{ __('messages.Send') ?? 'Submit Form' }}</span>
                                    <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            const phoneInputs = document.querySelectorAll('.phone-input');
            const itiInstances = [];

            phoneInputs.forEach(input => {
                const iti = window.intlTelInput(input, {
                    initialCountry: "eg",
                    preferredCountries: ["eg", "ae", "sa", "qa", "kw"],
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
        });
    </script>
</x-frontend.layout>
