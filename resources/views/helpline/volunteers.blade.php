<x-layout>
    <x-backhead>{{ __('messages.Manage Helpline Volunteers') ?? 'إدارة متطوعي خط المساعدة' }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Bar Actions --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-people-fill text-primary me-2"></i>{{ __('messages.Helpline Volunteers Roster') ?? 'قائمة متطوعي خطوط المساعدة' }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.Volunteers note') ?? 'المتطوعون النشطون هنا يظهرون مباشرة كخيارات في نموذج تسجيل المكالمات العام، مع إمكانية إضافة "أخرى" دائماً.' }}
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('helpline.index') }}" class="btn btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-arrow-right"></i> {{ __('messages.Back to Dashboard') ?? 'العودة لتقرير المكالمات' }}
                </a>
                <button type="button" class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addVolunteerModal">
                    <i class="bi bi-person-plus-fill"></i> {{ __('messages.Add Volunteer') ?? 'إضافة متطوع جديد' }}
                </button>
            </div>
        </div>

        {{-- Volunteers Table Card --}}
        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center" style="width:100%;">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>اسم المتطوع</th>
                            <th>رقم الهاتف</th>
                            <th>إجمالي المكالمات المسجلة</th>
                            <th>الحالة بالنموذج</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($volunteers as $v)
                            <tr>
                                <td class="fw-bold text-muted">{{ $v->sort_order }}</td>
                                <td class="fw-bold text-start ps-4">
                                    <i class="bi bi-person-circle text-primary me-2 fs-5 align-middle"></i>
                                    {{ $v->name }}
                                </td>
                                <td dir="ltr" class="small">{{ $v->phone ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                        {{ $v->calls_count }} مكالمة
                                    </span>
                                </td>
                                <td>
                                    @if($v->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> مفعّل بالنموذج
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill">
                                            معطّل (مخفي)
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $v->created_at ? $v->created_at->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Toggle Status --}}
                                        <form action="{{ route('helpline.volunteers.toggle', $v->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $v->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} rounded-pill px-2 py-1" title="{{ $v->is_active ? 'تعطيل ظهور المتطوع بالنموذج' : 'تفعيل ظهور المتطوع بالنموذج' }}">
                                                <i class="bi {{ $v->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Edit Button --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 btn-edit-volunteer" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editVolunteerModal" 
                                                data-id="{{ $v->id }}"
                                                data-name="{{ $v->name }}"
                                                data-phone="{{ $v->phone ?? '' }}"
                                                data-sort-order="{{ $v->sort_order }}"
                                                data-is-active="{{ $v->is_active ? '1' : '0' }}"
                                                data-action="{{ route('helpline.volunteers.update', $v->id) }}"
                                                title="تعديل">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('helpline.volunteers.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المتطوع؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="حذف">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted py-5">
                                    <i class="bi bi-people fs-1 d-block mb-2 text-muted"></i>
                                    لم يتم تسجيل متطوعين بعد. انقر على "إضافة متطوع جديد" للبدء.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Volunteer Modal --}}
    <div class="modal fade" id="addVolunteerModal" tabindex="-1" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start border-0 shadow-lg rounded-4">
                <form action="{{ route('helpline.volunteers.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i> إضافة متطوع جديد لخط المساعدة</h5>
                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم المتطوع <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="مثال: أحمد ع." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">رقم الهاتف (اختياري)</label>
                            <input type="text" name="phone" class="form-control rounded-3" placeholder="01xxxxxxxxx" dir="ltr">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ترتيب الظهور</label>
                            <input type="number" name="sort_order" class="form-control rounded-3" value="0">
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addActiveSwitch" checked>
                            <label class="form-check-label fw-bold" for="addActiveSwitch">مفعّل ويظهر مباشرة في خيارات النموذج العام</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">إضافة المتطوع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Volunteer Modal (Single Reusable Modal) --}}
    <div class="modal fade" id="editVolunteerModal" tabindex="-1" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start border-0 shadow-lg rounded-4">
                <form id="editVolunteerForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> تعديل بيانات المتطوع</h5>
                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم المتطوع <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editNameInput" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">رقم الهاتف (اختياري)</label>
                            <input type="text" name="phone" id="editPhoneInput" class="form-control rounded-3" dir="ltr">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ترتيب الظهور بالنموذج</label>
                            <input type="number" name="sort_order" id="editSortOrderInput" class="form-control rounded-3" value="0">
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActiveSwitch">
                            <label class="form-check-label fw-bold" for="editActiveSwitch">مفعّل ويظهر في خيارات النموذج العام</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Move modals to body to prevent backdrop z-index traps and stacking context issues
            const modals = ['addVolunteerModal', 'editVolunteerModal'];
            modals.forEach(function(modalId) {
                const el = document.getElementById(modalId);
                if (el && el.parentElement !== document.body) {
                    document.body.appendChild(el);
                }
            });

            // Populate edit modal when opened
            const editModalEl = document.getElementById('editVolunteerModal');
            if (editModalEl) {
                editModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name') || '';
                    const phone = button.getAttribute('data-phone') || '';
                    const sortOrder = button.getAttribute('data-sort-order') || '0';
                    const isActive = button.getAttribute('data-is-active') === '1';
                    const action = button.getAttribute('data-action') || '';

                    const form = document.getElementById('editVolunteerForm');
                    if (form) form.action = action;

                    const nameInput = document.getElementById('editNameInput');
                    if (nameInput) nameInput.value = name;

                    const phoneInput = document.getElementById('editPhoneInput');
                    if (phoneInput) phoneInput.value = phone;

                    const sortOrderInput = document.getElementById('editSortOrderInput');
                    if (sortOrderInput) sortOrderInput.value = sortOrder;

                    const activeSwitch = document.getElementById('editActiveSwitch');
                    if (activeSwitch) activeSwitch.checked = isActive;
                });
            }
        });
    </script>
</x-layout>
