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

@if(app()->getLocale() === 'ar')
<style>
    .select2-container[dir="rtl"] .select2-selection--single .select2-selection__rendered {
        padding-right: 0.75rem !important;
        padding-left: 2.25rem !important;
    }
    .select2-container[dir="rtl"] .select2-selection__arrow {
        right: auto !important;
        left: 0.5rem !important;
    }
    /* Fix the normal form-select for type if it has issues */
    .form-select[dir="rtl"], [dir="rtl"] .form-select {
        padding-right: 0.75rem;
        padding-left: 2.25rem;
        background-position: left 0.75rem center;
    }
</style>
@endif

    <div
        x-data="{
            model: @entangle($attributes->wire('model')),
            init() {
                let select = $(this.$refs.select).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: '{{ __('messages.Choose') }} {{ $label }}...',
                    allowClear: {{ $attributes->has('data-allow-clear') ? 'true' : 'false' }},
                    dir: '{{ app()->getLocale() === "ar" ? "rtl" : "ltr" }}'
                });

                select.on('change', (e) => {
                    this.model = select.val();
                });

                this.$watch('model', (value) => {
                    select.val(value).trigger('change.select2');
                });
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
