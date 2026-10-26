<x-layout>

    <x-backhead>{{ __('messages.Form Builder') ?? 'Form Builder' }}</x-backhead>

    <div class="container-fluid px-4">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="mb-1 fw-bold" style="color: var(--text-primary);">{{ __('messages.Forms List') ?? 'Forms Dashboard' }}</h4>
            </div>
            <a href="{{ route('forms.create') }}" class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> {{ __('messages.Create Form') ?? 'Create Form' }}
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('forms.index', ['tab' => 'active']) }}" 
               class="btn rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 {{ ($filter ?? 'active') === 'active' ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}" style="font-size: 0.88rem;">
                <i class="bi bi-collection-play"></i>
                <span>{{ __('messages.Active Forms') }}</span>
                <span class="badge rounded-pill {{ ($filter ?? 'active') === 'active' ? 'bg-white text-primary' : 'bg-secondary' }}">{{ $activeCount ?? 0 }}</span>
            </a>
            <a href="{{ route('forms.index', ['tab' => 'archived']) }}" 
               class="btn rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 {{ ($filter ?? '') === 'archived' ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}" style="font-size: 0.88rem;">
                <i class="bi bi-archive"></i>
                <span>{{ __('messages.Archived Forms') }}</span>
                <span class="badge rounded-pill {{ ($filter ?? '') === 'archived' ? 'bg-white text-primary' : 'bg-secondary' }}">{{ $archivedCount ?? 0 }}</span>
            </a>
            <a href="{{ route('forms.index', ['tab' => 'all']) }}" 
               class="btn rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 {{ ($filter ?? '') === 'all' ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}" style="font-size: 0.88rem;">
                <i class="bi bi-layers"></i>
                <span>{{ __('messages.All Forms') }}</span>
                <span class="badge rounded-pill {{ ($filter ?? '') === 'all' ? 'bg-white text-primary' : 'bg-secondary' }}">{{ $totalCount ?? 0 }}</span>
            </a>
        </div>

        <div class="glass-card p-3 p-md-4">
            <div class="table-responsive">
                <table class="table neo-table align-middle text-center display" id="forms-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>{{ __('messages.Title') ?? 'Title' }}</th>
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
                                $isArchived = $form->status === 'archived';
                                $statusBadge = $isArchived 
                                    ? 'bg-secondary text-white' 
                                    : ($form->status === 'published' ? 'bg-success' : ($form->status === 'draft' ? 'bg-warning text-dark' : 'bg-secondary'));
                            @endphp
                            <tr class="{{ $isArchived ? 'opacity-75' : '' }}">
                                <td class="fw-bold" style="color: var(--text-primary);" title="{{ $form->title }}">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span>{{ \Illuminate\Support\Str::limit($form->title, 35) }}</span>
                                        @if ($form->type === 'survey')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">{{ __('messages.Survey') ?? 'Survey' }}</span>
                                        @elseif ($form->type === 'service_position_application')
                                            <span class="badge rounded-pill px-2 py-0.5" style="font-size: 0.7rem; background-color: rgba(99, 102, 241, 0.12); color: #4f46e5; border: 1px solid rgba(99, 102, 241, 0.25);">{{ __('messages.Service Position Application') ?? 'Service Position Application' }}</span>
                                        @elseif ($form->type === 'suggestions')
                                            <span class="badge rounded-pill px-2 py-0.5" style="font-size: 0.7rem; background-color: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25);">{{ __('messages.Suggestions') ?? 'Suggestions' }}</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">{{ __('messages.Event Registration') ?? 'Event Registration' }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if (!$isArchived)
                                            <form action="{{ route('forms.toggleStatus', $form->id) }}" method="POST" id="status-toggle-{{ $form->id }}" class="m-0">
                                                @csrf
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="switch-{{ $form->id }}" onchange="document.getElementById('status-toggle-{{ $form->id }}').submit()" {{ $form->status === 'published' ? 'checked' : '' }} style="cursor: pointer;">
                                                </div>
                                            </form>
                                        @endif
                                        <span class="badge {{ $statusBadge }} rounded-pill px-2.5 py-1 small d-inline-flex align-items-center gap-1">
                                            @if ($isArchived)
                                                <i class="bi bi-archive-fill"></i> {{ __('messages.Archived') ?? 'Archived' }}
                                            @else
                                                {{ ucfirst($form->status) }}
                                            @endif
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
                                                @if (!$isArchived)
                                                    <li>
                                                        <form action="{{ route('forms.toggleStatus', $form->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="bi bi-toggle-on text-warning"></i> 
                                                                {{ $form->status === 'published' ? (__('messages.Unpublish') ?? 'Unpublish') : (__('messages.Publish') ?? 'Publish') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
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
                                                @if ($isArchived)
                                                    <li>
                                                        <form action="{{ route('forms.unarchive', $form->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-success">
                                                                <i class="bi bi-arrow-counterclockwise text-success"></i> {{ __('messages.Restore Form') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('forms.archive', $form->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.are_you_sure') ?? 'Are you sure you want to archive this form?' }}');">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-warning">
                                                                <i class="bi bi-archive text-warning"></i> {{ __('messages.Archive Form') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
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
