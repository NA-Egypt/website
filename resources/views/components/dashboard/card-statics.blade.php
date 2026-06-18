@props(['name', 'qty', 'icon', 'color-theme' => 'primary', 'description' => null])
<div class="glass-card w-100 h-100 p-0 position-relative overflow-hidden" 
     style="border-color: rgba(var(--bs-{{ $colorTheme }}-rgb), 0.3) !important;">
  
  {{-- Neon Ambient Glow behind the card --}}
  <div class="position-absolute top-0 start-0 w-100 h-100 bg-{{ $colorTheme }}" 
       style="opacity: 0.1; mix-blend-mode: overlay; pointer-events: none;"></div>
       
  <div class="card-body p-4 position-relative z-1">
      <div class="d-flex align-items-center justify-content-between">
          <div>
              <p class="mb-1 small fw-bold" style="color: var(--text-secondary);">{{ $name }}</p>
              <h3 class="my-0 fw-bolder" style="color: var(--text-primary); font-size: 2rem;">{{ $qty }}</h3>
              @if($description)
                  <p class="mb-0 mt-2 small text-muted" style="font-size: 0.8rem;">{{ $description }}</p>
              @endif
          </div>
          <div class="d-flex align-items-center justify-content-center rounded-circle" 
               style="min-width: 55px; min-height: 55px; background: rgba(var(--bs-{{ $colorTheme }}-rgb), 0.15); border: 1px solid rgba(var(--bs-{{ $colorTheme }}-rgb), 0.3); color: var(--bs-{{ $colorTheme }}); box-shadow: 0 0 15px rgba(var(--bs-{{ $colorTheme }}-rgb), 0.2);">
            <i class="bi bi-{{ $icon }} fs-3"></i>
          </div>
      </div>
  </div>
</div>