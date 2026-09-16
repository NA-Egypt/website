<x-layout>
    <x-backhead>{{ __('messages.Helpline Calls') ?? 'مكالمات خط المساعدة' }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Bar Actions --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-telephone-fill text-primary me-2"></i>{{ __('messages.Helpline Calls Management') ?? 'إدارة تقارير واستجابات خط المساعدة' }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.Cycle reset note') ?? 'يتم تحديث الدورة وحساب التقارير شهرياً بنهاية أول ثلاثاء من كل شهر.' }}
                </p>
            </div>
            
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('forms.helpline.show') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('messages.Open Public Form') ?? 'فتح النموذج العام' }}
                </a>
                <a href="{{ route('helpline.volunteers') }}" class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-people-fill"></i> {{ __('messages.Manage Volunteers') ?? 'قائمة المتطوعين' }}
                </a>
                <form action="{{ route('helpline.sync.workgroup') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="cycle" value="{{ $selectedCycleKey }}">
                    <button type="submit" class="btn btn-primary rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm" onclick="return confirm('هل تريد إنشاء/تحديث مسودة تقرير مجموعة العمل لهذه الدورة لتتمكن لجنة العلاقات العامة من إدراجه؟')">
                        <i class="bi bi-journal-arrow-up"></i> {{ __('messages.Sync to PR Report') ?? 'إدراج بتقرير لجنة العلاقات العامة' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Standalone Vue Component Container --}}
        <div 
            data-vue-app="HelplineResponsesDataTable"
            data-initial-data='@json($calls)'
            data-initial-metrics='@json($reportData)'
            data-initial-cycles='@json($availableCycles)'
            data-current-cycle-key="{{ $selectedCycleKey }}"
            data-period-label="{{ $periodLabel }}"
            data-fetch-url="{{ route('helpline.data') }}"
            data-export-excel-route="{{ route('helpline.export.excel') }}"
            data-export-pdf-route="{{ route('helpline.export.pdf') }}"
            data-csrf-token="{{ csrf_token() }}"
        ></div>
    </div>
</x-layout>
