<div class="text-center w-100 mb-2">
    <h1 {{ $attributes->merge(['class' => 'text-center text-gradient d-inline-block mx-auto', 'style' => 'font-weight: 700; line-height: 1.55; padding-bottom: 6px; padding-top: 2px; overflow: visible; text-align: center !important; margin: 0 auto; display: inline-block;']) }}>{{ $slot }}</h1>
</div>
<div class="alert alert-warning text-center border-warning-subtle py-3 fade-in mx-auto" role="alert" style="background: #fffce0; border: 1px #dbd06a solid; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); color: #856404; font-weight: 600; font-size: 0.95rem; margin: 0 auto 20px; max-width: 820px; width: 100%;">
    <p class="m-0 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
        <span>{{ __('messages.thewarning') }}</span>
    </p>
</div>