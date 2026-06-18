@props(['groups'])

<div class="groups-container">
    @foreach ($groups as $group)
    <div class="neo-list-item group-item d-flex justify-content-between align-items-center mb-2 px-3 py-2" 
         style="background: rgba(255, 255, 255, 0.03); 
                border: 1px solid var(--glass-border); 
                border-radius: 12px; 
                width: 100%;
                box-sizing: border-box;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);">
        <div class="d-flex align-items-center">
            <div class="ms-3">
                <h6 class="mb-1 fw-bold">
                    <a href="{{ route('searches.meeting', ['id' => $group->id]) }}" class="text-decoration-none group-name" style="color: var(--text-primary); transition: color 0.2s;">
                        @if(app()->getLocale() === 'ar')
                            {{ $group->ar_name }}
                        @else
                            {{ $group->en_name }}
                        @endif
                    </a>
                </h6>
                <div class="d-flex align-items-center gap-2 mt-1">
                     <span class="neo-badge neo-badge-info group-neighborhood px-2 py-1" style="font-size: 0.7rem;">
                          <i class="bi bi-geo-alt me-1"></i>
                        @if(app()->getLocale() === 'ar')
                            {{ $group->neighborhood->ar_name }}
                        @else
                            {{ $group->neighborhood->en_name }}
                        @endif
                     </span>
                     <span class="neo-badge neo-badge-primary group-service-body px-2 py-1" style="font-size: 0.7rem;">
                        @if(app()->getLocale() === 'ar')
                            {{ $group->serviceBody->ar_name }}
                        @else
                            {{ $group->serviceBody->en_name }}
                        @endif
                     </span>
                </div>
            </div>
        </div>
        
        <div class="text-end">
            <a href="{{ route('searches.city', $group->neighborhood->city->id) }}" class="badge rounded-pill mb-1 d-block" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9; border: 1px solid rgba(14, 165, 233, 0.3);">
                {{ $group->neighborhood->city->name }}
            </a>
            <small class="d-block" style="font-size: 0.75rem; color: var(--text-secondary);">
                {{ $group->meetings->count() }} {{ __('messages.Meetings') }}
            </small>
        </div>
    </div>
    @endforeach
</div>

<style>
    .group-item:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(59, 130, 246, 0.4) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(59, 130, 246, 0.1) !important;
    }
    .group-item:hover .group-name {
        color: #3b82f6 !important;
    }
</style>