@props([
    'name' => '',
    'id' => '',
    'value' => '',
    'type' => 'date', // 'date', 'time', or 'datetime'
    'placeholder' => '',
    'label' => '',
    'col' => 'col-12'
])

@php
    $isTime = $type === 'time';
    $isDateTime = $type === 'datetime';
    $enableTime = $isDateTime; // Only enable time picker if type is datetime (date mode will disable time picker)
    $timeOnly = $isTime;
    $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
@endphp

<div class="{{ $col }} my-2">
    @if($label)
        <x-forms.label :name="$name" :label="$label" />
    @endif
    <div data-vue-app="VueCtkDateTimePicker"
         data-name="{{ $name }}"
         data-id="{{ $id ?: $name }}"
         data-value="{{ old($name, $value) }}"
         data-enable-time="{{ $enableTime ? 'true' : 'false' }}"
         data-time-only="{{ $timeOnly ? 'true' : 'false' }}"
         data-placeholder="{{ $placeholder }}"
         data-locale="{{ $locale }}">
    </div>
    <x-forms.error :error="$errors->first($name)" />
</div>
