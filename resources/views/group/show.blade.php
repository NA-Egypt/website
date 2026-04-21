<x-layout>
    <x-backhead>{{__("messages.Group information for") }}
        @if(app()->getLocale() === 'ar')
            {{$group->ar_name}}
        @else
            {{$group->en_name}}
        @endif
    </x-backhead>

    <div class="container-fluid mt-4 mb-5">
        
        {{-- Header Actions --}}
        <div class="d-flex justify-content-end mb-4">
            <x-button-a href="{{ route('group.edit', $group->id) }}" color='primary' name="{{  __('messages.Edit Group') }}" class="rounded-pill shadow-sm px-4" />
        </div>

        {{-- Group Information Grid (Dashboard UI/UX) --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
            
            {{-- Arabic Group Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Arabic Group Name') }}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->ar_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-translate"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- English Group Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.English Group Name') }}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->en_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-globe"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Email')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary); font-size: 1rem; word-break: break-all;">{{$group->user->email}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #ec4899, #db2777); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Phone --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Phone')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);" dir="ltr">{{$group->phone}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-telephone"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Locations --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Locations')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary); font-size: 0.95rem; word-break: break-word; overflow-wrap: anywhere;">
                                @if(filter_var($group->location, FILTER_VALIDATE_URL))
                                    <a href="{{ $group->location }}" target="_blank" class="text-primary text-decoration-underline">{{ $group->location }}</a>
                                @else
                                    {{$group->location}}
                                @endif
                            </h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-pin-map"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Body --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Service Body')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->serviceBody->ar_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Neighborhood --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Neighborhood')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->neighborhood->ar_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #64748b, #475569); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Arabic GSR Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.Arabic GSR Name')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->ar_gsr_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #14b8a6, #0d9488); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- English GSR Name --}}
            <div class="col">
                <div class="glass-card h-100 p-4 rounded-4 transition-hover">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary" style="font-size: 0.85rem; font-weight: 500;">{{ __('messages.English GSR Name')}}</p>
                            <h5 class="my-2 fw-bold" style="color: var(--text-primary);">{{$group->en_gsr_name}}</h5>
                        </div>
                        <div class="widgets-icons text-white ms-auto shadow-sm" style="background: linear-gradient(135deg, #0f766e, #115e59); border-radius: 12px; opacity: 0.9;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Meetings Section --}}
        <div class="glass-card p-4 rounded-4 mt-5">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="bi bi-calendar-event me-2"></i> {{ __('messages.Meetings') }}</h4>
                <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" class="rounded-pill" />
            </div>
            
            @if($meetings->count() > 0)
                <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($meetings as $meeting)
                        <div class="col">
                            <div class="glass-card h-100 p-4 rounded-4 border position-relative transition-hover" style="border-color: var(--glass-border) !important; background: rgba(0,0,0,0.01);">
                                
                                {{-- Meeting Header --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1 fw-bold text-primary">
                                            @if(app()->getLocale() === 'ar')
                                                {{ $meeting->day->ar_name }}
                                            @else
                                                {{ $meeting->day->en_name }}
                                            @endif
                                        </h5>
                                        <p class="text-secondary small fw-bold mb-0" dir="ltr">
                                            <i class="bi bi-clock mx-1"></i>
                                            {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                    <div class="badge px-3 py-2 rounded-pill fw-medium" style="background-color: rgba(59, 130, 246, 0.15) !important; color: #2563eb !important; border: 1px solid rgba(59, 130, 246, 0.4) !important;">
                                        @if(app()->getLocale() === 'ar')
                                            {{ __("messages." . $meeting->type) }}
                                        @else
                                            {{ $meeting->type }}
                                        @endif
                                    </div>
                                </div>

                                {{-- Title --}}
                                @if($meeting->title)
                                    <h6 class="fw-bold mb-3">{{ $meeting->title }}</h6>
                                @endif

                                {{-- Topics Tags --}}
                                @if($meeting->topics && $meeting->topics->count() > 0)
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach($meeting->topics as $topic)
                                            <span class="badge rounded-pill px-3 py-2 fw-medium" style="background-color: rgba(14, 165, 233, 0.15) !important; color: #0284c7 !important; border: 1px solid rgba(14, 165, 233, 0.4) !important;">
                                                <i class="bi bi-tag me-1"></i>
                                                @if(app()->getLocale() === 'ar')
                                                    {{ $topic->ar_name }}
                                                @else
                                                    {{ $topic->en_name }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Description --}}
                                @if($meeting->description)
                                    <div class="p-3 rounded-3 mb-3" style="background: rgba(0,0,0,0.03); border: 1px dashed var(--glass-border);">
                                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">{{ $meeting->description }}</p>
                                    </div>
                                @endif

                                {{-- Meeting Options --}}
                                @if($meeting->options->count() > 0)
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        @foreach($meeting->options as $option)
                                            <span class="badge rounded-pill px-2 py-1 fw-medium" style="background-color: rgba(0, 0, 0, 0.03) !important; color: var(--text-secondary) !important; border: 1px solid var(--glass-border) !important;">
                                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                @if(app()->getLocale() === 'ar')
                                                    {{ $option->ar_name }}
                                                @else
                                                    {{ $option->en_name }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="d-flex gap-2 mt-auto pt-3 border-top" style="border-color: var(--glass-border) !important;">
                                    <x-button-a href="{{ route('meeting.edit', $meeting->id) }}"
                                                color='outline-primary'
                                                name="{{ __('messages.Edit Meeting') }}"
                                                class="btn-sm rounded-pill flex-grow-1" />

                                    <x-forms.delete-button
                                            name="{{ __('messages.Delete') }}"
                                            formName='delete-item'
                                            id="{{$meeting->id}}"
                                            routeName="meeting.destroy"
                                            class="btn-sm rounded-pill flex-grow-1" />
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center p-5 rounded-4" style="background: rgba(0,0,0,0.02); border: 1px dashed var(--glass-border);">
                    <i class="bi bi-calendar-x text-secondary" style="font-size: 3rem; opacity: 0.5;"></i>
                    <h5 class="text-secondary mt-3">{{ __('messages.No meetings scheduled') }}</h5>
                </div>
            @endif
        </div>
    </div>
</x-layout>

<style>
.transition-hover {
    transition: all 0.3s ease;
}
.transition-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
}
.widgets-icons {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
/* RTL specific fixes */
[dir="rtl"] .me-1 { margin-left: 0.25rem !important; margin-right: 0 !important; }
[dir="rtl"] .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
[dir="rtl"] .ms-auto { margin-right: auto !important; margin-left: 0 !important; }
</style>