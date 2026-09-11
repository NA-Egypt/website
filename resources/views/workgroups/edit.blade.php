<x-layout>

    <x-backhead>{{ __('messages.Edit Workgroup') }}</x-backhead>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="glass-card p-4 p-md-5">
                    <form action="{{ route('workgroup.update', $workgroup->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Hierarchy & Classification -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-diagram-3-fill me-2 text-primary"></i> 
                                {{ __('messages.Hierarchy & Classification') }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.Parent Committee') }} <span class="text-danger">*</span></label>
                                    @if($isSuperAdmin)
                                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror" required>
                                            <option value="">{{ __('messages.Select Parent Committee') }}</option>
                                            @foreach($committees as $committee)
                                                <option value="{{ $committee->id }}" {{ old('parent_id', $workgroup->parent_id) == $committee->id ? 'selected' : '' }}>
                                                    {{ app()->getLocale() === 'ar' ? $committee->ar_name : $committee->en_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <input type="text" class="form-control bg-light" readonly value="{{ $workgroup->parent ? (app()->getLocale() === 'ar' ? $workgroup->parent->ar_name : $workgroup->parent->en_name) : '-' }}">
                                        <input type="hidden" name="parent_id" value="{{ $workgroup->parent_id }}">
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.Workgroup Type') }} <span class="text-danger">*</span></label>
                                    <select name="workgroup_type" class="form-select @error('workgroup_type') is-invalid @enderror" required>
                                        <option value="permanent" {{ old('workgroup_type', $workgroup->workgroup_type) === 'permanent' ? 'selected' : '' }}>{{ __('messages.Permanent') }}</option>
                                        <option value="temporary" {{ old('workgroup_type', $workgroup->workgroup_type) === 'temporary' ? 'selected' : '' }}>{{ __('messages.Temporary') }}</option>
                                    </select>
                                    @error('workgroup_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">{{ __('messages.Workgroup Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $workgroup->status) === 'active' ? 'selected' : '' }}>{{ __('messages.Active') }}</option>
                                        <option value="completed" {{ old('status', $workgroup->status) === 'completed' ? 'selected' : '' }}>{{ __('messages.Completed') }}</option>
                                        <option value="archived" {{ old('status', $workgroup->status) === 'archived' ? 'selected' : '' }}>{{ __('messages.Archived') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('messages.Target Start Date') }}</label>
                                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date', $workgroup->start_date ? $workgroup->start_date->format('Y-m-d') : '') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">{{ __('messages.Target End Date') }}</label>
                                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date', $workgroup->end_date ? $workgroup->end_date->format('Y-m-d') : '') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: General Information -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 text-primary"></i> 
                                {{ __('messages.General Information') }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Service Committee Name') }}" :value="$workgroup->ar_name" required/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="en_name" label="{{ __('messages.English Service Committee Name') }}" :value="$workgroup->en_name" required/>
                                </div>
                                <div class="col-12">
                                    @if($isSuperAdmin || (auth()->check() && auth()->user()->hasRole('Committees')))
                                        <x-forms.select :$users name="email" label="{{ __('messages.Email')}} ({{ __('messages.Assigned User') }})" :value="$workgroup->user_id"/>
                                    @else
                                        <input type="hidden" name="email" value="{{ $workgroup->user_id }}"/>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Leadership Contact -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-person-badge-fill me-2 text-success"></i>
                                {{ __('messages.Leadership Contact') }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.input name="chairman_name" label="{{ __('messages.Chairman Name')}}" :value="$workgroup->chairman_name"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="chairman_phone" label="{{ __('messages.Chairman Phone')}}" :value="$workgroup->chairman_phone"/>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Meetings & Location -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                {{ __('messages.Workgroup Meetings') }} & {{ __('messages.Location Details') }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <x-forms.input id="notes" name="notes" label="{{ __('messages.Workgroup Meetings') }} ({{ __('messages.Schedule Description') }})" :value="$workgroup->notes"/>
                                </div>
                                <div class="col-12">
                                    <x-forms.input id="location" name="location" label="{{ __('messages.Locations') }} ({{ __('messages.Zoom / Google Maps URL') }})" :value="$workgroup->location"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}" :value="$workgroup->ar_address"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}" :value="$workgroup->en_address"/>
                                </div>
                            </div>
                        </div>

                        <!-- Section 5: Settings & Logo -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-gear-fill me-2 text-warning"></i>
                                {{ __('messages.Settings & Details') }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <x-forms.textarea name="default_footer" id="default_footer" label="{{ __('messages.Default Report Footer') }}" maxlength="1000" :value="$workgroup->default_footer"/>
                                </div>
                                <div class="col-12">
                                    <label for="logo" class="form-label fw-bold d-flex align-items-center">
                                        <i class="bi bi-image me-2 text-info"></i>
                                        {{ __('messages.Committee Logo') }}
                                    </label>
                                    <input type="file" name="logo" id="logo" class="form-control mb-2" accept="image/png, image/jpeg, image/jpg">
                                    <div class="form-text text-muted mb-2">{{ __('messages.Allowed types: PNG, JPG, JPEG. Max size 2MB.') }}</div>

                                    @if($workgroup->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($workgroup->logo))
                                        <div class="p-2 border rounded d-inline-block bg-light">
                                            <img src="{{ asset('storage/' . $workgroup->logo) }}" alt="Logo" style="max-height: 70px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('workgroup.index') }}" class="btn btn-secondary px-4">{{ __('messages.Cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-5">{{ __('messages.Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layout>
