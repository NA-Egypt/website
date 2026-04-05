@php
    $isArabic = app()->getLocale() === 'ar';
    $dir = $isArabic ? 'rtl' : 'ltr';
    $align = $isArabic ? 'right' : 'left';
    
    // Strings
    $btnTitle = $isArabic ? 'للتعاون مع زمالة المدمنين المجهولين - مصر' : 'Cooperate with Narcotics Anonymous - Egypt';
    $successTitle = $isArabic ? 'شكرًا لتواصلك مع زمالة المدمنين المجهولين' : 'Thank you for contacting Narcotics Anonymous';
    $successDesc = $isArabic ? 'وسنقوم بالتواصل معك في أقرب وقت' : 'We will contact you shortly';
    
    $introText = $isArabic ? 'هذه الاستمارة موجهة للسادة أصحاب التخصصات المعنية بالتعامل مع مدمني المخدرات ومساعدتهم للتعافي أو الشفاء من مشكلة ادمانهم على المخدرات (الأطباء، المعالجين، الاستشاريين، الإعلاميين، الأكاديميين، موظفي أو متطوعي الجمعيات أو الأنشطة المشابهة) الراغبين في التعرف على طريقة برنامج زمالة المدمنين المجهولين.' : 'This form is intended for professionals involved in treating and helping drug addicts recover from their addiction (doctors, therapists, consultants, media professionals, academics, employees, or volunteers of similar associations or activities) who wish to learn about the Narcotics Anonymous program.';
    $badgeText = $isArabic ? '"نستخدم بياناتكم للتواصل فقط في نطاق خدمات العلاقات العامة، ولن تتم مشاركتها مع أي جهة أخرى"' : '"We use your data only for communication within PR services, and it will not be shared with any other party"';
    
    $basicInfoTitle = $isArabic ? 'البيانات الأساسية' : 'Basic Information';
    $nameLbl = $isArabic ? 'الاسم بالكامل' : 'Full Name';
    $professionLbl = $isArabic ? 'المهنة والتخصص' : 'Profession and Specialization';
    $organizationLbl = $isArabic ? 'اسم المؤسسة أو جهة العمل' : 'Organization or Workplace Name';
    $emailLbl = $isArabic ? 'البريد الإلكتروني' : 'Email Address';
    $phoneLbl = $isArabic ? 'رقم الهاتف' : 'Phone Number';
    $cityLbl = $isArabic ? 'المحافظة / المدينة' : 'Governorate / City';
    
    $coopTitle = $isArabic ? 'طبيعة التعاون المطلوب' : 'Nature of Required Cooperation';
    $coopSubtitle = $isArabic ? 'اختر ما يناسبك – يمكن اختيار عدة اختيارات' : 'Choose what suits you - multiple choices allowed';
    
    $cooperationOptionsRaw = [
        ['ar' => 'اود التعرف بشكل أكثر على برنامج زمالة المدمنين المجهولين.', 'en' => 'I would like to learn more about the Narcotics Anonymous program.'],
        ['ar' => 'اود الحصول على مواد تعريفية (نشرات – كتيبات – مطبوعات).', 'en' => 'I would like to receive informational materials (pamphlets - booklets - prints).'],
        ['ar' => 'ارغب في التعاون لتنظيم ندوة تعريفية عن الزمالة.', 'en' => 'I would like to cooperate in organizing an introductory seminar about the fellowship.'],
        ['ar' => 'ارغب في التعاون لإحالة المرضى أو المهتمين برسالتكم إلى الزمالة.', 'en' => 'I would like to cooperate in referring patients or those interested in your message to the fellowship.'],
        ['ar' => 'التنسيق الإعلامي (مقابلات – تقارير صحفية/تلفزيونية).', 'en' => 'Media coordination (interviews - press/TV reports).']
    ];
    
    $otherLbl = $isArabic ? 'أخرى' : 'Other';
    $specifyLbl = $isArabic ? 'حدد...' : 'Specify...';
    
    $questionsTitle = $isArabic ? 'أسئلة واستفسارات' : 'Questions and Inquiries';
    $questionsLbl = $isArabic ? 'ما هي استفساراتك حول زمالة المدمنين المجهولين؟' : 'What are your inquiries about Narcotics Anonymous?';
    $questionsPlaceholder = $isArabic ? 'مساحة مفتوحة للإجابة...' : 'Open space for answers...';
    
    $contactMethodTitle = $isArabic ? 'أفضل وسيلة للتواصل' : 'Preferred Communication Method';
    $contactMethodsRaw = [
        ['ar' => 'البريد الإلكتروني', 'en' => 'Email'],
        ['ar' => 'الهاتف', 'en' => 'Phone'],
        ['ar' => 'واتساب', 'en' => 'WhatsApp']
    ];
    
    $contactTimeTitle = $isArabic ? 'أفضل وقت للتواصل' : 'Preferred Time to Communicate';
    $contactTimesRaw = [
        ['ar' => 'صباحًا', 'en' => 'Morning'],
        ['ar' => 'مساءًا', 'en' => 'Evening'],
        ['ar' => 'غير محدد', 'en' => 'Not Specified']
    ];
    
    $agreementBlk1 = $isArabic ? 'أوافق على استخدام بياناتي للتواصل معي فقط بخصوص خدمات ومعلومات عن زمالة المدمنين المجهولين.' : 'I agree to use my data to contact me only regarding NA services and information.';
    $agreementBlk2 = $isArabic ? '(لن يتم مشاركة بياناتك مع أي جهة أخرى)' : '(Your data will not be shared with any other party)';
    $agreementError = $isArabic ? 'يجب الموافقة على هذا الشرط لإرسال الطلب.' : 'You must agree to this condition to submit the request.';
    
    $btnCancel = $isArabic ? 'إلغاء' : 'Cancel';
    $btnSubmit = $isArabic ? 'إرسال الطلب' : 'Submit Request';
    $btnSending = $isArabic ? 'جاري الإرسال...' : 'Sending...';
    $btnClose = $isArabic ? 'إغلاق' : 'Close';

    $langKey = $isArabic ? 'ar' : 'en';
@endphp

<div>
    <!-- Trigger Button -->
    <button wire:click="openModal" class="btn btn-primary px-4 py-2" style="background-color: #1e3a8a; border: none; border-radius: 8px; font-size: 1.1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        {{ $btnTitle }}
    </button>

    <!-- Modal Background -->
    @if($showModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5); z-index: 1055;">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content" dir="{{ $dir }}" style="text-align: {{ $align }}; border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                <div class="modal-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; border-radius: 12px 12px 0 0; padding: 1rem 1.5rem;">
                    <h5 class="modal-title fw-bold m-0" style="color: white; font-size: 1.25rem;">{{ $btnTitle }}</h5>
                    <button type="button" class="btn-close btn-close-white m-0 p-2" wire:click="closeModal" aria-label="Close" style="background-color: transparent; border: none; font-size: 1.5rem; color: white;">&times;</button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    @if($successMessage)
                        <div class="alert alert-success text-center py-4 mb-0" style="border-radius: 8px;">
                            <i class="bi bi-check-circle-fill d-block mb-3" style="font-size: 3rem; color: #10b981;"></i>
                            <h4 class="alert-heading fw-bold mb-2">{{ $successTitle }}</h4>
                            <p class="mb-0 fs-5">{{ $successDesc }}</p>
                        </div>
                    @else
                        <div class="mb-4 text-center">
                            <p class="text-muted" style="line-height: 1.6;">{{ $introText }}</p>
                            <div class="badge bg-info bg-opacity-10 text-primary p-3 rounded text-wrap" style="line-height: 1.5; font-size: 0.95rem;">
                                {{ $badgeText }}
                            </div>
                        </div>

                        <form wire:submit.prevent="submit">
                            <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">{{ $basicInfoTitle }}</h5>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $nameLbl }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.defer="name">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $professionLbl }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.defer="profession">
                                    @error('profession') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $organizationLbl }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.defer="organization">
                                    @error('organization') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $emailLbl }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" wire:model.defer="email" style="text-align: left; direction: ltr;">
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $phoneLbl }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.defer="phone" style="text-align: left; direction: ltr;">
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $cityLbl }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model.defer="city">
                                    @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">{{ $coopTitle }}</h5>
                            <p class="text-muted small mb-2">{{ $coopSubtitle }}</p>
                            <div class="mb-4 bg-white p-3 rounded border">
                                @foreach($cooperationOptionsRaw as $index => $option)
                                <label class="d-flex align-items-start gap-2 mb-2" style="cursor: pointer;">
                                    <input class="form-check-input m-0 mt-1 flex-shrink-0" type="checkbox" value="{{ $option['ar'] }}" id="coop_{{ $index }}" wire:model.defer="cooperationType">
                                    <span class="form-check-label" style="user-select: none;">{{ $option[$langKey] }}</span>
                                </label>
                                @endforeach
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                                        <input class="form-check-input m-0 flex-shrink-0" type="checkbox" value="أخرى" id="coop_other" wire:model.lazy="cooperationType">
                                        <span class="form-check-label" style="user-select: none;">{{ $otherLbl }}</span>
                                    </label>
                                    @if(is_array($cooperationType) && in_array('أخرى', $cooperationType))
                                    <div class="flex-grow-1">
                                        <input type="text" class="form-control form-control-sm w-100" placeholder="{{ $specifyLbl }}" wire:model.defer="cooperationTypeOther">
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">{{ $questionsTitle }}</h5>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ $questionsLbl }}</label>
                                <textarea class="form-control" rows="3" placeholder="{{ $questionsPlaceholder }}" wire:model.defer="questions"></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h6 class="fw-bold mb-2">{{ $contactMethodTitle }} <span class="text-danger">*</span></h6>
                                    <div class="bg-white p-3 rounded border h-100">
                                        @foreach($contactMethodsRaw as $index => $method)
                                        <label class="d-flex align-items-start gap-2 mb-2" style="cursor: pointer;">
                                            <input class="form-check-input m-0 mt-1 flex-shrink-0" type="radio" name="contactMethod" value="{{ $method['ar'] }}" id="method_{{ $index }}" wire:model.lazy="contactMethod">
                                            <span class="form-check-label" style="user-select: none;">{{ $method[$langKey] }}</span>
                                        </label>
                                        @endforeach
                                        <div class="d-flex align-items-center flex-wrap gap-2 mb-0">
                                            <label class="d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                                                <input class="form-check-input m-0 flex-shrink-0" type="radio" name="contactMethod" value="أخرى" id="method_other" wire:model.lazy="contactMethod">
                                                <span class="form-check-label" style="user-select: none;">{{ $otherLbl }}</span>
                                            </label>
                                            @if($contactMethod === 'أخرى')
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control form-control-sm w-100" placeholder="{{ $specifyLbl }}" wire:model.defer="contactMethodOther">
                                            </div>
                                            @endif
                                        </div>
                                        @error('contactMethod') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-2">{{ $contactTimeTitle }} <span class="text-danger">*</span></h6>
                                    <div class="bg-white p-3 rounded border h-100">
                                        @foreach($contactTimesRaw as $index => $time)
                                        <label class="d-flex align-items-start gap-2 mb-2" style="cursor: pointer;">
                                            <input class="form-check-input m-0 mt-1 flex-shrink-0" type="radio" name="contactTime" value="{{ $time['ar'] }}" id="time_{{ $index }}" wire:model.defer="contactTime">
                                            <span class="form-check-label" style="user-select: none;">{{ $time[$langKey] }}</span>
                                        </label>
                                        @endforeach
                                        @error('contactTime') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 bg-light p-3 rounded border border-warning" style="background-color: #fffbeb !important;">
                                <label class="d-flex align-items-start gap-2 mb-0" style="cursor: pointer;">
                                    <input class="form-check-input m-0 mt-1 flex-shrink-0" type="checkbox" id="agreement" wire:model.defer="agreement">
                                    <span class="form-check-label" style="user-select: none;">
                                        <strong>{{ $agreementBlk1 }}</strong>
                                        <div class="text-muted small mt-1">{{ $agreementBlk2 }}</div>
                                    </span>
                                </label>
                                @error('agreement') <span class="text-danger small fw-bold mt-2 d-block">{{ $agreementError }}</span> @enderror
                            </div>
                        @endif
                </div>
                
                <div class="modal-footer d-flex" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-radius: 0 0 12px 12px; gap: 10px;">
                    @if(!$successMessage)
                        <button type="button" class="btn btn-secondary px-4 me-auto" wire:click="closeModal">{{ $btnCancel }}</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold" style="background-color: #1e3a8a; border: none;">
                            <span wire:loading.remove wire:target="submit">{{ $btnSubmit }}</span>
                            <span wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ $btnSending }}
                            </span>
                        </button>
                    @else
                        <button type="button" class="btn btn-primary px-5 fw-bold ms-auto mx-auto w-100" wire:click="closeModal" style="background-color: #1e3a8a; border: none;">{{ $btnClose }}</button>
                    @endif
                </div>
                @if(!$successMessage)
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
    @endif
</div>
