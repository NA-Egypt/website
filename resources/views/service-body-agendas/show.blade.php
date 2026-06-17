<x-layout>
    <x-backhead>{{ __('messages.Service Body Agenda Details') ?? 'Service Body Agenda Details' }}</x-backhead>

    <div class="container py-4">
        <div class="glass-card p-4 p-md-5 mb-4 shadow-sm border-0" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 15px;">
            <!-- Logo Header Area -->
            <div class="row align-items-center mb-4 g-3">
                <div class="col-6 text-start">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="NA Egypt Logo" style="max-height: 70px; object-fit: contain;">
                </div>
                <div class="col-6 text-end">
                    @if($agenda->serviceBody && $agenda->serviceBody->logo)
                        <img src="{{ asset('storage/' . $agenda->serviceBody->logo) }}" alt="Service Body Logo" style="max-height: 70px; object-fit: contain; border-radius: 8px;">
                    @endif
                </div>
            </div>

            <!-- Header Info -->
            <div class="border-bottom pb-3 mb-4">
                @php
                    $arabicMonths = [
                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
                        7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                    ];
                    $year = $agenda->meeting_date->format('Y');
                    $monthNum = (int)$agenda->meeting_date->format('m');
                    $monthName = $arabicMonths[$monthNum] ?? $agenda->meeting_date->format('m');
                @endphp
                <h2 class="text-primary fw-bold mb-2">
                    {{ $agenda->serviceBody->ar_name }} - {{ $monthName }} {{ $year }}
                </h2>
                <div class="d-flex flex-wrap gap-3 text-secondary" style="font-size: 0.95rem;">
                    <span><i class="bi bi-calendar-event me-1"></i> <strong>{{ __('messages.Meeting Date') ?? 'Meeting Date' }}:</strong> {{ $agenda->meeting_date->format('Y-m-d') }}</span>
                    <span><i class="bi bi-calendar-check me-1"></i> <strong>{{ __('messages.Writing Date') ?? 'Writing Date' }}:</strong> {{ $agenda->agenda_date->format('Y-m-d') }}</span>
                    <span>
                        <i class="bi bi-info-circle me-1"></i> 
                        <strong>{{ __('messages.Status') ?? 'Status' }}:</strong> 
                        <span class="badge {{ $agenda->status === 'approved' ? 'bg-success' : ($agenda->status === 'submitted' ? 'bg-primary' : 'bg-warning') }}">
                            {{ $agenda->status }}
                        </span>
                    </span>
                    @if($agenda->is_exceptional)
                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('messages.Exceptional Meeting') ?? 'Exceptional Meeting' }}</span>
                    @endif
                </div>
            </div>

            <!-- Groups Section -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-people-fill me-2"></i>{{ __('messages.Service Body Groups') ?? 'Service Body Groups' }}</h5>
                <div class="p-3 border rounded bg-light mb-3">
                    <h6 class="fw-bold text-muted mb-2">{{ __('messages.Registered Groups') ?? 'Registered Groups' }}:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($agenda->serviceBody->groups as $group)
                            <span class="badge bg-secondary p-2 fs-6">{{ $group->ar_name }}</span>
                        @empty
                            <span class="text-muted">{{ __('messages.No registered groups.') ?? 'No registered groups.' }}</span>
                        @endforelse
                    </div>
                </div>

                @if(!empty($agenda->groups_joined))
                    <div class="p-3 border rounded bg-light">
                        <h6 class="fw-bold text-success mb-2"><i class="bi bi-plus-circle-fill me-1"></i> {{ __('messages.New Groups Joined') ?? 'New Groups Joined' }}:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($agenda->groups_joined as $g)
                                <span class="badge bg-success p-2 fs-6">{{ $g }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sections Content -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-file-earmark-text-fill me-2"></i>{{ __('messages.Agenda Body') ?? 'Agenda Body' }}</h5>
                <div class="d-flex flex-column gap-3">
                    @forelse($agenda->body as $index => $section)
                        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: rgba(0, 0, 0, 0.02);">
                            <div class="card-header bg-light fw-bold" style="border-radius: 12px 12px 0 0;">
                                {{ $section['headline'] ?? (__('messages.Section') . ' #' . ($index + 1)) }}
                            </div>
                            <div class="card-body">
                                {!! $section['content'] !!}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">{{ __('messages.No content sections available.') ?? 'No content sections available.' }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Action Controls -->
            <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-3 border-top">
                <a href="{{ route('service-body-agendas.pdf', $agenda->id) }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-pdf"></i> {{ __('messages.Download PDF') ?? 'Download PDF' }}
                </a>

                @if(Auth::check() && (Auth::user()->hasRole('super admin') || Auth::user()->hasRole('rsc')))
                    @if($agenda->status === 'submitted')
                        <form action="{{ route('service-body-agendas.approve', $agenda->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> {{ __('messages.Approve & Publish') ?? 'Approve & Publish' }}
                            </button>
                        </form>
                        <form action="{{ route('service-body-agendas.returnToDraft', $agenda->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning text-white">
                                <i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.Return to Draft') ?? 'Return to Draft' }}
                            </button>
                        </form>
                    @endif
                @endif

                @if(Auth::check() && $agenda->status === 'draft')
                    @if(Auth::user()->hasRole('super admin') || Auth::user()->hasRole('rsc') || (Auth::user()->hasRole('ServiceBody') && Auth::user()->service_body_id === $agenda->service_body_id))
                        <a href="{{ route('service-body-agendas.edit', $agenda->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> {{ __('messages.Edit Draft') ?? 'Edit Draft' }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-layout>
