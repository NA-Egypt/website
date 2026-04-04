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
    <div
        x-data="{
            model: @entangle($attributes->wire('model')),
            init() {
                let select = $(this.$refs.select).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: '{{ $label }}',
                    allowClear: {{ $attributes->has('data-allow-clear') ? 'true' : 'false' }}
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
            <option value="">{{ __('messages.Select a') }} {{ $label }}</option>
            @if($name === 'day')
                <option value="all">{{ __('messages.All Days') }}</option>
            @endif
            @if($options)
                @foreach($options as $option)
                    @if($name === 'type')
                        <option value="open">
                            {{ __('messages.open') }}
                        </option>
                        <option value="closed">
                            {{ __('messages.closed') }}
                        </option>
                    @else
                        <option value="{{ $option->$field }}">
                            {{ $option->$field }}
                        </option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>
