@props(['city'])

<div class="best-product-item">
  <div class="d-flex align-items-center gap-3">
    <div class="product-info">
      <p class="product-name mb-1 text-primary group-name fs-md-1 ">
        <a href="{{ route('searches.city', $city->id) }}" class="text-danger">

          @if(app()->getLocale() === 'ar')
            {{ $city->ar_name }}
          @else
            {{ $city->en_name }}
          @endif
        </a>
      </p>
    </div>
    <div class="sales-count ms-auto">
      <p class="mb-0">
        {{ $slot }}
      </p>
    </div>
  </div>
</div>

<x-divider />
