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
            @if($name === 'type')
                <option value="open" {{ request('type') == 'open' ? 'selected' : '' }}>
                    {{ __('messages.open') }}
                </option>
                <option value="closed" {{ request('type') == 'closed' ? 'selected' : '' }}>
                    {{ __('messages.closed') }}
                </option>
            @else
            <option
                    value="{{ $option->$field }}"
                    {{ request($name) == $option->$field ? 'selected' : '' }}>
                {{ $option->$field }}
            </option>
          @endif
        @endforeach
    @endif
</select>
