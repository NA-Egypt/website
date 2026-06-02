<x-layout>
    <x-backhead>{{ __('messages.IT Change Requests') }}</x-backhead>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <a href="{{ route('change-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> {{ __('messages.Request IT Change') }}
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center align-middle mb-0">
                    <thead class="table-light text-uppercase">
                        <tr>
                            <th>{{ __('messages.ID') }}</th>
                            @if(auth()->user()->hasRole('super admin'))
                                <th>{{ __('messages.Requester') }}</th>
                            @endif
                            <th>{{ __('messages.Request Type') }}</th>
                            <th>{{ __('messages.Subject') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                            <th>{{ __('messages.Date') }}</th>
                            <th>{{ __('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($changeRequests as $req)
                            <tr>
                                <td>{{ $req->id }}</td>
                                @if(auth()->user()->hasRole('super admin'))
                                    <td>
                                        <div class="fw-bold">{{ $req->user->name ?? 'Unknown' }}</div>
                                        <small class="text-muted">{{ $req->user->email ?? 'Unknown' }}</small>
                                    </td>
                                @endif
                                <td>
                                    @php
                                        $typeLabel = $req->request_type;
                                    @endphp
                                    <span class="badge bg-light text-dark border">
                                        {{ __('messages.' . $typeLabel) ?? ucfirst(str_replace('_', ' ', $typeLabel)) }}
                                    </span>
                                </td>
                                <td class="text-start">{{ $req->subject }}</td>
                                <td>
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> {{ __('messages.Pending') }}</span>
                                    @elseif($req->status === 'in_progress')
                                        <span class="badge bg-primary"><i class="bi bi-gear-fill"></i> {{ __('messages.In Progress') }}</span>
                                    @elseif($req->status === 'completed')
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> {{ __('messages.Completed') }}</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> {{ __('messages.Rejected') }}</span>
                                    @endif
                                </td>
                                <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('change-requests.show', $req->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> {{ __('messages.Show') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super admin') ? 7 : 6 }}" class="py-4 text-muted">
                                    {{ __('messages.No requests found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            {{ $changeRequests->links() }}
        </div>
    </div>
</x-layout>
