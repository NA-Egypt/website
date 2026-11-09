{{-- Interactive Animated Video Guide Modal for GSR Processes (Meetings, Agendas, Literature) --}}
<div class="modal fade" id="gsrVideoModal" tabindex="-1" aria-labelledby="gsrVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: #090d16; border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: #0f172a; border-bottom: 1px solid rgba(255,255,255,0.08);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info text-dark px-2.5 py-1 rounded-pill fw-bold">NA Egypt</span>
                    <h6 class="modal-title fw-bold text-white mb-0" id="gsrVideoModalLabel">
                        {{ __('messages.gsr_animated_guide_title') }}
                    </h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ asset('tutorials/gsr-guide-animation.html') }}" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3 d-inline-flex align-items-center gap-1.5" title="{{ __('messages.open_in_new_tab') }}">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>{{ __('messages.open_in_new_tab') }}</span>
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('messages.close_modal') }}"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 640px; max-height: 80vh;">
                <iframe src="{{ asset('tutorials/gsr-guide-animation.html') }}" style="width: 100%; height: 100%; border: none;" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

