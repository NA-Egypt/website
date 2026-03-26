@props(['name', 'label'])
<div class="form-check">
    <input class="form-check-input" type="checkbox" value="" id="{{$name}}" name="{{$name}}">
    <label class="form-check-label" for="{{$name}}">
        {{$label}}
    </label>
</div>