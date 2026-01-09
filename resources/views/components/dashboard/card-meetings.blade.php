@props(['city'])

<div class="d-flex align-items-center justify-content-between p-3 border-bottom dashed-border">
  <div class="d-flex align-items-center">
    <div class="icon-shape icon-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
       <i class="bi bi-geo-alt-fill"></i>
    </div>
    <h6 class="mb-0 fw-bold">
        @if(app()->getLocale() === 'ar')
          {{ $city->ar_name }}
        @else
          {{ $city->en_name }}
        @endif
    </h6>
  </div>
  <div>
       <span class="badge bg-primary rounded-pill">{{ $slot }}</span>
  </div>
</div>
