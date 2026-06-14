<x-layout>

    <x-backhead>{{ __('messages.Form Builder') ?? 'Form Builder' }}</x-backhead>

    <div class="container-fluid px-4">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold" style="color: var(--text-primary);">{{ __('messages.Forms List') ?? 'Forms Dashboard' }}</h4>
            <a href="{{ route('forms.create') }}" class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> {{ __('messages.Create Form') ?? 'Create Form' }}
            </a>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive" style="overflow: visible !important;">
                <table class="table neo-table align-middle text-center display" id="forms-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>{{ __('messages.Title') ?? 'Title' }}</th>
                            <th>{{ __('messages.Type') ?? 'Type' }}</th>
                            <th>{{ __('messages.Status') ?? 'Status' }}</th>
                            <th>{{ __('messages.Last Submission') ?? 'Last Submission' }}</th>
                            <th>{{ __('messages.Views') ?? 'Views' }}</th>
                            <th>{{ __('messages.Submissions') ?? 'Submissions' }}</th>
                            <th>{{ __('messages.Conversion') ?? 'Conversion' }}</th>
                            <th>{{ __('messages.Actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($forms as $form)
                            @php
                                $lastSubmission = $form->submissions()->latest()->first();
                                $conversion = $form->conversion_rate;
                                $statusBadge = $form->status === 'published' ? 'bg-success' : ($form->status === 'draft' ? 'bg-warning text-dark' : 'bg-secondary');
                            @endphp
                            <tr>
                                <td class="fw-bold" style="color: var(--text-primary);">{{ $form->title }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        {{ $form->type === 'survey' ? __('messages.Survey') ?? 'Survey' : __('messages.Event Registration') ?? 'Event Entry' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <form action="{{ route('forms.toggleStatus', $form->id) }}" method="POST" id="status-toggle-{{ $form->id }}" class="m-0">
                                            @csrf
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch" id="switch-{{ $form->id }}" onchange="document.getElementById('status-toggle-{{ $form->id }}').submit()" {{ $form->status === 'published' ? 'checked' : '' }} style="cursor: pointer;">
                                            </div>
                                        </form>
                                        <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1 small">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-secondary">
                                    {{ $lastSubmission ? $lastSubmission->created_at->format('Y-m-d H:i') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark rounded-pill px-2.5">{{ $form->views }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill px-2.5">{{ $form->submissions->count() }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="progress" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $conversion }}%" aria-valuenow="{{ $conversion }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="fw-bold text-success">{{ $conversion }}%</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ route('forms.report', $form->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-1">
                                            <i class="bi bi-graph-up"></i> {{ __('messages.Report') ?? 'Report' }}
                                        </a>
                                        <a href="{{ route('forms.edit', $form->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 d-flex align-items-center gap-1">
                                            <i class="bi bi-pencil"></i> {{ __('messages.Edit') ?? 'Edit' }}
                                        </a>
                                        
                                        <!-- Gear Dropdown for Admin Features -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-circle p-1 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false" style="width: 32px; height: 32px;">
                                                <i class="bi bi-gear-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                <li>
                                                    <!-- Preview link triggers modal or new tab -->
                                                    <a href="{{ route('forms.show.public', $form->slug) }}" target="_blank" class="dropdown-item d-flex align-items-center gap-2">
                                                        <i class="bi bi-eye text-primary"></i> {{ __('messages.Preview') ?? 'Preview' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2" onclick="copyFormUrl('{{ route('forms.show.public', $form->slug) }}')">
                                                        <i class="bi bi-link-45deg text-success"></i> {{ __('messages.Copy URL') ?? 'Copy URL' }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('forms.toggleStatus', $form->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="bi bi-toggle-on text-warning"></i> 
                                                            {{ $form->status === 'published' ? (__('messages.Unpublish') ?? 'Unpublish') : (__('messages.Publish') ?? 'Publish') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="{{ route('forms.report', $form->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                        <i class="bi bi-database text-info"></i> {{ __('messages.View Submissions') ?? 'View Submissions' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('forms.duplicate', $form->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="bi bi-files text-secondary"></i> {{ __('messages.Duplicate') ?? 'Duplicate' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('forms.reset', $form->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reset all submissions and views? This action cannot be undone.');">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                            <i class="bi bi-trash-fill"></i> {{ __('messages.Reset Submissions') ?? 'Reset Submissions' }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('forms.destroy', $form->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this form entirely?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                            <i class="bi bi-x-circle-fill"></i> {{ __('messages.Delete') ?? 'Delete' }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alert toast for URL copy confirmation -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <div id="copy-toast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> URL copied to clipboard!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        function copyFormUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                const toastEl = document.getElementById('copy-toast');
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
            }).catch(err => {
                alert('Failed to copy URL: ' + err);
            });
        }
    </script>

</x-layout>
