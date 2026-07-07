<x-layout>
    <div class="container-fluid px-2 px-sm-3 mt-4 mb-5 mx-auto" style="max-width: 1200px; width: 100%;">
        <x-backhead>{{__("messages.Group information for") }}
            @if(app()->getLocale() === 'ar')
                {{$directOnlineGroup->ar_name}}
            @else
                {{$directOnlineGroup->en_name}}
            @endif
        </x-backhead>

        {{-- Consolidated Group Profile Card --}}
        <div class="glass-card p-3 p-md-4 rounded-4 mb-5 shadow-sm mt-4">
            {{-- Header Details --}}
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--text-primary);">
                    <i class="bi bi-shield-check me-2"></i> 
                    {{ __('messages.Group Details') ?? 'Group Details' }} ({{ __('messages.legend_online') }})
                </h4>
                
                {{-- Actions --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <x-button-a href="{{ route('direct-online-group.edit', $directOnlineGroup->id) }}" color='primary' name="{{ __('messages.Edit Group') }}" class="btn-sm rounded-pill px-3 py-2 shadow-sm" />
                </div>
            </div>

            {{-- Grid details --}}
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                            <span class="text-secondary small d-block mb-1"><i class="bi bi-translate me-1 text-primary"></i> {{ __('messages.Arabic Group Name') }}</span>
                            <span class="fw-bold text-dark">{{ $directOnlineGroup->ar_name }}</span>
                        </div>

                        <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                            <span class="text-secondary small d-block mb-1"><i class="bi bi-globe me-1 text-primary"></i> {{ __('messages.English Group Name') }}</span>
                            <span class="fw-bold text-dark">{{ $directOnlineGroup->en_name }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                            <span class="text-secondary small d-block mb-1"><i class="bi bi-person-badge me-1 text-primary"></i> {{ __('messages.Arabic GSR Name') }}</span>
                            <span class="fw-bold text-dark">{{ $directOnlineGroup->ar_gsr_name ?: 'N/A' }}</span>
                        </div>

                        <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                            <span class="text-secondary small d-block mb-1"><i class="bi bi-person-badge-fill me-1 text-primary"></i> {{ __('messages.English GSR Name') }}</span>
                            <span class="fw-bold text-dark">{{ $directOnlineGroup->en_gsr_name ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.015); border: 1px solid var(--glass-border);">
                        <span class="text-secondary small d-block mb-1"><i class="bi bi-pin-map me-1 text-primary"></i> Zoom Link</span>
                        <span class="fw-bold text-dark text-break">
                            <a href="{{ $directOnlineGroup->location }}" target="_blank" class="text-primary text-decoration-underline">{{ $directOnlineGroup->location }}</a>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Contacts --}}
            <div class="d-flex flex-wrap gap-2 align-items-center mt-4 pt-3 border-top" style="border-color: var(--glass-border) !important;">
                <span class="text-secondary small me-2"><i class="bi bi-person-lines-fill"></i> {{ __('messages.Contacts') ?? 'Contacts' }}:</span>
                @if($directOnlineGroup->user)
                    <a href="mailto:{{ $directOnlineGroup->user->email }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center overflow-hidden" style="max-width: 100%;">
                        <i class="bi bi-envelope me-2 flex-shrink-0"></i>
                        <span class="text-truncate" style="max-width: 180px; display: inline-block; vertical-align: middle;">{{ $directOnlineGroup->user->email }}</span>
                    </a>
                @endif
                @if($directOnlineGroup->phone)
                    <a href="tel:{{ $directOnlineGroup->phone }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center" dir="ltr">
                        <i class="bi bi-telephone me-2 flex-shrink-0"></i> {{ $directOnlineGroup->phone }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Meetings Section --}}
        <div class="glass-card p-3 p-md-4 rounded-4 mt-4 mt-md-5">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 border-bottom pb-3 mb-4" style="border-color: var(--glass-border) !important;">
                <h4 class="mb-0 fw-bold" style="color: var(--text-primary);"><i class="bi bi-calendar-event me-2"></i> {{ __('messages.Meetings') }}</h4>
                <x-button-a href="{{ route('meeting.create') }}" color='outline-primary' name="{{__('messages.Add') . ' ' . __('messages.Meeting')}}" class="rounded-pill" />
            </div>
            
            @if($meetings->count() > 0)
                <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($meetings as $meeting)
                        <div class="col">
                            <div class="glass-card h-100 p-3 p-md-4 rounded-4 border position-relative transition-hover" style="border-color: var(--glass-border) !important; background: rgba(0,0,0,0.01);">
                                
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

                                {{-- Notes/Description --}}
                                @if($meeting->notes)
                                    <div class="p-3 rounded-3 mb-3" style="background: rgba(0,0,0,0.03); border: 1px dashed var(--glass-border);">
                                        <p class="mb-0 text-secondary small" style="line-height: 1.6;">{{ $meeting->notes }}</p>
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
            @endif
        </div>
    </div>
</x-layout>
