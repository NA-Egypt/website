@props(['city'])

<div class="neo-list-item d-flex align-items-center justify-content-between mb-1" style="border-bottom: 1px dashed var(--glass-border);">
  <div class="d-flex align-items-center">
    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
       <i class="bi bi-building"></i>
    </div>
    <a href="{{ route('searches.city', $city->id) }}" class="text-decoration-none" style="color: var(--text-primary); transition: color 0.3s; hover: color: #10b981;">
        <h6 class="mb-0 fw-bold">
            @if(app()->getLocale() === 'ar')
              {{ $city->ar_name }}
            @else
              {{ $city->en_name }}
            @endif
        </h6>
    </a>
  </div>
  <div>
       <span class="neo-badge neo-badge-success">{{ $slot }}</span>
  </div>
</div>
