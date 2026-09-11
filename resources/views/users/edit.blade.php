<x-layout>
    @php
    $groupedPermissions = \App\Models\Permission::getGrouped($permissions);
    @endphp

    <x-backhead>{{ __('messages.Edit User') ?? 'Edit User' }}: {{ $user->name }}</x-backhead>

    <div class="container mt-4">
        <div class="glass-card glass-card-compact shadow-lg border-0">
            <div class="glass-card-header">
                <h4 class="m-0 fw-bold text-primary">{{ __('messages.User Information') ?? 'User Information' }}</h4>
            </div>
            
            <div class="glass-card-body">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="display_name" class="form-label fw-bold text-uppercase fs-7 text-muted mb-2">{{ __('messages.Display Name') ?? 'Display Name' }}</label>
                            <input type="text" name="display_name" id="display_name" class="form-control glow-input @error('display_name') is-invalid @enderror" value="{{ old('display_name', $user->display_name) }}" required>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold text-uppercase fs-7 text-muted mb-2">{{ __('messages.Email') ?? 'Email' }}</label>
                            <input type="email" name="email" id="email" class="form-control glow-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="service_body_id" class="form-label fw-bold text-uppercase fs-7 text-muted mb-2">{{ __('messages.Service Body') ?? 'Service Body' }}</label>
                        <select name="service_body_id" id="service_body_id" class="form-select glow-input @error('service_body_id') is-invalid @enderror">
                            <option value="">-- {{ __('messages.None') ?? 'None' }} --</option>
                            @foreach ($serviceBodies as $sb)
                                <option value="{{ $sb->id }}" {{ old('service_body_id', $user->service_body_id) == $sb->id ? 'selected' : '' }}>
                                    {{ $sb->en_name }} ({{ $sb->ar_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('service_body_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4 opacity-25">

                    <!-- Roles section -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary fs-5 mb-3">{{ __('messages.Roles') ?? 'Roles' }}</label>
                        <div class="row g-3">
                            @foreach ($roles as $role)
                                <div class="col-md-4 col-sm-6">
                                    <div class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between shadow-sm">
                                        <span class="fw-semibold text-secondary">{{ $role->name }}</span>
                                        <label class="form-switch-premium m-0">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                            <span class="slider-premium"></span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <!-- Direct Permissions section -->
                    <div class="mb-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                            <label class="form-label fw-bold text-primary fs-5 mb-0">{{ __('messages.Direct Permissions') ?? 'Direct Permissions' }}</label>
                            <div style="min-width: 260px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                    <input type="text" id="userPermissionSearch" class="form-control bg-white border-start-0" placeholder="{{ __('messages.Search permissions...') ?? 'Search permissions...' }}">
                                </div>
                            </div>
                        </div>
                        
                        @foreach ($groupedPermissions as $catKey => $category)
                            @if (count($category['permissions']) > 0)
                                <div class="accordion-premium user-perm-category" id="acc_{{ Str::slug($catKey) }}">
                                    <div class="accordion-premium-header" onclick="toggleAccordion(this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="chevron-icon"><i class="bi bi-chevron-down"></i></span>
                                            <span class="fs-6 fw-bold d-flex align-items-center gap-2">
                                                <i class="bi {{ $category['icon'] }} text-primary"></i>
                                                {{ $category['title'] }}
                                            </span>
                                            <span class="badge bg-light text-muted border rounded-pill small px-2 category-counter">
                                                {{ count($category['permissions']) }}
                                            </span>
                                        </div>
                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                                            <input class="form-check-input select-all-category" type="checkbox" id="select_all_{{ Str::slug($catKey) }}">
                                            <label class="form-check-label small text-muted" for="select_all_{{ Str::slug($catKey) }}">
                                                {{ __('messages.Select All') ?? 'Select All' }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="accordion-premium-content">
                                        <div class="row g-3">
                                            @foreach ($category['permissions'] as $permission)
                                                <div class="col-md-6 user-perm-item" data-search="{{ strtolower($permission->display_name . ' ' . $permission->name . ' ' . $permission->description) }}">
                                                    <div class="p-3 border rounded-3 bg-white d-flex align-items-start justify-content-between shadow-sm h-100 gap-3">
                                                        <div class="d-flex flex-column gap-1 pe-2">
                                                            <span class="text-dark fw-semibold fs-6">{{ $permission->display_name }}</span>
                                                            @if(!empty($permission->description))
                                                                <span class="text-muted small lh-sm">{{ $permission->description }}</span>
                                                            @endif
                                                            <div class="mt-1">
                                                                <span class="badge bg-light text-secondary border font-monospace py-1 px-2" style="font-size: 0.75rem;">
                                                                    <i class="bi bi-key-fill me-1 opacity-75"></i>{{ $permission->name }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <label class="form-switch-premium m-0 flex-shrink-0 mt-1">
                                                            <input type="checkbox" class="permission-checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission_{{ $permission->id }}" {{ $user->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                            <span class="slider-premium"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div id="noSearchMatches" class="text-center py-4 text-muted d-none">
                            <i class="bi bi-search fs-3 d-block mb-2"></i>
                            <span>{{ __('messages.No permissions match your search') ?? 'No permissions match your search.' }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <button type="submit" class="btn btn-premium-primary">{{ __('messages.Save') ?? 'Save' }}</button>
                        <a href="{{ route('users.index') }}" class="btn btn-premium-secondary d-flex align-items-center justify-content-center">{{ __('messages.Cancel') ?? 'Cancel' }}</a>
                        @if(auth()->user()->hasRole('super admin') && !$user->hasRole('super admin'))
                            <button type="button" class="btn btn-warning text-dark fw-bold px-3 ms-auto d-flex align-items-center gap-2 rounded-pill shadow-sm" onclick="if(confirm('{{ __('messages.impersonation_started', ['name' => $user->display_name ?? $user->name]) }}')) { document.getElementById('impersonate-form-{{ $user->id }}').submit(); }">
                                <i class="bi bi-incognito"></i>
                                <span>{{ __('messages.impersonate_user') }}</span>
                            </button>
                        @endif
                    </div>
                </form>
                @if(auth()->user()->hasRole('super admin') && !$user->hasRole('super admin'))
                    <form id="impersonate-form-{{ $user->id }}" action="{{ route('users.impersonate', $user) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleAccordion(header) {
            const accordion = header.closest('.accordion-premium');
            accordion.classList.toggle('open');
        }

        document.querySelectorAll('.select-all-category').forEach(selectAllCheckbox => {
            selectAllCheckbox.addEventListener('change', function(e) {
                const accordion = this.closest('.accordion-premium');
                const checkboxes = accordion.querySelectorAll('.permission-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        });

        function updateSelectAllStates() {
            document.querySelectorAll('.accordion-premium').forEach(accordion => {
                const selectAll = accordion.querySelector('.select-all-category');
                if (!selectAll) return;
                const checkboxes = accordion.querySelectorAll('.permission-checkbox');
                if (checkboxes.length === 0) return;
                const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                selectAll.checked = checkedCount === checkboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            });
        }

        document.querySelectorAll('.permission-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectAllStates);
        });

        document.addEventListener('DOMContentLoaded', updateSelectAllStates);

        // Live Search Handler
        const userPermSearch = document.getElementById('userPermissionSearch');
        if (userPermSearch) {
            userPermSearch.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                let totalVisible = 0;

                document.querySelectorAll('.user-perm-category').forEach(category => {
                    const items = category.querySelectorAll('.user-perm-item');
                    let categoryVisible = 0;

                    items.forEach(item => {
                        const searchText = item.getAttribute('data-search') || '';
                        if (query === '' || searchText.includes(query)) {
                            item.style.display = '';
                            categoryVisible++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (query !== '') {
                        if (categoryVisible > 0) {
                            category.style.display = '';
                            category.classList.add('open');
                        } else {
                            category.style.display = 'none';
                        }
                    } else {
                        category.style.display = '';
                        category.classList.remove('open');
                    }

                    totalVisible += categoryVisible;
                });

                const noMatches = document.getElementById('noSearchMatches');
                if (noMatches) {
                    if (query !== '' && totalVisible === 0) {
                        noMatches.classList.remove('d-none');
                    } else {
                        noMatches.classList.add('d-none');
                    }
                }
            });
        }
    </script>
</x-layout>