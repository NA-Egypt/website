<h1 {{ $attributes->merge(['class' => 'text-center text-gradient', 'style' => 'font-weight: 700;']) }}>{{ $slot }}</h1>
<div class="row justify-content-center w-80 position-relative" style="background: #fffce0;border: 1px #dbd06a solid;text-align: center;padding: 10px;border-radius: 10px;">
<p style="margin-bottom: 0 !important">{{ __('messages.thewarning') }}</p>
</div><br />