@props(['width', 'name'])

<div class="categories">
    <div class="progress-wrapper">
        <p class="mb-2">{{ $name }} <span class="float-end">{{ $width }}</span></p>
        <div class="progress" style="height: 6px;">
        {{-- {{ $slot }} --}}
        <div {{ $attributes->merge(["class"=>"progress-bar"]) }}  role="progressbar" style="width: {{$width}};"></div>
        </div>
    </div>
    <x-divider class="my-3" />
</div>