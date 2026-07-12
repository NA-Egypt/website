<x-layout>

    <x-backhead>{{ __('messages.Manage Subscribers') ?? 'Manage Subscribers' }}</x-backhead>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success shadow-sm d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form id="bulk-action-form" method="POST" action="{{ route('subscribers.bulk_action') }}">
            @csrf
            <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <select name="action" class="form-select w-auto">
                        <option value="">{{ __('messages.Select Action') ?? 'Select Action' }}</option>
                        <option value="delete">{{ __('messages.Delete Selected') ?? 'Delete Selected' }}</option>
                        <option value="verify">{{ __('messages.Verify Selected') ?? 'Verify Selected' }}</option>
                        <option value="unverify">{{ __('messages.Unverify Selected') ?? 'Unverify Selected' }}</option>
                    </select>
                    <button type="submit" class="btn btn-danger">
                        {{ __('messages.Apply') ?? 'Apply' }}
                    </button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <x-button-a href="{{ route('subscribers.create') }}" color="primary" name="{{ __('messages.Add Subscriber') ?? 'Add Subscriber' }}" />
                    <a href="{{ route('subscribers.export') }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                        {{ __('messages.Export CSV') ?? 'Export CSV' }}
                    </a>
                    <button type="button" class="btn btn-outline-warning" onclick="startBulkVerification()">
                        <i class="bi bi-shield-check me-1"></i>
                        {{ __('messages.verify_unverified_cleanup') ?? 'Verify Unverified Mailboxes & Cleanup' }}
                    </button>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                <table class="main-tables text-center table table-bordered display" id="example">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                            <th>{{ __('messages.Email') ?? 'Email' }}</th>
                            <th>{{ __('messages.Status') ?? 'Status' }}</th>
                            <th>{{ __('messages.Created At') ?? 'Created At' }}</th>
                            <th>{{ __('messages.Control') ?? 'Control' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($subscribers as $subscriber)
                        <tr>
                            <td><input type="checkbox" name="subscriber_ids[]" class="subscriber-checkbox" value="{{$subscriber->id}}"></td>
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
        </form>
    </div>

    <!-- Verification Progress Modal -->
    <div class="modal fade" id="verifyProgressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="verifyProgressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifyProgressModalLabel">{{ __('messages.Verification Progress') ?? 'Verification Progress' }}</h5>
                </div>
                <div class="modal-body">
                    <p id="verify-status-text" class="text-center mb-3">Initializing...</p>
                    <div class="progress mb-3" style="height: 25px;">
                        <div id="verify-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Processed: <strong id="verify-processed-count">0</strong> / <strong id="verify-total-count">0</strong></span>
                        <span>Deleted Invalid: <strong id="verify-deleted-count" class="text-danger">0</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.subscriber-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
            var action = this.querySelector('select[name="action"]').value;
            if (!action) {
                e.preventDefault();
                alert('{{ __('messages.Please select an action') ?? "Please select an action" }}');
                return;
            }

            var checkedCount = this.querySelectorAll('.subscriber-checkbox:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('{{ __('messages.Please select at least one subscriber') ?? "Please select at least one subscriber" }}');
                return;
            }

            var confirmMsg = '{{ __('messages.Are you sure?') ?? "Are you sure?" }}';
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });

        async function startBulkVerification() {
            var confirmMsg = '{{ __('messages.confirm_bulk_verify') ?? "Are you sure you want to run SMTP verification on all unverified subscribers? Valid emails will be marked as verified, and invalid ones will be deleted immediately." }}';
            if (!confirm(confirmMsg)) {
                return;
            }

            // Show Modal
            var myModal = new bootstrap.Modal(document.getElementById('verifyProgressModal'), {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();

            const statusText = document.getElementById('verify-status-text');
            const progressBar = document.getElementById('verify-progress-bar');
            const processedCountEl = document.getElementById('verify-processed-count');
            const totalCountEl = document.getElementById('verify-total-count');
            const deletedCountEl = document.getElementById('verify-deleted-count');

            try {
                statusText.innerText = "{{ __('messages.verify_fetching_list') ?? 'Fetching subscribers list...' }}";
                const idsResponse = await fetch('{{ route('subscribers.ids') }}');
                const ids = await idsResponse.json();
                
                const total = ids.length;
                totalCountEl.innerText = total;

                if (total === 0) {
                    statusText.innerText = "{{ __('messages.verify_no_subscribers') ?? 'No subscribers to verify.' }}";
                    setTimeout(() => { myModal.hide(); }, 2000);
                    return;
                }

                const batchSize = 10;
                let processed = 0;
                let deletedTotal = 0;

                for (let i = 0; i < total; i += batchSize) {
                    const chunk = ids.slice(i, i + batchSize);
                    var batchText = "{{ __('messages.verify_verifying_batch') ?? 'Verifying batch {batch} of {total}...' }}";
                    statusText.innerText = batchText.replace('{batch}', Math.floor(i / batchSize) + 1).replace('{total}', Math.ceil(total / batchSize));
                    
                    const batchResponse = await fetch('{{ route('subscribers.verify_batch') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ subscriber_ids: chunk })
                    });

                    if (!batchResponse.ok) {
                        throw new Error('Batch verification failed.');
                    }

                    const result = await batchResponse.json();
                    processed += result.processed;
                    deletedTotal += result.deleted;

                    // Update UI
                    processedCountEl.innerText = processed;
                    deletedCountEl.innerText = deletedTotal;
                    const percent = Math.round((processed / total) * 100);
                    progressBar.style.width = `${percent}%`;
                    progressBar.innerText = `${percent}%`;
                    progressBar.setAttribute('aria-valuenow', percent);
                }

                statusText.className = "text-center mb-3 text-success fw-bold";
                var completeText = "{{ __('messages.verify_cleanup_complete') ?? 'Cleanup complete! Deleted {count} invalid subscribers.' }}";
                statusText.innerText = completeText.replace('{count}', deletedTotal);
                
                setTimeout(() => {
                    window.location.reload();
                }, 3000);

            } catch (error) {
                console.error(error);
                statusText.className = "text-center mb-3 text-danger fw-bold";
                statusText.innerText = "{{ __('messages.verify_error_occurred') ?? 'An error occurred during verification. Please try again.' }}";
                
                const closeBtn = document.createElement('button');
                closeBtn.type = "button";
                closeBtn.className = "btn btn-secondary btn-sm mt-3 d-block mx-auto";
                closeBtn.innerText = "Close";
                closeBtn.onclick = function() { myModal.hide(); };
                document.querySelector('#verifyProgressModal .modal-body').appendChild(closeBtn);
            }
        }
    </script>

</x-layout>
