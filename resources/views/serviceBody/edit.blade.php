<x-layout>

    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Service Body')}}</x-backhead>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="glass-card p-4 p-md-5">
                    <form action="{{ route('serviceBody.update', $serviceBody->id) }}" method="post" enctype="multipart/form-data">
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
                                    <x-forms.input name="ar_name" label="{{ __('messages.Service Body Arabic Name')}}" :value="$serviceCommittee->ar_name ?? $serviceBody->ar_name"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="en_name" label="{{ __('messages.Service Body English Name')}}" :value="$serviceCommittee->en_name ?? $serviceBody->en_name"/>
                                </div>
                                <div class="col-12">
                                    <x-forms.textarea name="description" label="{{ __('messages.Description')}}" :value="$serviceBody->description"/>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Schedule & Recurrence -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3 pb-2 border-bottom text-primary d-flex align-items-center">
                                <i class="bi bi-calendar-event-fill me-2 text-success"></i>
                                {{ __('messages.Schedule & Recurrence') ?? 'Schedule & Recurrence' }}
                            </h5>
                            <div class="row g-3 align-items-end">
                                <div class="col-12">
                                    <x-forms.select :$days name="day_id" label="{{ __('messages.Day')}}" :value="$serviceBody->day_id"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="start_time" label="{{ __('messages.From')}}" type="time" :value="$serviceBody->start_time"/>
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input name="end_time" label="{{ __('messages.To')}}" type="time" :value="$serviceBody->end_time"/>
                                </div>
                                <div class="col-12 mt-3">
                                    <h6 class="fw-bold mb-3" style="color: var(--text-primary);">
                                        <i class="bi bi-calendar-week me-2 text-warning"></i> {{ __('messages.Recurrence') }}
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php
                                            $recurrences = [
                                                '1st' => __('messages.1st'),
                                                'monthly' => __('messages.monthly'),
                                            ];
                                            $selectedRecurrence = old('recurrence', $serviceBody->recurrence ?? ['1st', 'monthly']);
                                        @endphp
                                        @foreach($recurrences as $val => $label)
                                            <div class="form-check form-check-inline glass-card p-2 rounded-3 m-0 d-flex align-items-center" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.4);">
                                                <input class="form-check-input mt-0" type="checkbox" name="recurrence[]" id="recurrence-{{ $val }}" value="{{ $val }}" {{ is_array($selectedRecurrence) && in_array($val, $selectedRecurrence) ? 'checked' : '' }} style="margin-inline-end: 0.5rem; width: 1.2em; height: 1.2em;">
                                                <label class="form-check-label text-secondary fw-medium mb-0" for="recurrence-{{ $val }}" style="font-size: 0.9rem;">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
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
                                    <x-forms.input name="location" label="{{ __('messages.Location')}}" :value="$serviceBody->location"/>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3 p-3 rounded-3" style="border: 1px dashed var(--glass-border); background: rgba(0,0,0,0.01);">
                                        <label for="logo" class="form-label fw-bold d-flex align-items-center">
                                            <i class="bi bi-image me-2 text-info"></i>
                                            {{ __('messages.Service Body Logo') ?? 'Service Body Logo' }}
                                        </label>
                                        <input type="file" name="logo" id="logo" class="form-control mb-3" accept="image/png, image/jpeg, image/jpg">
                                        <div class="form-text text-muted mb-3">{{ __('messages.Allowed types: PNG, JPG, JPEG. Max size 2MB.') ?? 'Allowed types: PNG, JPG, JPEG. Max size 2MB.' }}</div>
                                        
                                        <!-- Current / Preview Logo Area -->
                                        <div id="logo-preview-container" class="mt-2 {{ $serviceBody->logo ? '' : 'd-none' }}">
                                            <p class="text-secondary small mb-1" id="logo-preview-label">
                                                {{ $serviceBody->logo ? (__('messages.Current Logo') ?? 'Current Logo') : (__('messages.Logo Preview') ?? 'Logo Preview') }}:
                                            </p>
                                            <img id="logo-preview" 
                                                 src="{{ $serviceBody->logo ? asset('storage/' . $serviceBody->logo) : '#' }}" 
                                                 alt="Logo Preview" 
                                                 class="img-thumbnail" 
                                                 style="max-height: 150px; border-radius: 8px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-3 border-top">
                            <x-forms.normal-button color='outline-dark px-4 py-2' name="{{ __('messages.Update') }}" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side image preview script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</x-layout>