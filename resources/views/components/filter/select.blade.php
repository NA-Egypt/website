@props(['options' => '', 'name', 'label'])

@php
    $field = app()->getLocale() === 'ar' ? 'ar_name' : 'en_name';
    $vueOptions = [];
    
    if ($name === 'day') {
        $vueOptions[] = ['label' => __('messages.All Days'), 'value' => 'all'];
    }
    
    if ($options) {
        foreach ($options as $option) {
            if ($name === 'type') {
                $vueOptions[] = ['label' => __('messages.open') . (isset($openCount) ? ' ('.$openCount.')' : ''), 'value' => 'open'];
                $vueOptions[] = ['label' => __('messages.closed') . (isset($closedCount) ? ' ('.$closedCount.')' : ''), 'value' => 'closed'];
            } else {
                $val = $name === 'recurrence' ? ($option->id ?? $option->$field) : $option->$field;
                $optLabel = $option->$field . (isset($option->meetings_count) ? ' ('.$option->meetings_count.')' : '');
                $vueOptions[] = ['label' => $optLabel, 'value' => $val];
            }
        }
    }

    $wireModel = $attributes->wire('model')->value();
@endphp

<x-forms.label :$name :$label />

<div x-data="{
        model: $wire.entangle('{{ $wireModel }}'){{ $attributes->wire('model')->hasModifier('live') ? '.live' : '' }}
     }"
     x-init="$watch('model', value => { window.dispatchEvent(new CustomEvent('update-vue-value-{{ $name }}', { detail: value || '' })) })"
     @picker-change="model = $event.detail || ''"
     wire:ignore
     class="w-100 position-relative">
    
    <div data-vue-app="VueSelectWrapper"
         data-name="{{ $name }}"
         data-options="{{ json_encode($vueOptions) }}"
         data-placeholder="{{ __('messages.Choose') }} {{ $label }}..."
         data-value="">
    </div>
</div>
