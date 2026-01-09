@props(['groups'])

<div class="groups-container">
    @foreach ($groups as $group)
    <div class="group-item p-3 mb-2 border rounded-3 bg-light shadow-sm position-relative">
        <div class="d-flex justify-content-between align-items-start">
            <div class="ms-2">
                <h6 class="mb-1 fw-bold text-primary">
                    <a href="{{ route('searches.meeting', ['id' => $group->id]) }}" class="text-decoration-none">
                        @if(app()->getLocale() === 'ar')
                            {{ $group->ar_name }}
                        @else
                            {{ $group->en_name }}
                        @endif
                    </a>
                </h6>
                <div class="d-flex align-items-center gap-2 mt-2">
                     <span class="badge bg-white text-secondary border border-secondary fw-normal">
                         <i class="bi bi-geo-alt me-1"></i>
                        @if(app()->getLocale() === 'ar')
                            {{ $group->neighborhood->ar_name }}
                        @else
                            {{ $group->neighborhood->en_name }}
                        @endif
                     </span>
                     <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal">
                        @if(app()->getLocale() === 'ar')
                            {{ $group->serviceBody->ar_name }}
                        @else
                            {{ $group->serviceBody->en_name }}
                        @endif
                     </span>
                </div>
            </div>
            
            <div class="text-end">
                <a href="{{ route('searches.city', $group->neighborhood->city->id) }}" class="badge bg-info bg-opacity-10 text-info border border-info text-decoration-none mb-1 d-block">
                    {{ $group->neighborhood->city->name }}
                </a>
                <small class="text-muted d-block" style="font-size: 0.75rem;">
                    {{ $group->meetings->count() }} {{ __('messages.Meetings') }}
                </small>
            </div>
        </div>
    </div>
    @endforeach
</div>