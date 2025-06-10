@props(['name', 'label'])


<div class="d-flex align-items-center mb-2">
    <span class="me-2" style="width: 0.5rem; height: 0.5rem; background-color: rgb(21, 0, 161); display: inline-block;"></span>
    <label class="fw-bold text-primary" for="{{ $name }}" id = {{$name}}>{{ $label }}</label>
</div>