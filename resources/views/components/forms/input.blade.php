@props(['name', 'label', 'value' => '', 'col' => 'col-12'])

@php
    $default = [
        'type' => 'text',
        'class' => "form-control",
        'name' => $name,
        'id' => $name,
        'value' => old($name, $value)
    ];
@endphp

<div class="{{ $col }} my-3">
    <x-forms.label :name="$name" :label="$label" />
    <input {{ $attributes->merge($default) }}>
    <x-forms.error :error="$errors->first($name)" />
</div>
