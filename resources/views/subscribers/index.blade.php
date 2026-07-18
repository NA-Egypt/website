<x-layout>

    <x-backhead>{{ __('messages.Manage Subscribers') ?? 'Manage Subscribers' }}</x-backhead>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success shadow-sm d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @php
        $columns = [
            ['field' => 'email', 'title' => __('messages.Email') ?? 'Email', 'sort' => true],
            ['field' => 'status', 'title' => __('messages.Status') ?? 'Status', 'sort' => true],
            ['field' => 'created_at_formatted', 'title' => __('messages.Created At') ?? 'Created At', 'sort' => true],
            ['field' => 'actions', 'title' => __('messages.Control') ?? 'Control', 'sort' => false]
        ];
        $bulkActions = [
            ['value' => 'delete', 'label' => __('messages.Delete Selected') ?? 'Delete Selected'],
            ['value' => 'verify', 'label' => __('messages.Verify Selected') ?? 'Verify Selected'],
            ['value' => 'unverify', 'label' => __('messages.Unverify Selected') ?? 'Unverify Selected']
        ];
        @endphp

        <div class="mb-3 d-flex justify-content-end align-items-center flex-wrap gap-2">
            <a href="{{ route('subscribers.export') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                {{ __('messages.Export CSV') ?? 'Export CSV' }}
            </a>
            <button type="button" class="btn btn-outline-warning" onclick="startBulkVerification()">
                <i class="bi bi-shield-check me-1"></i>
                {{ __('messages.verify_unverified_cleanup') ?? 'Verify Unverified Mailboxes & Cleanup' }}
            </button>
        </div>

        <div data-vue-app="GenericDataTable"
             data-fetch-url="{{ route('subscribers.index') }}"
             data-columns="{{ json_encode($columns) }}"
             data-create-route="{{ route('subscribers.create') }}"
             data-create-label="{{ __('messages.Add Subscriber') ?? 'Add Subscriber' }}"
             data-bulk-action-route="{{ route('subscribers.bulk_action') }}"
             data-bulk-actions="{{ json_encode($bulkActions) }}"
             data-bulk-ids-name="subscriber_ids[]"
             data-has-toggle-verification-button
             data-delete-route-name="subscribers.destroy_admin"
             data-delete-route-template="{{ str_replace('1', '{id}', route('subscribers.destroy_admin', ['subscriber' => 1])) }}">
        </div>
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
