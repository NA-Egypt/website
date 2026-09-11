<x-layout>
    <x-backhead>{{__('messages.Assign Permissions to')}} <span style="color: #3461ff">{{ $role->name }}</span></x-backhead>

    @php
        $categories = \App\Models\Permission::getGrouped($permissions);
    @endphp

    <div class="container py-3 d-flex justify-content-center align-items-center">
        <form action="{{ route('roles.update-permissions', $role->id) }}" method="POST" class="row g-3 col-md-12 col-lg-10">
            @csrf

            <div class="card border-0 shadow-lg p-4" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{__('messages.Permissions')}}</h5>
                    <div style="min-width: 250px;">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border: 1px solid var(--glass-border); color: var(--text-secondary);"><i class="bi bi-search"></i></span>
                            <input type="text" id="permissionSearch" class="form-control bg-transparent border-start-0" placeholder="{{ __('messages.Search permissions...') ?? 'Search permissions...' }}" style="border: 1px solid var(--glass-border); color: var(--text-primary); border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($categories as $key => $cat)
                        @if(count($cat['permissions']) > 0)
                            <div class="col-md-6 category-section" id="cat_{{ $key }}">
                                <div class="card h-100 border-0" style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border) !important; border-radius: 12px;">
                                    <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center py-3 px-3">
                                        <span class="fw-bold d-flex align-items-center gap-2" style="color: var(--text-primary);">
                                            <i class="bi {{ $cat['icon'] }} text-primary"></i>
                                            {{ $cat['title'] }}
                                            <span class="badge bg-light text-muted border rounded-pill small px-2">
                                                {{ count($cat['permissions']) }}
                                            </span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-link select-all-btn text-decoration-none p-0 text-primary fw-semibold" data-target="cat_{{ $key }}" data-selected="false">
                                            {{ __('messages.Select All') ?? 'Select All' }}
                                        </button>
                                    </div>
                                    <div class="card-body px-3 py-1">
                                        @foreach($cat['permissions'] as $permission)
                                            <div class="permission-card p-3 rounded-3 d-flex align-items-start justify-content-between mb-3 gap-3" style="background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); transition: all 0.2s;" data-search="{{ strtolower($permission->display_name . ' ' . $permission->name . ' ' . $permission->description) }}">
                                                <div class="d-flex flex-column gap-1 pe-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bi bi-shield-check text-primary fs-6"></i>
                                                        <span style="color: var(--text-primary); font-weight: 600; font-size: 0.95rem;">{{ $permission->display_name }}</span>
                                                    </div>
                                                    @if(!empty($permission->description))
                                                        <span class="small lh-sm" style="color: var(--text-secondary); margin-inline-start: 1.5rem;">{{ $permission->description }}</span>
                                                    @endif
                                                    <div style="margin-inline-start: 1.5rem;" class="mt-1">
                                                        <span class="badge bg-light text-secondary border font-monospace py-1 px-2" style="font-size: 0.75rem;">
                                                            <i class="bi bi-key-fill me-1 opacity-75"></i>{{ $permission->name }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-check form-switch mb-0 flex-shrink-0 mt-1">
                                                    <input class="form-check-input perm-checkbox" type="checkbox" role="switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ $role->permissions->contains($permission->id) ? 'checked' : '' }} autocomplete="off">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div id="noSearchMatches" class="text-center py-4 text-muted d-none">
                    <i class="bi bi-search fs-3 d-block mb-2"></i>
                    <span>{{ __('messages.No permissions match your search') ?? 'No permissions match your search.' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <x-forms.normal-button color='outline-primary' name='{{__("messages.Update")}}'/>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Search filtering
            const searchInput = document.getElementById('permissionSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    let totalVisible = 0;

                    document.querySelectorAll('.permission-card').forEach(function(card) {
                        const searchText = card.getAttribute('data-search') || '';
                        if (query === '' || searchText.includes(query)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Hide empty categories and track overall count
                    document.querySelectorAll('.category-section').forEach(function(section) {
                        const visibleCards = section.querySelectorAll('.permission-card[style*="display: flex"]');
                        // In case style was reset to display: flex
                        const isVisible = Array.from(section.querySelectorAll('.permission-card')).some(card => card.style.display !== 'none');
                        if (!isVisible) {
                            section.style.display = 'none';
                        } else {
                            section.style.display = 'block';
                            totalVisible++;
                        }
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

            // Select all / Deselect all handler
            document.querySelectorAll('.select-all-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const isSelected = this.getAttribute('data-selected') === 'true';
                    const targetContainer = document.getElementById(targetId);
                    
                    targetContainer.querySelectorAll('.perm-checkbox').forEach(function(checkbox) {
                        checkbox.checked = !isSelected;
                    });
                    
                    this.setAttribute('data-selected', !isSelected ? 'true' : 'false');
                    this.textContent = !isSelected ? '{{ __("messages.Deselect All") ?? "Deselect All" }}' : '{{ __("messages.Select All") ?? "Select All" }}';
                });
            });
        });
    </script>
</x-layout>
