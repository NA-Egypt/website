@props(['name', 'label',  'value'=>''])

@php
    $defaults = [
        'id'=>$name,
        'name'=>$name,
        'class'=>'rounded-xl bg-white/10 border border-white/10  w-full',
        'style'=>'height:100px'
];

$classes = $defaults['class'];





$defaults['class'] = $classes;

@endphp


<x-forms.label :$label :$name />

<textarea {{ $attributes->merge($defaults) }} >
    {{ old($name, $value) ?: $slot }}
</textarea>

<x-forms.error :error="$errors->first($name)" />