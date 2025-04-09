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
<select {{ $attributes->merge($default) }}>
    <option value="">{{ __('messages.Select a') }} {{ $label }}</option>
    @if($name === 'day')
    <option value="all">{{ __('messages.All Days') }}</option>
    @endif
    @if($options)
        @foreach($options as $option)
            <option
                    value="{{ $option->$field }}"
                    {{ request($name) == $option->$field ? 'selected' : '' }}>
                {{ $option->$field }}
            </option>
        @endforeach
    @endif
</select>
