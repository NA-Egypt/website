@props(['city'])

<div class="neo-list-item d-flex align-items-center justify-content-between mb-1" style="border-bottom: 1px dashed var(--glass-border);">
  <div class="d-flex align-items-center">
    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #3b82f6;">
       <i class="bi bi-geo-alt-fill"></i>
    </div>
    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
        @if(app()->getLocale() === 'ar')
          {{ $city->ar_name }}
        @else
          {{ $city->en_name }}
        @endif
    </h6>
  </div>
  <div>
       <span class="neo-badge neo-badge-primary">{{ $slot }}</span>
  </div>
</div>
