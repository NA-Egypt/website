@props(['city', 'groupsCount', 'meetingsCount'])

<div class="neo-list-item d-flex align-items-center justify-content-between mb-2 p-2 px-3" 
     style="border-bottom: 1px dashed var(--glass-border); 
            background: rgba(255, 255, 255, 0.02); 
            border-radius: 12px;
            transition: all 0.2s;">
  <div class="d-flex align-items-center">
    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
         style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #3b82f6;">
       <i class="bi bi-building"></i>
    </div>
    <a href="{{ route('searches.city', $city->id) }}" class="text-decoration-none" style="color: var(--text-primary); transition: color 0.3s;">
        <h6 class="mb-0 fw-bold">
            @if(app()->getLocale() === 'ar')
              {{ $city->ar_name }}
            @else
              {{ $city->en_name }}
            @endif
        </h6>
    </a>
  </div>
  <div class="d-flex align-items-center gap-2">
       <span class="neo-badge neo-badge-success d-flex align-items-center gap-1" style="font-size: 0.75rem;">
           <i class="bi bi-people-fill"></i>
           {{ $groupsCount }} {{ __('messages.Groups') }}
       </span>
       <span class="neo-badge neo-badge-primary d-flex align-items-center gap-1" style="font-size: 0.75rem;">
           <i class="bi bi-calendar-week"></i>
           {{ $meetingsCount }} {{ __('messages.Meetings') }}
       </span>
  </div>
</div>
