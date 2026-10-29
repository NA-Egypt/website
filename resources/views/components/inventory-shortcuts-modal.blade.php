@props([
    'mode' => 'store', // 'store' or 'lit'
])

<div class="modal fade" id="keyboardShortcutsModal" tabindex="-1" aria-labelledby="keyboardShortcutsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary-subtle text-primary p-4">
                <div>
                    <h4 class="modal-title fw-bold mb-1" id="keyboardShortcutsModalLabel">
                        <i class="bi bi-keyboard me-2"></i>{{ __('messages.keyboard_shortcuts_legend_title') }}
                    </h4>
                    <p class="text-secondary small mb-0">{{ __('messages.keyboard_shortcuts_legend_desc') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="row g-4">
                    {{-- Navigation & Selection --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4 bg-light-subtle border border-light-subtle h-100">
                            <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-arrows-move"></i>
                                {{ __('messages.kb_section_navigation') }}
                            </h6>
                            <div class="d-flex flex-column gap-2.5">
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_nav_up_down') }}</span>
                                    <div class="d-flex gap-1 flex-shrink-0 ms-2" dir="ltr">
                                        <span class="kbd-badge">↑</span>
                                        <span class="kbd-badge">↓</span>
                                        <span class="text-muted small mx-0.5">/</span>
                                        <span class="kbd-badge">J</span>
                                        <span class="kbd-badge">K</span>
                                    </div>
                                </div>
                                @if($mode === 'store')
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_toggle_select') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">Space</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_global_search') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">/</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="small text-dark">{{ __('messages.kb_global_help') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">?</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row Quick Actions --}}
                    <div class="col-12 col-md-6">
                        <div class="p-3 rounded-4 bg-light-subtle border border-light-subtle h-100">
                            <h6 class="fw-bold text-success mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-lightning-charge-fill"></i>
                                {{ __('messages.kb_section_row_actions') }}
                            </h6>
                            <div class="d-flex flex-column gap-2.5">
                                @if($mode === 'store')
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_action_receive') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">R</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_action_transfer') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">T</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_action_return') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">U</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_action_wrong_receive') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">W</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                    <span class="small text-dark">{{ __('messages.kb_action_inline_edit') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">E</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="small text-dark">{{ __('messages.kb_action_damage') }}</span>
                                    <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">D</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Global & Editing Shortcuts --}}
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-light-subtle border border-light-subtle">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-command text-warning-emphasis"></i>
                                {{ __('messages.kb_section_global') }}
                            </h6>
                            <div class="row g-3">
                                @if($mode === 'store')
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                        <span class="small text-dark">{{ __('messages.kb_global_batch') }}</span>
                                        <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">M</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                                        <span class="small text-dark">{{ __('messages.kb_global_new_item') }}</span>
                                        <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">N</span>
                                    </div>
                                </div>
                                @endif
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex justify-content-between align-items-center py-1">
                                        <span class="small text-dark">{{ __('messages.kb_global_save') }}</span>
                                        <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">Enter</span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="d-flex justify-content-between align-items-center py-1">
                                        <span class="small text-dark">{{ __('messages.kb_global_escape') }}</span>
                                        <span class="kbd-badge flex-shrink-0 ms-2" dir="ltr">Esc</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light-subtle p-3">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">
                    {{ __('messages.Close') ?? 'Close' }}
                </button>
            </div>
        </div>
    </div>
</div>

