@props(['groups'])

<div class="groups-container">
    @foreach ($groups as $group)
    <div class="neo-list-item d-flex justify-content-between align-items-center mb-2" style="border-bottom: 1px solid var(--glass-border);">
        <div class="d-flex align-items-center">
            <div class="ms-3">
                <h6 class="mb-1 fw-bold">
                    <a href="{{ route('searches.meeting', ['id' => $group->id]) }}" class="text-decoration-none" style="color: var(--text-primary);">
                        @if(app()->getLocale() === 'ar')
                            {{ $group->ar_name }}
                        @else
                            {{ $group->en_name }}
                        @endif
                    </a>
                </h6>
                <div class="d-flex align-items-center gap-2 mt-1">
                     <span class="neo-badge neo-badge-info px-2 py-1" style="font-size: 0.7rem;">
                         <i class="bi bi-geo-alt me-1"></i>
                        @if(app()->getLocale() === 'ar')
                            {{ $group->neighborhood->ar_name }}
                        @else
                            {{ $group->neighborhood->en_name }}
                        @endif
                     </span>
                     <span class="neo-badge neo-badge-primary px-2 py-1" style="font-size: 0.7rem;">
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