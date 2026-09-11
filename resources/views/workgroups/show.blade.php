<x-layout>

    <x-backhead>{{ app()->getLocale() === 'ar' ? $workgroup->ar_name : $workgroup->en_name }}</x-backhead>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card p-4 p-md-5">
                    {{-- Header with Badges --}}
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 pb-4 border-bottom mb-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($workgroup->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($workgroup->logo))
                                <img src="{{ asset('storage/' . $workgroup->logo) }}" alt="Logo" class="rounded-circle shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white shadow-sm" style="width: 64px; height: 64px;">
                                    <i class="bi bi-people-fill fs-3"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="fw-bold mb-1">{{ app()->getLocale() === 'ar' ? $workgroup->ar_name : $workgroup->en_name }}</h3>
                                <div class="text-muted small mb-2">{{ app()->getLocale() === 'ar' ? $workgroup->en_name : $workgroup->ar_name }}</div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-arrow-return-right me-1"></i>
                                        {{ __('messages.Belongs to') }}: {{ $workgroup->parent ? (app()->getLocale() === 'ar' ? $workgroup->parent->ar_name : $workgroup->parent->en_name) : '-' }}
                                    </span>
                                    <span class="badge {{ $workgroup->workgroup_type === 'permanent' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                        {{ $workgroup->workgroup_type === 'permanent' ? __('messages.Permanent') : __('messages.Temporary') }}
                                    </span>
                                    <span class="badge {{ $workgroup->status === 'active' ? 'bg-success' : ($workgroup->status === 'completed' ? 'bg-info' : 'bg-dark') }}">
                                        {{ __('messages.' . ucfirst($workgroup->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @can('update', $workgroup)
                            <a href="{{ route('workgroup.edit', $workgroup->id) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                <i class="bi bi-pencil-square"></i>
                                <span>{{ __('messages.Edit') }}</span>
                            </a>
                        @endcan
                    </div>

                    {{-- Workgroup Details Grid --}}
                    <div class="row g-4 mb-4">
                        {{-- Leadership --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-person-badge-fill me-2 text-success"></i>
                                    {{ __('messages.Leadership Contact') }}
                                </h6>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('messages.Chairman Name') }}:</span>
                                    <span class="fw-semibold">{{ $workgroup->chairman_name ?: '-' }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('messages.Chairman Phone') }}:</span>
                                    <span class="fw-semibold">{{ $workgroup->chairman_phone ?: '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-muted small d-block">{{ __('messages.Email') }}:</span>
                                    <span class="fw-semibold">{{ $workgroup->email ?: '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Meetings & Location --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100 border">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-calendar-event-fill me-2 text-danger"></i>
                                    {{ __('messages.Workgroup Meetings') }}
                                </h6>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('messages.Schedule Description') }}:</span>
                                    <span class="fw-semibold text-dark">{{ $workgroup->notes ?: '-' }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted small d-block">{{ __('messages.Locations') }}:</span>
                                    @if($workgroup->location && filter_var($workgroup->location, FILTER_VALIDATE_URL))
                                        <a href="{{ $workgroup->location }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info mt-1">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> {{ __('messages.Join / View Location') }}
                                        </a>
                                    @else
                                        <span class="fw-semibold">{{ $workgroup->location ?: '-' }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-muted small d-block">{{ __('messages.Address') }}:</span>
                                    @php
                                        $displayAddress = app()->getLocale() === 'ar' 
                                            ? ($workgroup->ar_address ?: $workgroup->en_address) 
                                            : ($workgroup->en_address ?: $workgroup->ar_address);
                                    @endphp
                                    <span class="fw-semibold">{{ $displayAddress ?: '-' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($workgroup->workgroup_type === 'temporary' && ($workgroup->start_date || $workgroup->end_date))
                            <div class="col-12">
                                <div class="p-3 rounded-3 bg-light border d-flex align-items-center gap-4 flex-wrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-clock-history text-warning fs-5"></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('messages.Target Start Date') }}</small>
                                            <span class="fw-bold">{{ $workgroup->start_date ? $workgroup->start_date->format('Y-m-d') : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-flag-fill text-danger fs-5"></i>
                                        <div>
                                            <small class="text-muted d-block">{{ __('messages.Target End Date') }}</small>
                                            <span class="fw-bold">{{ $workgroup->end_date ? $workgroup->end_date->format('Y-m-d') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('workgroup.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('messages.Back to Workgroups') }}
                        </a>
                        <a href="{{ route('committee-reports.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-text me-1"></i> {{ __('messages.Workgroup Reports') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
