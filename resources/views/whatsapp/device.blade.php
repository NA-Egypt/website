<x-layout>
    <x-backhead>{{ __('messages.whatsapp_device') }}</x-backhead>

    <div class="container-fluid px-3 px-md-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-whatsapp text-success me-2"></i>{{ __('messages.whatsapp_multi_device_hub') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.whatsapp_device_desc') }}
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>{{ __('messages.whatsapp_add_device') }}</span>
                </button>
                <a href="{{ route('whatsapp.inbox') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>{{ __('messages.whatsapp_inbox') }}</span>
                </a>
                <a href="{{ route('whatsapp.subscribers.index') }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-broadcast"></i>
                    <span>{{ __('messages.whatsapp_subscribers') }}</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Staging / Dev Mode Notice (egyptna.org) --}}
        @if($isDev)
            <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3 p-md-4 mb-4" style="background: rgba(245, 158, 11, 0.1); border-left: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-2 p-md-3 d-flex align-items-center justify-content-center bg-warning text-white flex-shrink-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-warning-emphasis">
                            <span class="badge bg-warning text-dark me-2">{{ __('messages.whatsapp_dev_badge') }}</span>
                        </h6>
                        <p class="mb-0 text-muted small">
                            {{ __('messages.whatsapp_dev_notice') }}
                            @if(!empty($devWhitelist))
                                <span class="d-block mt-1"><strong>{{ __('messages.Allowed Numbers:') }}</strong> <code>{{ implode(', ', $devWhitelist) }}</code></span>
                            @else
                                <span class="d-block mt-1 text-danger"><strong>{{ __('messages.Notice:') }}</strong> {{ __('No developer whitelist numbers configured in WHATSAPP_DEV_WHITELIST.') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Multi-Device Slots Grid --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                <i class="bi bi-phone-fill text-primary me-2"></i>{{ __('messages.whatsapp_multi_device_hub') }}
                <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2 font-monospace">{{ count($devices) }}</span>
            </h5>
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i>{{ __('Click "Scan Pairing QR" on any device to link or re-authenticate.') }}
            </span>
        </div>

        <div class="row g-3 g-md-4 mb-4" id="deviceCardsContainer">
            @forelse($devices as $dev)
                @php
                    $devId = $dev['id'] ?? 'default';
                    $isPrimary = ($devId === $activeDeviceId);
                    $state = $dev['state'] ?? 'disconnected';
                    $isLoggedIn = in_array($state, ['logged_in', 'connected']);
                    $displayName = $dev['display_name'] ?? '';
                    $jid = $dev['jid'] ?? '';
                @endphp
                <div class="col-12 col-md-6 col-xl-4" id="deviceCard-{{ $devId }}">
                    <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden" style="background: var(--card-bg, #ffffff);">
                        {{-- Top color accent bar --}}
                        <div style="height: 5px; background: {{ $isLoggedIn ? 'linear-gradient(90deg, #10b981, #059669)' : 'linear-gradient(90deg, #f59e0b, #d97706)' }};"></div>
                        
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h5 class="fw-bold mb-0 font-monospace text-primary">{{ $devId }}</h5>
                                        @if($isPrimary)
                                            <span class="badge bg-primary rounded-pill small" style="font-size: 0.7rem;">
                                                <i class="bi bi-star-fill me-1"></i>{{ __('messages.whatsapp_primary_device') }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill small" style="font-size: 0.7rem;">
                                                {{ __('messages.whatsapp_secondary_device') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($displayName)
                                        <div class="fw-semibold text-dark small">{{ $displayName }}</div>
                                    @endif
                                    @if($jid)
                                        <div class="text-muted font-monospace small" style="font-size: 0.78rem;">
                                            <i class="bi bi-telephone me-1"></i>{{ str_replace('@s.whatsapp.net', '', $jid) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Live Status Badge --}}
                                <div class="device-status-badge">
                                    @if($isLoggedIn)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                            <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                                            {{ __('messages.whatsapp_connected') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                            <i class="bi bi-qr-code-scan"></i>
                                            {{ __('messages.whatsapp_scan_qr') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 rounded-4 bg-light mb-3 flex-grow-1">
                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">{{ __('Created') }}</span>
                                        <span class="fw-semibold text-truncate d-block">{{ isset($dev['created_at']) ? \Carbon\Carbon::parse($dev['created_at'])->diffForHumans() : '-' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block" style="font-size: 0.72rem;">{{ __('State') }}</span>
                                        <span class="fw-semibold font-monospace">{{ ucfirst($state) }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Device Actions --}}
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                @if(!$isLoggedIn)
                                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1 btn-open-qr" 
                                            data-device-id="{{ $devId }}" data-device-name="{{ $displayName ?: $devId }}">
                                        <i class="bi bi-qr-code"></i>
                                        <span>{{ __('messages.whatsapp_scan_qr') }}</span>
                                    </button>
                                @endif

                                <form method="POST" action="{{ route('whatsapp.device.reconnect') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="device_id" value="{{ $devId }}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" title="{{ __('messages.whatsapp_reconnect') }}">
                                        <i class="bi bi-arrow-clockwise"></i>
                                        <span class="d-none d-sm-inline">{{ __('messages.whatsapp_reconnect') }}</span>
                                    </button>
                                </form>

                                @if($isLoggedIn)
                                    <form method="POST" action="{{ route('whatsapp.device.logout') }}" class="d-inline" onsubmit="return confirm('{{ __('messages.Are you sure you want to disconnect WhatsApp?') }}')">
                                        @csrf
                                        <input type="hidden" name="device_id" value="{{ $devId }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" title="{{ __('messages.whatsapp_logout') }}">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span class="d-none d-sm-inline">{{ __('messages.whatsapp_logout') }}</span>
                                        </button>
                                    </form>
                                @endif

                                @if(!$isPrimary || count($devices) > 1)
                                    <form method="POST" action="{{ route('whatsapp.device.destroy', $devId) }}" class="d-inline ms-auto" onsubmit="return confirm('{{ __('messages.whatsapp_device_delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px;" title="{{ __('Delete Device Slot') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                        <i class="bi bi-phone text-secondary display-4 mb-3"></i>
                        <h5 class="fw-bold">{{ __('No WhatsApp Devices Configured') }}</h5>
                        <p class="text-muted small mb-3">{{ __('Add your first device slot to generate a pairing QR code and connect.') }}</p>
                        <div>
                            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                                <i class="bi bi-plus-circle me-1"></i>{{ __('messages.whatsapp_add_device') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Test Message Card --}}
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
                            <i class="bi bi-send-fill text-primary me-2"></i>{{ __('messages.whatsapp_send_test') }}
                        </h5>
                        <form method="POST" action="{{ route('whatsapp.device.test') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">{{ __('messages.whatsapp_choose_device') }}</label>
                                    <select name="device_id" class="form-select rounded-3">
                                        @foreach($devices as $dev)
                                            <option value="{{ $dev['id'] }}" {{ $dev['id'] === $activeDeviceId ? 'selected' : '' }}>
                                                {{ $dev['id'] }} {{ !empty($dev['display_name']) ? '('.$dev['display_name'].')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">{{ __('messages.whatsapp_test_phone') }}</label>
                                    <input type="text" name="phone" class="form-control rounded-3" placeholder="2010xxxxxxxx" required value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary rounded-pill w-100 shadow-sm py-2 d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-send"></i>
                                        <span>{{ __('messages.Send') }}</span>
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">{{ __('messages.whatsapp_test_message') }}</label>
                                    <input type="text" name="message" class="form-control rounded-3" placeholder="Hello from NA Egypt WhatsApp service test" required value="{{ old('message', 'Test message from NA-Egypt WhatsApp Automation') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Service Details Card --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">
                            <i class="bi bi-gear-fill me-1"></i>{{ __('Service Overview') }}
                        </h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                            <li class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                <span class="text-muted">{{ __('Microservice URL') }}</span>
                                <code class="small">{{ config('whatsapp.api_url') }}</code>
                            </li>
                            <li class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                <span class="text-muted">{{ __('Bot Automation') }}</span>
                                <span class="badge {{ config('whatsapp.bot_enabled') ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                    {{ config('whatsapp.bot_enabled') ? 'ON' : 'OFF' }}
                                </span>
                            </li>
                            <li class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                                <span class="text-muted">{{ __('Live Agent Timeout') }}</span>
                                <span class="fw-semibold">{{ config('whatsapp.live_agent_timeout_minutes') }} min</span>
                            </li>
                            <li class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('Daily Broadcast Time') }}</span>
                                <span class="fw-semibold text-success">07:00 (Cairo)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive QR Pairing Modal (With Real-Time Live Auto-Polling) --}}
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 bg-light p-4">
                    <div>
                        <h5 class="modal-title fw-bold" id="qrModalLabel">
                            <i class="bi bi-qr-code text-primary me-2"></i>{{ __('messages.whatsapp_scan_qr') }}
                        </h5>
                        <p class="text-muted small mb-0">
                            {{ __('Pairing device:') }} <span id="modalDeviceName" class="fw-bold font-monospace text-primary"></span>
                        </p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseQrModal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    {{-- Instructions --}}
                    <div class="alert alert-light border rounded-3 p-2 small text-muted mb-3 text-start">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        {{ __('messages.whatsapp_scan_qr_desc') }}
                    </div>

                    {{-- Loading Spinner --}}
                    <div id="modalQrLoading" class="py-5" style="display: none;">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mb-0">{{ __('Generating fresh pairing QR code...') }}</p>
                    </div>

                    {{-- QR Code View --}}
                    <div id="modalQrView" style="display: none;">
                        <div class="d-inline-block p-3 rounded-4 bg-white border border-2 border-dashed shadow-sm mb-3 position-relative">
                            <img id="modalQrImg" src="" alt="WhatsApp QR Code" class="img-fluid rounded-3" style="width: 240px; height: 240px; object-fit: contain;">
                        </div>

                        {{-- 30-Second Countdown Progress Bar --}}
                        <div class="mb-2">
                            <div class="progress rounded-pill" style="height: 6px;">
                                <div id="modalQrProgressBar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%;"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted px-1">
                            <span>
                                <span class="spinner-grow spinner-grow-sm text-success me-1" role="status"></span>
                                <span id="modalPollingStatus">{{ __('messages.whatsapp_pairing_in_progress') }}</span>
                            </span>
                            <span>{{ __('Expires in:') }} <strong id="modalQrSeconds" class="text-danger font-monospace">30s</strong></span>
                        </div>
                    </div>

                    {{-- Already Logged In / Connected Alert --}}
                    <div id="modalAlreadyLoggedIn" class="py-4 text-center" style="display: none;">
                        <div class="rounded-circle d-inline-flex p-3 bg-success-subtle text-success mb-3">
                            <i class="bi bi-check-circle-fill display-4"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-2">{{ __('messages.whatsapp_already_paired') }}</h5>
                        <p class="text-muted small mb-3" id="modalAlreadyLoggedInDesc">
                            {{ __('Device is connected and authenticated. No new QR code needed.') }}
                        </p>
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                    </div>

                    {{-- Real-Time Success Animation View --}}
                    <div id="modalQrSuccess" class="py-4 text-center" style="display: none;">
                        <div class="rounded-circle d-inline-flex p-3 bg-success text-white mb-3 shadow animate__animated animate__zoomIn">
                            <i class="bi bi-check-lg display-3"></i>
                        </div>
                        <h4 class="fw-bold text-success mb-2">{{ __('messages.whatsapp_linked_successfully') }}</h4>
                        <p class="text-muted small mb-0">{{ __('The connection was verified. Refreshing dashboard...') }}</p>
                    </div>

                    {{-- Error State --}}
                    <div id="modalQrError" class="py-4 text-center" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill text-danger display-4 mb-2 d-block"></i>
                        <h6 class="fw-bold text-danger mb-2">{{ __('Failed to load pairing QR code') }}</h6>
                        <p class="text-muted small mb-3" id="modalQrErrorMsg"></p>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" id="btnModalRetryQr">
                            <i class="bi bi-arrow-clockwise me-1"></i>{{ __('Retry') }}
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Device Slot Modal --}}
    <div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="POST" action="{{ route('whatsapp.device.store') }}">
                    @csrf
                    <div class="modal-header border-0 bg-light p-4">
                        <h5 class="modal-title fw-bold" id="addDeviceModalLabel">
                            <i class="bi bi-plus-circle text-primary me-2"></i>{{ __('messages.whatsapp_add_device') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">{{ __('messages.whatsapp_device_id') }} <span class="text-danger">*</span></label>
                            <input type="text" name="device_id" class="form-control rounded-3 font-monospace" placeholder="e.g. helpline-cairo, alex-phone, device-2" required pattern="[A-Za-z0-9_-]+" maxlength="50">
                            <div class="form-text small">{{ __('messages.whatsapp_device_id_help') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i>{{ __('Save Device') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let activeDeviceId = null;
            let countdownInterval = null;
            let pollStatusInterval = null;
            const qrModalElement = document.getElementById('qrModal');
            const qrModal = new bootstrap.Modal(qrModalElement);

            const modalDeviceName = document.getElementById('modalDeviceName');
            const modalQrLoading = document.getElementById('modalQrLoading');
            const modalQrView = document.getElementById('modalQrView');
            const modalQrImg = document.getElementById('modalQrImg');
            const modalQrProgressBar = document.getElementById('modalQrProgressBar');
            const modalQrSeconds = document.getElementById('modalQrSeconds');
            const modalAlreadyLoggedIn = document.getElementById('modalAlreadyLoggedIn');
            const modalQrSuccess = document.getElementById('modalQrSuccess');
            const modalQrError = document.getElementById('modalQrError');
            const modalQrErrorMsg = document.getElementById('modalQrErrorMsg');
            const btnModalRetryQr = document.getElementById('btnModalRetryQr');

            // Attach QR button handlers
            document.querySelectorAll('.btn-open-qr').forEach(btn => {
                btn.addEventListener('click', function () {
                    activeDeviceId = this.getAttribute('data-device-id');
                    const devName = this.getAttribute('data-device-name') || activeDeviceId;
                    modalDeviceName.textContent = devName;
                    qrModal.show();
                    loadQrCode(activeDeviceId);
                });
            });

            btnModalRetryQr.addEventListener('click', function () {
                if (activeDeviceId) {
                    loadQrCode(activeDeviceId);
                }
            });

            // When modal closes, clean intervals
            qrModalElement.addEventListener('hidden.bs.modal', function () {
                clearInterval(countdownInterval);
                clearInterval(pollStatusInterval);
                activeDeviceId = null;
            });

            function showState(state) {
                modalQrLoading.style.display = (state === 'loading') ? 'block' : 'none';
                modalQrView.style.display = (state === 'qr') ? 'block' : 'none';
                modalAlreadyLoggedIn.style.display = (state === 'already_logged_in') ? 'block' : 'none';
                modalQrSuccess.style.display = (state === 'success') ? 'block' : 'none';
                modalQrError.style.display = (state === 'error') ? 'block' : 'none';
            }

            function loadQrCode(deviceId) {
                showState('loading');
                clearInterval(countdownInterval);
                clearInterval(pollStatusInterval);

                fetch(`{{ route('whatsapp.device.qr') }}?device_id=${encodeURIComponent(deviceId)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.already_logged_in) {
                            showState('already_logged_in');
                            return;
                        }

                        if (data.success && data.qr_image) {
                            modalQrImg.src = data.qr_image;
                            showState('qr');
                            startCountdown(data.qr_duration || 30, deviceId);
                            startLiveStatusPolling(deviceId);
                        } else {
                            showState('error');
                            modalQrErrorMsg.textContent = data.error || 'Unable to retrieve pairing code.';
                        }
                    })
                    .catch(err => {
                        showState('error');
                        modalQrErrorMsg.textContent = err.message || 'Connection error.';
                    });
            }

            function startCountdown(totalSeconds, deviceId) {
                let remaining = totalSeconds;
                modalQrSeconds.textContent = remaining + 's';
                modalQrProgressBar.style.width = '100%';

                countdownInterval = setInterval(() => {
                    remaining--;
                    if (remaining < 0) {
                        clearInterval(countdownInterval);
                        // Auto-refresh fresh QR code when expired
                        loadQrCode(deviceId);
                        return;
                    }

                    const pct = Math.max(0, Math.round((remaining / totalSeconds) * 100));
                    modalQrProgressBar.style.width = pct + '%';
                    modalQrSeconds.textContent = remaining + 's';

                    if (remaining <= 5) {
                        modalQrProgressBar.className = 'progress-bar bg-danger progress-bar-striped progress-bar-animated';
                    } else if (remaining <= 12) {
                        modalQrProgressBar.className = 'progress-bar bg-warning progress-bar-striped progress-bar-animated';
                    } else {
                        modalQrProgressBar.className = 'progress-bar bg-success progress-bar-striped progress-bar-animated';
                    }
                }, 1000);
            }

            // Real-Time Live Status Polling: Detects when user scans QR on phone
            function startLiveStatusPolling(deviceId) {
                pollStatusInterval = setInterval(() => {
                    fetch(`{{ route('whatsapp.device.check') }}?device_id=${encodeURIComponent(deviceId)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.connected) {
                                // Paired successfully!
                                clearInterval(countdownInterval);
                                clearInterval(pollStatusInterval);
                                showState('success');

                                // Automatically reload after 1.8 seconds to reflect active session
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1800);
                            }
                        })
                        .catch(() => {});
                }, 2500);
            }
        });
    </script>
    @endpush
</x-layout>
