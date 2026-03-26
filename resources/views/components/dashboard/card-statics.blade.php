@props(['name', 'qty', 'icon'])
<div class="card radius-10 w-100 border-0 shadow-sm h-100">
  <div class="card-body">
      <div class="d-flex align-items-center">
          <div>
              <p class="mb-0 text-muted small text-uppercase fw-bold">{{ $name }}</p>
              <h3 class="my-2 fw-bold text-dark">{{ $qty }}</h3>
          </div>
          <div {{ $attributes->merge(["class"=>"widget-icon-large text-white ms-auto shadow-sm rounded-3 d-flex align-items-center justify-content-center"]) }} style="min-width: 50px; min-height: 50px;">
            <i class="bi bi-{{ $icon }} fs-4"></i>
          </div>
      </div>
  </div>
</div>