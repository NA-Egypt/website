<div class="w-100" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    @if($isModal)
        <div class="p-4">
    @else
        <div class="container-fluid py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="glass-card shadow-lg p-4">
    @endif
                
                {{-- Wizard Header --}}
                <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">
                            {{ app()->getLocale() === 'ar' ? 'تصدير الاجتماعات للطباعة' : 'Export Meetings for Printing' }}
                        </h4>
                        <p class="text-secondary small mb-0">
                            {{ app()->getLocale() === 'ar' ? 'قم بإعداد وتصدير قائمة الاجتماعات في ملف PDF منسق للطباعة.' : 'Prepare and export meetings list in a print-ready A4 PDF format.' }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                            {{ app()->getLocale() === 'ar' ? 'الخطوة' : 'Step' }} {{ $step }} / 2
                        </span>
                        <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                {{-- Step Progress Indicator --}}
                <div class="d-flex align-items-center justify-content-center mb-4 px-3">
                    <div class="d-flex align-items-center w-100" style="max-width: 400px;">
                        <div class="text-center position-relative" style="flex: 1;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ $step >= 1 ? 'bg-primary text-white' : 'bg-light text-muted border' }}" style="width: 36px; height: 36px; font-weight: bold; border-width: 2px !important;">1</div>
                            <span class="small fw-semibold {{ $step >= 1 ? 'text-primary' : 'text-muted' }}">
                                {{ app()->getLocale() === 'ar' ? 'المناطق والمنتديات' : 'Service Bodies' }}
                            </span>
                        </div>
                        <div class="flex-grow-1 border-top" style="border-width: 2px !important; border-color: {{ $step >= 2 ? '#3b82f6' : '#dee2e6' }} !important; margin-bottom: 20px;"></div>
                        <div class="text-center position-relative" style="flex: 1;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 {{ $step >= 2 ? 'bg-primary text-white' : 'bg-light text-muted border' }}" style="width: 36px; height: 36px; font-weight: bold; border-width: 2px !important;">2</div>
                            <span class="small fw-semibold {{ $step >= 2 ? 'text-primary' : 'text-muted' }}">
                                {{ app()->getLocale() === 'ar' ? 'تحديد الحقول' : 'Select Fields' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Step 1: Choose Criteria --}}
                @if($step == 1)
                    <div class="step-content">
                        
                        {{-- Selection Mode Toggle --}}
                        <div class="mb-4 d-flex justify-content-center gap-3">
                            <button type="button" wire:click="$set('exportType', 'service_bodies')" 
                                class="btn {{ $exportType === 'service_bodies' ? 'btn-primary' : 'btn-outline-secondary' }} px-4 py-2 rounded-pill fw-semibold">
                                <i class="bi bi-diagram-3 me-1"></i>{{ app()->getLocale() === 'ar' ? 'تصفية حسب المناطق والمنتديات' : 'Filter by Service Bodies' }}
                            </button>
                            <button type="button" wire:click="$set('exportType', 'cities')" 
                                class="btn {{ $exportType === 'cities' ? 'btn-primary' : 'btn-outline-secondary' }} px-4 py-2 rounded-pill fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i>{{ app()->getLocale() === 'ar' ? 'تصفية حسب المدن' : 'Filter by Cities' }}
                            </button>
                        </div>

                        @if($exportType === 'service_bodies')
                            <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                                {{ app()->getLocale() === 'ar' ? 'اخترالمناطق والمنتديات لتضمينها في التقرير (حد أقصى: ٢)' : 'Select Service Bodies to include (Max: 2)' }}
                            </h5>
                            
                            @error('selectedServiceBodies')
                                <div class="alert alert-danger p-2 small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="card p-3 mb-4 neo-scrollbar" style="max-height: 350px; overflow-y: auto; background: rgba(0,0,0,0.01) !important;">
                                <div class="row g-2">
                                    @forelse($serviceBodies as $sb)
                                        <div class="col-md-6">
                                            <div class="form-check neo-list-item border p-2 d-flex align-items-center gap-2" style="cursor: pointer; border-radius: 8px; border-color: var(--glass-border) !important;">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $sb['id'] }}" id="sb-{{ $sb['id'] }}" wire:model.live="selectedServiceBodies">
                                                <label class="form-check-label w-100 text-start" for="sb-{{ $sb['id'] }}" style="cursor: pointer; color: var(--text-primary);">
                                                    {{ app()->getLocale() === 'ar' ? ($sb['ar_name'] ?: $sb['en_name']) : ($sb['en_name'] ?: $sb['ar_name']) }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted py-3">
                                            {{ app()->getLocale() === 'ar' ? 'لا يوجد هيئات خدمية متاحة.' : 'No service bodies available.' }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @else
                            <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                                {{ app()->getLocale() === 'ar' ? 'اختر المدن لتضمينها في التقرير (حد أقصى: 3 مدن)' : 'Select Cities to include (Max: 3)' }}
                            </h5>
                            
                            @error('selectedCities')
                                <div class="alert alert-danger p-2 small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="card p-3 mb-4 neo-scrollbar" style="max-height: 350px; overflow-y: auto; background: rgba(0,0,0,0.01) !important;">
                                <div class="row g-2">
                                    @forelse($cities as $city)
                                        <div class="col-md-6">
                                            <div class="form-check neo-list-item border p-2 d-flex align-items-center gap-2" style="cursor: pointer; border-radius: 8px; border-color: var(--glass-border) !important;">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" value="{{ $city['id'] }}" id="city-{{ $city['id'] }}" wire:model.live="selectedCities">
                                                <label class="form-check-label w-100 text-start" for="city-{{ $city['id'] }}" style="cursor: pointer; color: var(--text-primary);">
                                                    {{ app()->getLocale() === 'ar' ? ($city['ar_name'] ?: $city['en_name']) : ($city['en_name'] ?: $city['ar_name']) }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted py-3">
                                            {{ app()->getLocale() === 'ar' ? 'لا يوجد مدن متاحة.' : 'No cities available.' }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button type="button" wire:click="goToStepTwo" class="btn btn-primary px-4 py-2 rounded-pill">
                                {{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }} <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} ms-1"></i>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Step 2: Choose Fields --}}
                @if($step == 2)
                    <div class="step-content">
                        <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                            {{ app()->getLocale() === 'ar' ? 'اختر حقول الاجتماعات المطلوبة لعرضها في الجدول' : 'Select meeting fields to display in the table' }}
                        </h5>
                        <p class="text-secondary small mb-3">
                            {{ app()->getLocale() === 'ar' ? 'ملاحظة: يتم استبعاد (العنوان، الموقع، السعة، الملاحظات وتفاصيل الاتصال) تلقائياً لتكون مناسبة للطباعة.' : 'Note: (address, location, capacity, notes, and contact details) are automatically excluded for layout optimization.' }}
                        </p>

                        @error('selectedFields')
                            <div class="alert alert-danger p-2 small mb-3">{{ $message }}</div>
                        @enderror

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-check neo-list-item border p-3 d-flex align-items-center gap-2" style="border-radius: 8px; border-color: var(--glass-border) !important;">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="topic" id="field-topic" wire:model="selectedFields">
                                    <label class="form-check-label w-100 text-start" for="field-topic" style="cursor: pointer; color: var(--text-primary);">
                                        <strong>{{ app()->getLocale() === 'ar' ? 'موضوع الاجتماع' : 'Meeting Topic' }}</strong>
                                        <span class="d-block text-secondary small">{{ app()->getLocale() === 'ar' ? 'اسم موضوع الاجتماع مثل (دراسة خطوات، مشاركة، إلخ)' : 'Shows the topic/type of recovery focus' }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check neo-list-item border p-3 d-flex align-items-center gap-2" style="border-radius: 8px; border-color: var(--glass-border) !important;">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="time" id="field-time" wire:model="selectedFields">
                                    <label class="form-check-label w-100 text-start" for="field-time" style="cursor: pointer; color: var(--text-primary);">
                                        <strong>{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Time' }}</strong>
                                        <span class="d-block text-secondary small">{{ app()->getLocale() === 'ar' ? 'وقت البدء ووقت الانتهاء' : 'Start and end time' }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check neo-list-item border p-3 d-flex align-items-center gap-2" style="border-radius: 8px; border-color: var(--glass-border) !important;">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="type" id="field-type" wire:model="selectedFields">
                                    <label class="form-check-label w-100 text-start" for="field-type" style="cursor: pointer; color: var(--text-primary);">
                                        <strong>{{ app()->getLocale() === 'ar' ? 'نوع الاجتماع' : 'Meeting Type' }}</strong>
                                        <span class="d-block text-secondary small">{{ app()->getLocale() === 'ar' ? 'مفتوح للزوار أو مغلق للمدمنين فقط' : 'Open or closed format' }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check neo-list-item border p-3 d-flex align-items-center gap-2" style="border-radius: 8px; border-color: var(--glass-border) !important;">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" value="lang" id="field-lang" wire:model="selectedFields">
                                    <label class="form-check-label w-100 text-start" for="field-lang" style="cursor: pointer; color: var(--text-primary);">
                                        <strong>{{ app()->getLocale() === 'ar' ? 'لغة الاجتماع' : 'Language' }}</strong>
                                        <span class="d-block text-secondary small">{{ app()->getLocale() === 'ar' ? 'اللغة المستخدمة في الاجتماع (عربي / إنجليزي)' : 'Meeting language' }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Paper Size Selection --}}
                        <div class="border-top pt-3 mt-4 mb-4">
                            <h6 class="fw-bold mb-2" style="color: var(--text-primary);">
                                {{ app()->getLocale() === 'ar' ? 'اختر حجم ورق الطباعة' : 'Select Paper Size' }}
                            </h6>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="pageSize" id="size-a4" value="A4" wire:model="pageSize">
                                    <label class="form-check-label" for="size-a4" style="color: var(--text-primary); cursor: pointer; font-weight: 600;">
                                        A4 {{ app()->getLocale() === 'ar' ? '(حجم قياسي)' : '(Standard)' }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="pageSize" id="size-a5" value="A5" wire:model="pageSize">
                                    <label class="form-check-label" for="size-a5" style="color: var(--text-primary); cursor: pointer; font-weight: 600;">
                                        A5 {{ app()->getLocale() === 'ar' ? '(حجم صغير مدمج)' : '(Compact)' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" wire:click="backToStepOne" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                                <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} me-1"></i> {{ app()->getLocale() === 'ar' ? 'السابق' : 'Back' }}
                            </button>
                            <button type="button" wire:click="generate" class="btn btn-success px-4 py-2 rounded-pill">
                                <i class="bi bi-file-earmark-pdf me-1"></i> {{ app()->getLocale() === 'ar' ? 'تنزيل ملف PDF للطباعة' : 'Download PDF' }}
                            </button>
                        </div>
                    </div>
                @endif

    @if($isModal)
        </div>
    @else
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
