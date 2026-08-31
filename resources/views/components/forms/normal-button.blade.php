@props(['color', 'name'])

<div class="col-12 col-md-6 mt-4">
    <button {{ $attributes->merge(['type'=>"submit", 'class'=>"btn btn-$color px-4"]) }} >{{ $name }}</button>
</div>