<x-layout>

    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Service Committees')}}</x-backhead>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="glass-card p-4 p-md-5">
                    <form action="{{ route('serviceCommittee.update', $serviceCommittee->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: General Information -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2 text-primary"></i> 
                                {{ __('messages.General Information') ?? 'General Information' }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Service Committee Name') }}" :value="$serviceCommittee->ar_name"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="en_name" label="{{ __('messages.English Service Committee Name') }}" :value="$serviceCommittee->en_name"/>
                                </div>
                                <div class="col-12">
                                    @auth
                                        @can('is-super-admin')
                                            <x-forms.select :$users name="email" label="{{ __('messages.Email')}}" :value="$serviceCommittee->user_id"/>
                                        @else
                                            <input type="hidden" name="user_id" value="{{ $serviceCommittee->user_id }}"/>
                                        @endcan
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Leadership Contact -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-person-badge-fill me-2 text-success"></i>
                                {{ __('messages.Leadership Contact') ?? 'Leadership Contact' }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.input name="chairman_name" label="{{ __('messages.Chairman Name')}}" :value="$serviceCommittee->chairman_name"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="chairman_phone" label="{{ __('messages.Chairman Phone')}}" :value="$serviceCommittee->chairman_phone"/>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Location Details -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                {{ __('messages.Location Details') ?? 'Location Details' }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <x-forms.input id="location" name="location" label="{{ __('messages.Locations')}}" :value="$serviceCommittee->location"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}" :value="$serviceCommittee->ar_address"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}" :value="$serviceCommittee->en_address"/>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Settings & Meetings -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-gear-fill me-2 text-warning"></i>
                                {{ __('messages.Settings & Details') ?? 'Settings & Details' }}
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <x-forms.input id="notes" name="notes" label="{{ __('messages.Committee Meetings')}}" :value="$serviceCommittee->notes"/>
                                </div>
                                <div class="col-12">
                                    <x-forms.textarea name="default_footer" id="default_footer" label="{{ __('messages.Default Report Footer') ?? 'Default Report Footer' }}" maxlength="1000" :value="$serviceCommittee->default_footer"/>
                                    <div class="form-text text-muted d-flex justify-content-between align-items-center mt-1" style="font-size: 0.85rem;">
                                        <span><i class="bi bi-info-circle me-1 text-primary"></i> {{ __('messages.Default Report Footer Helper') }}</span>
                                        <span><span id="char-count" class="fw-bold text-primary">1000</span> {{ __('messages.Characters Remaining') }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3 p-3 rounded-3" style="border: 1px dashed var(--glass-border); background: rgba(0,0,0,0.01);">
                                        <label for="logo" class="form-label fw-bold d-flex align-items-center">
                                            <i class="bi bi-image me-2 text-info"></i>
                                            {{ __('messages.Committee Logo') ?? 'Committee Logo' }}
                                        </label>
                                        <input type="file" name="logo" id="logo" class="form-control mb-3" accept="image/png, image/jpeg, image/jpg">
                                        <div class="form-text text-muted mb-3">{{ __('messages.Allowed types: PNG, JPG, JPEG. Max size 2MB.') ?? 'Allowed types: PNG, JPG, JPEG. Max size 2MB.' }}</div>
                                        
                                        <!-- Current / Preview Logo Area -->
                                        <div id="logo-preview-container" class="mt-2 {{ $serviceCommittee->logo ? '' : 'd-none' }}">
                                            <p class="text-secondary small mb-1" id="logo-preview-label">
                                                {{ $serviceCommittee->logo ? (__('messages.Current Logo') ?? 'Current Logo') : (__('messages.Logo Preview') ?? 'Logo Preview') }}:
                                            </p>
                                            <img id="logo-preview" 
                                                 src="{{ $serviceCommittee->logo ? asset('storage/' . $serviceCommittee->logo) : '#' }}" 
                                                 alt="Logo Preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <x-forms.normal-button color='outline-dark px-4 py-2' name="{{ __('messages.Save') }}" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side image preview and character counter script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logo Preview
            const logoInput = document.getElementById('logo');
            const previewContainer = document.getElementById('logo-preview-container');
            const previewImage = document.getElementById('logo-preview');
            const previewLabel = document.getElementById('logo-preview-label');

            if (logoInput) {
                logoInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.setAttribute('src', e.target.result);
                            previewLabel.textContent = "{{ __('messages.Logo Preview') ?? 'Logo Preview' }}:";
                            previewContainer.classList.remove('d-none');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Character Counter
            const footerTextarea = document.getElementById('default_footer');
            const charCountSpan = document.getElementById('char-count');
            const maxLength = 1000;

            if (footerTextarea && charCountSpan) {
                const updateCounter = () => {
                    const remaining = maxLength - footerTextarea.value.length;
                    charCountSpan.textContent = remaining;
                };

                footerTextarea.addEventListener('input', updateCounter);
                // Run on initial load
                updateCounter();
            }
        });
    </script>
</x-layout>