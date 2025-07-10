{{-- First 4 cards in dashboard --}}

@props(['name', 'qty', 'icon'])
<div class="col-12 col-lg-6 col-xl-6 d-flex">
    <div class="card radius-10 ">
      <div class="card-body custom-shadow">
          <div class="d-flex align-items-center ">
              <div>
                  <p class="mb-0 text-secondary">{{ $name }}</p>
                  <h4 class="my-1">{{ $qty }}</h4>
              </div>
              <div {{ $attributes->merge(["class"=>"widget-icon-large text-white ms-auto"]) }} >
                <i class="bi bi-{{ $icon }}"></i>
              </div>
          </div>
      </div>
    </div>
  </div>