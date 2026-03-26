@props(['name', 'label'])


<div class="d-flex align-items-center justify-content-start mb-2 gap-2">
    <span style="width: 0.5rem; height: 0.5rem; background-color: rgb(21, 0, 161); display: inline-block;"></span>
    <label class="text-primary m-0 p-0" for="{{ $name }}" id = {{$name}}>{{ $label }}</label>
</div>