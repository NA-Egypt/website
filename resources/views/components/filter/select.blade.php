@props(['options' => '', 'name', 'label'])

@php
        $default = [
            'data-allow-clear' => "true",
            'class' => "select2",
            'name' => $name,
        ];

        // Determine the field based on locale
        $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';
@endphp

<x-forms.label :$name :$label />

<style>
    /* Clean, non-collapsible Select2 styling for Bootstrap 5 */
    .select2-container {
        display: block !important;
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.5rem !important;
        position: relative !important;
        display: block !important;
        box-shadow: none !important;
    }
    .select2-container--open .select2-selection--single {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #212529 !important;
        line-height: 36px !important;
        font-size: 0.9rem !important;
        display: block !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        text-align: right !important;
        padding-right: 0.75rem !important;
        padding-left: 2rem !important;
    }
    .select2-container[dir="ltr"] .select2-selection--single .select2-selection__rendered {
        text-align: left !important;
        padding-left: 0.75rem !important;
        padding-right: 2rem !important;
    }
    .select2-container .select2-selection--single .select2-selection__placeholder {
        color: #6c757d !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        position: absolute !important;
        top: 0 !important;
        width: 25px !important;
    }
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__arrow {
        left: 6px !important;
        right: auto !important;
    }
    .select2-container[dir="ltr"] .select2-selection--single .select2-selection__arrow {
        right: 6px !important;
        left: auto !important;
    }
    .select2-dropdown {
        border: 1px solid #ced4da !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        z-index: 999999 !important;
        background-color: #ffffff !important;
    }
    .select2-results__option {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.9rem !important;
        color: #212529 !important;
    }
</style>

    <div x-init="init()"
        x-data="{
            model: $wire.entangle('{{ $attributes->wire('model')->value() }}'){{ $attributes->wire('model')->hasModifier('live') ? '.live' : '' }},
            isSyncing: false,
            initSelect2() {
                let $select = $(this.$refs.select);
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                
                let select = $select.select2({
                    width: '100%',
                    placeholder: '{{ __('messages.Choose') }} {{ $label }}...',
                    allowClear: true,
                    dir: '{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}',
                    dropdownParent: $(document.body)
                });

                let valToSet = this.model !== undefined && this.model !== null ? this.model : ($select.val() || '');
                if (valToSet) {
                    this.isSyncing = true;
                    select.val(valToSet).trigger('change.select2');
                    this.isSyncing = false;
                }

                select.off('change.select2-bind').on('change.select2-bind', (e) => {
                    if (this.isSyncing) return;
                    let val = select.val() || '';
                    if (this.model !== val) {
                        this.model = val;
                    }
                });

                this.$watch('model', (value) => {
                    let currentVal = select.val() || '';
                    let newPropsVal = value || '';
                    if (currentVal !== newPropsVal) {
                        this.isSyncing = true;
                        select.val(newPropsVal || null).trigger('change.select2');
                        this.isSyncing = false;
                    }
                });

                const observer = new MutationObserver(() => {
                    select.prop('disabled', this.$refs.select.disabled).trigger('change.select2');
                });
                observer.observe(this.$refs.select, { attributes: true, attributeFilter: ['disabled'] });
            },
            init() {
                let self = this;
                const start = () => {
                    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
                        self.initSelect2();
                        return true;
                    }
                    return false;
                };

                if (!start()) {
                    let attempts = 0;
                    let timer = setInterval(() => {
                        attempts++;
                        if (start() || attempts > 100) {
                            clearInterval(timer);
                        }
                    }, 50);
                }
            }
        }"
        wire:ignore
        class="w-100"
    >
        <select
            x-ref="select"
            {{ $attributes->whereDoesntStartWith('wire:model')->merge($default) }}
        >
            <option value="">{{ __('messages.Choose') }} {{ $label }}...</option>
            @if($name === 'day')
                <option value="all">{{ __('messages.All Days') }}</option>
            @endif
            @if($options)
                @foreach($options as $option)
                    @if($name === 'type')
                        <option value="open">
                            {{ __('messages.open') }} {{ isset($openCount) ? '('.$openCount.')' : '' }}
                        </option>
                        <option value="closed">
                            {{ __('messages.closed') }} {{ isset($closedCount) ? '('.$closedCount.')' : '' }}
                        </option>
                    @else
                        <option value="{{ $name === 'recurrence' ? ($option->id ?? $option->$field) : $option->$field }}">
                            {{ $option->$field }} {{ isset($option->meetings_count) ? '('.$option->meetings_count.')' : '' }}
                        </option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>
