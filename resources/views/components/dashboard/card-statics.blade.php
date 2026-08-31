@props(['name', 'qty', 'icon', 'color-theme' => 'primary', 'description' => null])
<div class="glass-card w-100 h-100 p-0 position-relative overflow-hidden stat-hover-card" 
     style="border-color: rgba(var(--bs-{{ $colorTheme }}-rgb), 0.25) !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 16px;">
  
  {{-- Neon Ambient Glow behind the card --}}
  <div class="position-absolute top-0 start-0 w-100 h-100 bg-{{ $colorTheme }}" 
       style="opacity: 0.08; filter: blur(10px); pointer-events: none;"></div>
       
  <div class="card-body p-4 position-relative z-1">
      <div class="d-flex align-items-center justify-content-between">
          <div>
              <p class="mb-1 small fw-bold text-uppercase opacity-75" style="color: var(--text-secondary); letter-spacing: 0.5px;">{{ $name }}</p>
              <h3 class="my-0 fw-bolder tracking-tight" style="color: var(--text-primary); font-size: 2.1rem; line-height: 1.25;">{{ $qty }}</h3>
              @if($description)
                  <p class="mb-0 mt-2 small text-muted" style="font-size: 0.825rem;">{{ $description }}</p>
              @endif
          </div>
          <div class="d-flex align-items-center justify-content-center rounded-4 icon-box-glow" 
               style="width: 58px; height: 58px; background: rgba(var(--bs-{{ $colorTheme }}-rgb), 0.12); border: 1px solid rgba(var(--bs-{{ $colorTheme }}-rgb), 0.3); color: var(--bs-{{ $colorTheme }}); box-shadow: 0 4px 15px rgba(var(--bs-{{ $colorTheme }}-rgb), 0.15); transition: transform 0.3s ease;">
            <i class="bi bi-{{ $icon }} fs-3"></i>
          </div>
      </div>
  </div>
</div>

<style>
.stat-hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08) !important;
}
.stat-hover-card:hover .icon-box-glow {
    transform: scale(1.08) rotate(3deg);
}
</style>