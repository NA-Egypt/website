<x-layout>

    <x-backhead>{{ __('messages.Add Workgroup') }}</x-backhead>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <div class="glass-card shadow-sm border-0 rounded-4 overflow-hidden">
                    <!-- Form Header Banner -->
                    <div class="p-4 bg-primary bg-gradient text-white d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="bi bi-diagram-3-fill fs-3 text-white"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold text-white">{{ __('messages.Add Workgroup') }}</h4>
                                <p class="mb-0 text-white-50 small">{{ __('messages.Hierarchy & Classification') }} &bull; {{ __('messages.Workgroups') }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white bg-opacity-25 px-3 py-2 text-white fw-semibold rounded-pill" id="liveTypeBadge">
                                <i class="bi bi-tag me-1"></i> {{ __('messages.Permanent') }}
                            </span>
                            <span class="badge bg-success bg-opacity-75 px-3 py-2 text-white fw-semibold rounded-pill" id="liveStatusBadge">
                                <i class="bi bi-check-circle me-1"></i> {{ __('messages.Active') }}
                            </span>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <div class="bg-light border-bottom px-4 pt-3">
                        <ul class="nav nav-pills nav-fill gap-2" id="workgroupTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-2" id="tab-identity-btn" data-bs-toggle="pill" data-bs-target="#tab-identity" type="button" role="tab">
                                    <i class="bi bi-diagram-3"></i>
                                    <span>{{ __('messages.Identity & Hierarchy') }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-2" id="tab-officer-btn" data-bs-toggle="pill" data-bs-target="#tab-officer" type="button" role="tab">
                                    <i class="bi bi-person-badge"></i>
                                    <span>{{ __('messages.Leadership & Officer') }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-2" id="tab-meetings-btn" data-bs-toggle="pill" data-bs-target="#tab-meetings" type="button" role="tab">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ __('messages.Meetings & Location') }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-semibold py-2 px-3 d-flex align-items-center justify-content-center gap-2" id="tab-media-btn" data-bs-toggle="pill" data-bs-target="#tab-media" type="button" role="tab">
                                    <i class="bi bi-gear"></i>
                                    <span>{{ __('messages.Media & Settings') }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Form Content -->
                    <form action="{{ route('workgroup.store') }}" method="post" enctype="multipart/form-data" id="workgroupForm" class="p-4 p-md-5">
                        @csrf

                        <div class="tab-content" id="workgroupTabsContent">
                            <!-- TAB 1: Identity & Hierarchy -->
                            <div class="tab-pane fade show active" id="tab-identity" role="tabpanel">
                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-info-circle-fill"></i>
                                        {{ __('messages.General Information') }}
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Arabic Service Committee Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="ar_name" class="form-control form-control-lg @error('ar_name') is-invalid @enderror" value="{{ old('ar_name') }}" required placeholder="{{ __('messages.Arabic Service Committee Name') }}">
                                            @error('ar_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.English Service Committee Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="en_name" class="form-control form-control-lg @error('en_name') is-invalid @enderror" value="{{ old('en_name') }}" required placeholder="{{ __('messages.English Service Committee Name') }}">
                                            @error('en_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 text-muted opacity-25">

                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-diagram-3-fill"></i>
                                        {{ __('messages.Hierarchy & Classification') }}
                                    </h5>
                                    @php
                                        $selectedParentId = $preselectedParentId ?? old('parent_id', request('parent_id', (isset($myCommittee) && $myCommittee ? $myCommittee->id : null)));
                                    @endphp
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Parent Committee') }} <span class="text-danger">*</span></label>
                                            @if($isSuperAdmin)
                                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror" required>
                                                    <option value="">{{ __('messages.Select Parent Committee') }}</option>
                                                    @foreach($committees as $committee)
                                                        <option value="{{ $committee->id }}" {{ (string)$selectedParentId === (string)$committee->id ? 'selected' : '' }}>
                                                            {{ app()->getLocale() === 'ar' ? ($committee->ar_name ?: $committee->en_name) : ($committee->en_name ?: $committee->ar_name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('parent_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @else
                                                @php
                                                    $parentCommittee = $committees->firstWhere('id', $selectedParentId);
                                                @endphp
                                                <input type="text" class="form-control bg-light" readonly value="{{ $parentCommittee ? (app()->getLocale() === 'ar' ? ($parentCommittee->ar_name ?: $parentCommittee->en_name) : ($parentCommittee->en_name ?: $parentCommittee->ar_name)) : '' }}">
                                                <input type="hidden" name="parent_id" value="{{ $selectedParentId }}">
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Workgroup Type') }} <span class="text-danger">*</span></label>
                                            <select name="workgroup_type" id="workgroup_type" class="form-select @error('workgroup_type') is-invalid @enderror" required>
                                                <option value="permanent" {{ old('workgroup_type', 'permanent') === 'permanent' ? 'selected' : '' }}>{{ __('messages.Permanent') }}</option>
                                                <option value="temporary" {{ old('workgroup_type') === 'temporary' ? 'selected' : '' }}>{{ __('messages.Temporary') }}</option>
                                            </select>
                                            @error('workgroup_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('messages.Workgroup Status') }} <span class="text-danger">*</span></label>
                                            <select name="status" id="workgroup_status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                                                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.Completed') }}</option>
                                                <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>{{ __('messages.Archived') }}</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('messages.Target Start Date') }}</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4" id="endDateContainer">
                                            <label class="form-label fw-bold">
                                                {{ __('messages.Target End Date') }}
                                                <span class="text-danger d-none" id="endDateRequiredMark">*</span>
                                            </label>
                                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                            <div class="form-text text-muted small" id="endDateHint">
                                                {{ __('messages.Permanent workgroups do not have an end date.') }}
                                            </div>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: Leadership & Officer -->
                            <div class="tab-pane fade" id="tab-officer" role="tabpanel">
                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-person-check-fill"></i>
                                        {{ __('messages.Assigned Officer') }}
                                    </h5>
                                    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                                        <label class="form-label fw-bold">{{ __('messages.Select Assigned Officer') }} <span class="text-danger">*</span></label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                            <input type="text" id="officerSearchInput" class="form-control" placeholder="{{ __('messages.Search user by name or email...') }}">
                                        </div>
                                        <select name="email" id="officerSelect" class="form-select @error('email') is-invalid @enderror" size="6" required>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ (string)old('email') === (string)$user->id || (string)old('email') === (string)$user->email ? 'selected' : '' }} data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted mt-2 small">
                                            <i class="bi bi-shield-check text-success me-1"></i>
                                            {{ __('messages.Assigned user will automatically be granted the Workgroups role to manage this entity and submit reports.') }}
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="my-4 text-muted opacity-25">

                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-person-badge-fill"></i>
                                        {{ __('messages.Leadership Contact') }}
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Chairman Name') }}</label>
                                            <input type="text" name="chairman_name" class="form-control" value="{{ old('chairman_name') }}" placeholder="{{ __('messages.Chairman Name') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Chairman Phone') }}</label>
                                            <input type="tel" name="chairman_phone" class="form-control" value="{{ old('chairman_phone') }}" placeholder="{{ __('messages.Chairman Phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: Meetings & Location -->
                            <div class="tab-pane fade" id="tab-meetings" role="tabpanel">
                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar-event-fill"></i>
                                        {{ __('messages.Workgroup Meetings') }} & {{ __('messages.Schedule Description') }}
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">{{ __('messages.Workgroup Meetings') }} ({{ __('messages.Schedule Description') }})</label>
                                            <input type="text" name="notes" id="notes" class="form-control form-control-lg" value="{{ old('notes') }}" placeholder="e.g. Every Monday at 7:00 PM via Zoom / كل اثنين الساعة ٧ مساء">
                                            <div class="form-text text-muted">{{ __('messages.Provide recurring schedule details for fellowship members.') }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">{{ __('messages.Locations') }} ({{ __('messages.Zoom / Google Maps URL') }})</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                                                <input type="url" name="location" id="location" class="form-control" value="{{ old('location') }}" placeholder="https://zoom.us/j/... or https://maps.google.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 text-muted opacity-25">

                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        {{ __('messages.Physical Address Details (Optional)') ?? __('messages.Location Details') }}
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Arabic Address') }}</label>
                                            <input type="text" name="ar_address" id="ar_address" class="form-control" value="{{ old('ar_address') }}" placeholder="{{ __('messages.Arabic Address') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.English Address') }}</label>
                                            <input type="text" name="en_address" id="en_address" class="form-control" value="{{ old('en_address') }}" placeholder="{{ __('messages.English Address') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 4: Media & Settings -->
                            <div class="tab-pane fade" id="tab-media" role="tabpanel">
                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-image-fill"></i>
                                        {{ __('messages.Committee Logo') }}
                                    </h5>
                                    <div class="row align-items-center g-4">
                                        <div class="col-md-8">
                                            <div class="border-2 border-dashed rounded-4 p-4 text-center bg-light bg-opacity-50 position-relative" id="logoDropzone" style="border: 2px dashed #cbd5e1; cursor: pointer;">
                                                <input type="file" name="logo" id="logoInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/png, image/jpeg, image/jpg" style="cursor: pointer;">
                                                <div class="py-2" id="dropzonePrompt">
                                                    <i class="bi bi-cloud-arrow-up text-primary fs-1 mb-2 d-block"></i>
                                                    <h6 class="fw-bold mb-1">{{ __('messages.Upload Logo') }}</h6>
                                                    <p class="text-muted small mb-0">{{ __('messages.Drag and drop or click to upload') }}</p>
                                                    <small class="text-muted d-block mt-2">{{ __('messages.Allowed types: PNG, JPG, JPEG. Max size 2MB.') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="p-3 border rounded-4 bg-white d-inline-block shadow-sm">
                                                <div class="text-muted small mb-2 fw-semibold">{{ __('messages.Preview') ?? 'Preview' }}</div>
                                                <div class="rounded-circle overflow-hidden border d-flex align-items-center justify-content-center bg-light mx-auto" style="width: 110px; height: 110px;">
                                                    <img id="logoPreviewImg" src="{{ asset('assets/images/na.png') }}" alt="Logo Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                </div>
                                                <button type="button" class="btn btn-sm btn-link text-danger mt-2 d-none" id="removeLogoPreviewBtn">
                                                    <i class="bi bi-x-circle me-1"></i> {{ __('messages.Remove Logo') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 text-muted opacity-25">

                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                        <i class="bi bi-file-text-fill"></i>
                                        {{ __('messages.Default Report Footer') }}
                                    </h5>
                                    <textarea name="default_footer" id="default_footer" class="form-control" rows="3" maxlength="1000" placeholder="{{ __('messages.Default Report Footer') }}">{{ old('default_footer') }}</textarea>
                                    <div class="form-text text-muted">{{ __('messages.Appears automatically at the end of PDF reports generated for this workgroup.') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sticky Action Bar -->
                        <div class="d-flex justify-content-between align-items-center gap-3 pt-4 mt-4 border-top">
                            <a href="{{ route('workgroup.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                                <i class="bi bi-x-lg me-1"></i> {{ __('messages.Cancel') }}
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light px-4 rounded-pill d-none" id="prevTabBtn">
                                    <i class="bi bi-arrow-left me-1"></i> {{ __('messages.Previous') ?? 'Previous' }}
                                </button>
                                <button type="button" class="btn btn-outline-primary px-4 rounded-pill" id="nextTabBtn">
                                    {{ __('messages.Next') ?? 'Next' }} <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm fw-bold">
                                    <i class="bi bi-check-lg me-1"></i> {{ __('messages.Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = ['tab-identity', 'tab-officer', 'tab-meetings', 'tab-media'];
            let currentTabIndex = 0;

            const prevBtn = document.getElementById('prevTabBtn');
            const nextBtn = document.getElementById('nextTabBtn');
            const typeSelect = document.getElementById('workgroup_type');
            const statusSelect = document.getElementById('workgroup_status');
            const liveTypeBadge = document.getElementById('liveTypeBadge');
            const liveStatusBadge = document.getElementById('liveStatusBadge');
            const endDateContainer = document.getElementById('endDateContainer');
            const endDateHint = document.getElementById('endDateHint');
            const endDateRequiredMark = document.getElementById('endDateRequiredMark');

            // Tab navigation helper
            function updateNavButtons(index) {
                currentTabIndex = index;
                if (index === 0) {
                    prevBtn.classList.add('d-none');
                } else {
                    prevBtn.classList.remove('d-none');
                }

                if (index === tabs.length - 1) {
                    nextBtn.classList.add('d-none');
                } else {
                    nextBtn.classList.remove('d-none');
                }
            }

            document.querySelectorAll('#workgroupTabs button[data-bs-toggle="pill"]').forEach((tabBtn, idx) => {
                tabBtn.addEventListener('shown.bs.tab', () => updateNavButtons(idx));
            });

            nextBtn.addEventListener('click', function () {
                if (currentTabIndex < tabs.length - 1) {
                    const nextTabTrigger = document.querySelector(`#workgroupTabs button[data-bs-target="#${tabs[currentTabIndex + 1]}"]`);
                    if (nextTabTrigger) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(nextTabTrigger);
                        tabInstance.show();
                    }
                }
            });

            prevBtn.addEventListener('click', function () {
                if (currentTabIndex > 0) {
                    const prevTabTrigger = document.querySelector(`#workgroupTabs button[data-bs-target="#${tabs[currentTabIndex - 1]}"]`);
                    if (prevTabTrigger) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(prevTabTrigger);
                        tabInstance.show();
                    }
                }
            });

            // Workgroup Type changes
            function updateWorkgroupTypeRules() {
                const val = typeSelect.value;
                if (val === 'temporary') {
                    liveTypeBadge.innerHTML = '<i class="bi bi-clock-history me-1"></i> {{ __("messages.Temporary") }}';
                    liveTypeBadge.className = 'badge bg-warning bg-opacity-75 px-3 py-2 text-dark fw-semibold rounded-pill';
                    endDateHint.textContent = '{{ __("messages.Temporary workgroups require target start and end dates.") }}';
                    endDateRequiredMark.classList.remove('d-none');
                } else {
                    liveTypeBadge.innerHTML = '<i class="bi bi-tag me-1"></i> {{ __("messages.Permanent") }}';
                    liveTypeBadge.className = 'badge bg-white bg-opacity-25 px-3 py-2 text-white fw-semibold rounded-pill';
                    endDateHint.textContent = '{{ __("messages.Permanent workgroups do not have an end date.") }}';
                    endDateRequiredMark.classList.add('d-none');
                }
            }

            typeSelect.addEventListener('change', updateWorkgroupTypeRules);
            updateWorkgroupTypeRules();

            // Status Badge updates
            statusSelect.addEventListener('change', function () {
                const s = this.value;
                if (s === 'active') {
                    liveStatusBadge.className = 'badge bg-success bg-opacity-75 px-3 py-2 text-white fw-semibold rounded-pill';
                    liveStatusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> {{ __("messages.Active") }}';
                } else if (s === 'completed') {
                    liveStatusBadge.className = 'badge bg-info bg-opacity-75 px-3 py-2 text-dark fw-semibold rounded-pill';
                    liveStatusBadge.innerHTML = '<i class="bi bi-flag me-1"></i> {{ __("messages.Completed") }}';
                } else {
                    liveStatusBadge.className = 'badge bg-secondary bg-opacity-75 px-3 py-2 text-white fw-semibold rounded-pill';
                    liveStatusBadge.innerHTML = '<i class="bi bi-archive me-1"></i> {{ __("messages.Archived") }}';
                }
            });

            // Officer Search Filtering
            const officerSearchInput = document.getElementById('officerSearchInput');
            const officerSelect = document.getElementById('officerSelect');
            if (officerSearchInput && officerSelect) {
                const originalOptions = Array.from(officerSelect.options);
                officerSearchInput.addEventListener('input', function () {
                    const query = this.value.trim().toLowerCase();
                    officerSelect.innerHTML = '';
                    originalOptions.forEach(opt => {
                        const name = opt.getAttribute('data-name') || '';
                        const email = opt.getAttribute('data-email') || '';
                        if (!query || name.includes(query) || email.includes(query)) {
                            officerSelect.appendChild(opt);
                        }
                    });
                });
            }

            // Live Logo Preview & Dropzone
            const logoInput = document.getElementById('logoInput');
            const logoPreviewImg = document.getElementById('logoPreviewImg');
            const defaultLogoSrc = logoPreviewImg.src;
            const removeLogoPreviewBtn = document.getElementById('removeLogoPreviewBtn');

            if (logoInput) {
                logoInput.addEventListener('change', function () {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        if (file.size > 2 * 1024 * 1024) {
                            alert('{{ __("messages.Allowed types: PNG, JPG, JPEG. Max size 2MB.") }}');
                            this.value = '';
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            logoPreviewImg.src = e.target.result;
                            removeLogoPreviewBtn.classList.remove('d-none');
                        };
                        reader.readAsDataURL(file);
                    }
                });

                removeLogoPreviewBtn.addEventListener('click', function () {
                    logoInput.value = '';
                    logoPreviewImg.src = defaultLogoSrc;
                    this.classList.add('d-none');
                });
            }
        });
    </script>
</x-layout>
