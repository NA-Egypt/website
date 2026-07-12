<x-layout>

    <x-backhead>{{ __('messages.Manage Subscribers') ?? 'Manage Subscribers' }}</x-backhead>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success shadow-sm d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <x-button-a href="{{ route('subscribers.create') }}" color="primary" name="{{ __('messages.Add Subscriber') ?? 'Add Subscriber' }}" />
                <a href="{{ route('subscribers.export') }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                    {{ __('messages.Export CSV') ?? 'Export CSV' }}
                </a>
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="main-tables text-center table table-bordered display" id="example">
                <thead>
                    <tr>
                        <th>{{ __('messages.Email') ?? 'Email' }}</th>
                        <th>{{ __('messages.Status') ?? 'Status' }}</th>
                        <th>{{ __('messages.Created At') ?? 'Created At' }}</th>
                        <th>{{ __('messages.Control') ?? 'Control' }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($subscribers as $subscriber)
                    <tr>
                        <td>{{ $subscriber->email }}</td>
                        <td>
                            @if ($subscriber->hasVerifiedEmail())
                                <span class="badge bg-success">{{ __('messages.Verified') ?? 'Verified' }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('messages.Unverified') ?? 'Unverified' }}</span>
                            @endif
                        </td>
                        <td>{{ $subscriber->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('subscribers.toggle-verification', $subscriber) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @if ($subscriber->hasVerifiedEmail())
                                        <button type="submit" class="btn btn-outline-warning btn-sm">
                                            {{ __('messages.Unverify') ?? 'Unverify' }}
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            {{ __('messages.Verify') ?? 'Verify' }}
                                        </button>
                                    @endif
                                </form>

                                <form action="{{ route('subscribers.destroy_admin', $subscriber) }}" method="POST" onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this subscriber?') ?? 'Are you sure you want to delete this subscriber?' }}')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        {{ __('messages.Delete') ?? 'Delete' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-layout>
