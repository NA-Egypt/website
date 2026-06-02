<x-layout>
    <x-backhead>{{ __('messages.IT Change Requests') }} #{{ $changeRequest->id }}</x-backhead>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">{{ $changeRequest->subject }}</h5>
                <div>
                    @if($changeRequest->status === 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.9rem;"><i class="bi bi-clock-history"></i> {{ __('messages.Pending') }}</span>
                    @elseif($changeRequest->status === 'in_progress')
                        <span class="badge bg-primary px-3 py-2" style="font-size: 0.9rem;"><i class="bi bi-gear-fill"></i> {{ __('messages.In Progress') }}</span>
                    @elseif($changeRequest->status === 'completed')
                        <span class="badge bg-success px-3 py-2" style="font-size: 0.9rem;"><i class="bi bi-check-circle-fill"></i> {{ __('messages.Completed') }}</span>
                    @else
                        <span class="badge bg-danger px-3 py-2" style="font-size: 0.9rem;"><i class="bi bi-x-circle-fill"></i> {{ __('messages.Rejected') }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="fw-bold text-muted">{{ __('messages.Requester') }}</div>
                        <div class="fs-5">{{ $changeRequest->user->name ?? 'Unknown' }}</div>
                        <div class="text-muted">{{ $changeRequest->user->email ?? 'Unknown' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-bold text-muted">{{ __('messages.Request Type') }}</div>
                        <div class="fs-5">
                            {{ __('messages.' . $changeRequest->request_type) ?? ucfirst(str_replace('_', ' ', $changeRequest->request_type)) }}
                        </div>
                        <div class="text-muted">{{ __('messages.Date') }}: {{ $changeRequest->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <div class="fw-bold text-muted mb-2">{{ __('messages.Description') }}</div>
                    <div class="p-3 bg-light rounded border" style="white-space: pre-wrap; min-height: 150px;">{{ $changeRequest->description }}</div>
                </div>

                @if($changeRequest->attachment_path)
                    <div class="mb-4">
                        <div class="fw-bold text-muted mb-2">{{ __('messages.Attachment') }}</div>
                        <a href="{{ route('change-requests.download-attachment', $changeRequest->id) }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-file-earmark-arrow-down"></i> {{ __('messages.Download Attachment') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if(auth()->user()->hasRole('super admin'))
            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning-subtle fw-bold">
                    <i class="bi bi-shield-lock-fill"></i> {{ __('messages.Update Status') }} (Admin Panel)
                </div>
                <div class="card-body">
                    <form action="{{ route('change-requests.update-status', $changeRequest->id) }}" method="POST" class="row align-items-end g-3">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-6">
                            <label for="statusSelect" class="form-label fw-bold">{{ __('messages.Status') }}</label>
                            <select name="status" id="statusSelect" class="form-select">
                                <option value="pending" {{ $changeRequest->status === 'pending' ? 'selected' : '' }}>{{ __('messages.Pending') }}</option>
                                <option value="in_progress" {{ $changeRequest->status === 'in_progress' ? 'selected' : '' }}>{{ __('messages.In Progress') }}</option>
                                <option value="completed" {{ $changeRequest->status === 'completed' ? 'selected' : '' }}>{{ __('messages.Completed') }}</option>
                                <option value="rejected" {{ $changeRequest->status === 'rejected' ? 'selected' : '' }}>{{ __('messages.Rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning w-100 fw-bold">
                                <i class="bi bi-save"></i> {{ __('messages.Update Status') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="mb-5">
            <a href="{{ route('change-requests.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('messages.Back to Requests') }}
            </a>
        </div>
    </div>
</x-layout>
