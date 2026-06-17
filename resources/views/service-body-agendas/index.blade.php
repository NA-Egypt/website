<x-layout>
    <x-backhead>{{ __('messages.Service Body Agendas') ?? 'Service Body Agendas' }}</x-backhead>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap g-3">
            <h4 class="fw-bold mb-0 text-secondary">{{ __('messages.Manage Agendas') ?? 'Manage Agendas' }}</h4>
            
            @if(Auth::check() && (Auth::user()->hasRole('ServiceBody') || Auth::user()->hasRole('super admin') || Auth::user()->hasRole('rsc')))
                <a href="{{ route('service-body-agendas.create') }}" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('messages.Create Agenda') ?? 'Create Agenda' }}
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background: rgba(255, 255, 255, 0.9);">
            <div class="card-body p-3">
                <form action="{{ route('service-body-agendas.index') }}" method="GET" class="row g-2 align-items-end">
                    @if($isRsc)
                        <div class="col-md-4 col-sm-6">
                            <label class="form-label small fw-bold text-muted">{{ __('messages.Filter by Service Body') ?? 'Filter by Service Body' }}</label>
                            <select name="service_body_id" class="form-select">
                                <option value="">{{ __('messages.All Service Bodies') ?? 'All Service Bodies' }}</option>
                                @foreach($serviceBodies as $body)
                                    <option value="{{ $body->id }}" {{ request('service_body_id') == $body->id ? 'selected' : '' }}>{{ $body->ar_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label small fw-bold text-muted">{{ __('messages.Search') ?? 'Search' }}</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ __('messages.Search agendas...') ?? 'Search agendas...' }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 col-sm-12 ms-auto d-grid">
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter"></i> {{ __('messages.Filter') ?? 'Filter' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Agendas Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden; background: rgba(255, 255, 255, 0.95);">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary fw-bold">
                        <tr>
                            <th class="ps-4">{{ __('messages.Service Body') ?? 'Service Body' }}</th>
                            <th>{{ __('messages.Writing Date') ?? 'Writing Date' }}</th>
                            <th>{{ __('messages.Meeting Date') ?? 'Meeting Date' }}</th>
                            <th>{{ __('messages.Status') ?? 'Status' }}</th>
                            <th>{{ __('messages.Exceptional') ?? 'Exceptional' }}</th>
                            <th class="text-end pe-4">{{ __('messages.Actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agendas as $agenda)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold text-dark">{{ $agenda->serviceBody->ar_name }}</span>
                                </td>
                                <td>{{ $agenda->agenda_date->format('Y-m-d') }}</td>
                                <td>{{ $agenda->meeting_date->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge {{ $agenda->status === 'approved' ? 'bg-success' : ($agenda->status === 'submitted' ? 'bg-primary' : 'bg-warning') }}">
                                        {{ $agenda->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($agenda->is_exceptional)
                                        <span class="badge bg-danger">{{ __('messages.Yes') ?? 'Yes' }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('service-body-agendas.show', $agenda->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i> {{ __('messages.View') ?? 'View' }}
                                        </a>

                                        @if($agenda->status === 'draft' && (
                                            $isRsc || 
                                            (Auth::check() && Auth::user()->hasRole('ServiceBody') && Auth::user()->service_body_id === $agenda->service_body_id)
                                        ))
                                            <a href="{{ route('service-body-agendas.edit', $agenda->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> {{ __('messages.Edit') ?? 'Edit' }}
                                            </a>
                                            <form action="{{ route('service-body-agendas.destroy', $agenda->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this agenda?') ?? 'Are you sure you want to delete this agenda?' }}');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($isRsc && $agenda->status === 'submitted')
                                            <form action="{{ route('service-body-agendas.approve', $agenda->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-lg"></i> {{ __('messages.Approve') ?? 'Approve' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-4 d-block mb-3"></i>
                                    {{ __('messages.No agendas found.') ?? 'No agendas found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agendas->hasPages())
                <div class="card-footer bg-white border-0 px-4 py-3">
                    {{ $agendas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
