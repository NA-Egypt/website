<h1 {{ $attributes->merge(['class' => 'text-center text-gradient', 'style' => 'font-weight: 700;']) }}>{{ $slot }}</h1>
<div class="alert alert-warning text-center border-warning-subtle py-3 fade-in" role="alert" style="background: #fffce0; border: 1px #dbd06a solid; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); color: #856404; font-weight: 600; font-size: 0.95rem; margin: 0 auto 20px; max-width: 820px; width: 100%;">
    <p class="m-0 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
        <span>{{ __('messages.thewarning') }}</span>
    </p>
</div>