<x-layout>
    @php
    $groupedPermissions = [
        'Service Body Agendas' => [
            'title_en' => 'Service Body Agendas',
            'title_ar' => 'جداول أعمال هيئة الخدمة',
            'permissions' => []
        ],
        'Store & Inventory' => [
            'title_en' => 'Store & Inventory',
            'title_ar' => 'المخزن والمخزون',
            'permissions' => []
        ],
        'General Calendar' => [
            'title_en' => 'General Calendar',
            'title_ar' => 'التقويم العام',
            'permissions' => []
        ],
        'General & Others' => [
            'title_en' => 'General & Others',
            'title_ar' => 'صلاحيات عامة وأخرى',
            'permissions' => []
        ],
    ];

    foreach ($permissions as $permission) {
        if (in_array($permission->name, ['create sb agenda', 'edit sb agenda', 'approve sb agenda', 'delete sb agenda'])) {
            $groupedPermissions['Service Body Agendas']['permissions'][] = $permission;
        } elseif (in_array($permission->name, ['manage store', 'view lit inventory'])) {
            $groupedPermissions['Store & Inventory']['permissions'][] = $permission;
        } elseif ($permission->name === 'can_manage_calendar') {
            $groupedPermissions['General Calendar']['permissions'][] = $permission;
        } else {
            $groupedPermissions['General & Others']['permissions'][] = $permission;
        }
    }
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
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
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
                        <label class="form-label fw-bold text-primary fs-5 mb-3">{{ __('messages.Direct Permissions') ?? 'Direct Permissions' }}</label>
                        
                        @foreach ($groupedPermissions as $catName => $category)
                            @if (count($category['permissions']) > 0)
                                <div class="accordion-premium" id="acc_{{ Str::slug($catName) }}">
                                    <div class="accordion-premium-header" onclick="toggleAccordion(this)">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="chevron-icon"><i class="bi bi-chevron-down"></i></span>
                                            <span class="fs-6 fw-bold">{{ app()->getLocale() === 'ar' ? $category['title_ar'] : $category['title_en'] }}</span>
                                        </div>
                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                                            <input class="form-check-input select-all-category" type="checkbox" id="select_all_{{ Str::slug($catName) }}">
                                            <label class="form-check-label small text-muted" for="select_all_{{ Str::slug($catName) }}">
                                                {{ __('messages.Select All') ?? 'Select All' }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="accordion-premium-content">
                                        <div class="row g-3">
                                            @foreach ($category['permissions'] as $permission)
                                                <div class="col-md-6">
                                                    <div class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between shadow-sm">
                                                        <span class="text-secondary fw-medium">{{ $permission->name }}</span>
                                                        <label class="form-switch-premium m-0">
                                                            <input type="checkbox" class="permission-checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ $user->permissions->contains($permission->id) ? 'checked' : '' }}>
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
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-premium-primary">{{ __('messages.Save') ?? 'Save' }}</button>
                        <a href="{{ route('users.index') }}" class="btn btn-premium-secondary d-flex align-items-center justify-content-center">{{ __('messages.Cancel') ?? 'Cancel' }}</a>
                    </div>
                </form>
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
    </script>
</x-layout>