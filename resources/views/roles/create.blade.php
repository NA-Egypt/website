<x-layout>
    <x-backhead>{{__('messages.Create New Roles')}}</x-backhead>

    @php
        $categories = [
            'agenda' => [
                'title' => __('messages.Service Body Agendas') ?? 'Service Body Agendas',
                'icon' => 'bi-journals',
                'permissions' => []
            ],
            'store' => [
                'title' => app()->getLocale() === 'ar' ? 'المخزن والمطبوعات' : 'Store & Literature',
                'icon' => 'bi-box-seam',
                'permissions' => []
            ],
            'calendar' => [
                'title' => __('messages.Calendar') ?? 'Calendar',
                'icon' => 'bi-calendar-check',
                'permissions' => []
            ],
            'general' => [
                'title' => __('messages.General & Others') ?? 'General & Others',
                'icon' => 'bi-gear-fill',
                'permissions' => []
            ]
        ];

        foreach ($permissions as $permission) {
            $name = strtolower($permission->name);
            if (str_contains($name, 'agenda') || str_contains($name, 'sb')) {
                $categories['agenda']['permissions'][] = $permission;
            } elseif (str_contains($name, 'store') || str_contains($name, 'lit')) {
                $categories['store']['permissions'][] = $permission;
            } elseif (str_contains($name, 'calendar') || str_contains($name, 'date') || str_contains($name, 'time')) {
                $categories['calendar']['permissions'][] = $permission;
            } else {
                $categories['general']['permissions'][] = $permission;
            }
        }
    @endphp

    <div class="container py-3 d-flex justify-content-center align-items-center">
        <form action="{{ route('roles.store') }}" method="post" class="row g-3 col-md-12 col-lg-10">
            @csrf

            <div class="card border-0 shadow-lg p-4 mb-3" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <x-forms.input name="name" label="{{__('messages.Role Name')}}" id="name"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-forms.input name="description" label="{{__('messages.Role Description')}}" id="description"/>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-lg p-4" style="background: var(--glass-bg); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid var(--glass-border); border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">{{__('messages.Permissions')}}</h5>
                    <div style="min-width: 250px;">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0" style="border: 1px solid var(--glass-border); color: var(--text-secondary);"><i class="bi bi-search"></i></span>
                            <input type="text" id="permissionSearch" class="form-control bg-transparent border-start-0" placeholder="{{ __('messages.Search Permissions...') ?? 'Search...' }}" style="border: 1px solid var(--glass-border); color: var(--text-primary); border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
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
                                        </span>
                                        <button type="button" class="btn btn-sm btn-link select-all-btn text-decoration-none p-0 text-primary fw-semibold" data-target="cat_{{ $key }}" data-selected="false">
                                            {{ __('messages.Select All') ?? 'Select All' }}
                                        </button>
                                    </div>
                                    <div class="card-body px-3 py-1">
                                        @foreach($cat['permissions'] as $permission)
                                            <div class="permission-card p-3 rounded-3 d-flex align-items-center justify-content-between mb-2" style="background: rgba(255,255,255,0.01); border: 1px solid var(--glass-border); transition: all 0.2s;" data-name="{{ strtolower($permission->name) }}">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-shield-check text-primary"></i>
                                                    <span style="color: var(--text-primary); font-weight: 500;">{{ $permission->name }}</span>
                                                </div>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input perm-checkbox" type="checkbox" role="switch" id="perm_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                <x-forms.normal-button color='outline-primary' name='{{__("messages.Save")}}'/>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Search filtering
            const searchInput = document.getElementById('permissionSearch');
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                document.querySelectorAll('.permission-card').forEach(function(card) {
                    const name = card.getAttribute('data-name');
                    if (name.includes(query)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Hide empty categories
                document.querySelectorAll('.category-section').forEach(function(section) {
                    const visibleCards = section.querySelectorAll('.permission-card[style="display: flex;"], .permission-card:not([style*="display: none"])');
                    if (visibleCards.length === 0) {
                        section.style.display = 'none';
                    } else {
                        section.style.display = 'block';
                    }
                });
            });

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