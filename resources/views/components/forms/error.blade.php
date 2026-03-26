@props(['error' => false])

@if ($error)
    <p class="text-sm text-danger mt-1">{{ $error }}</p>    
@endif


{{-- @if ($error && $array)
    <p class="text-sm text-danger mt-1">{{ $error }}</p>    
@else
    
@endif --}}