<x-layout>
    <x-backhead>{{ __('messages.whatsapp_device') }}</x-backhead>

    <div class="container-fluid px-4 py-3">
        {{-- Header & Subtitle --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-whatsapp text-success me-2"></i>{{ __('messages.whatsapp_device') }}
                </h3>
                <p class="text-muted mb-0 small">
                    {{ __('messages.whatsapp_device_desc') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('whatsapp.inbox') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>{{ __('messages.whatsapp_inbox') }}</span>
                </a>
                <a href="{{ route('whatsapp.docs') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-book-half"></i>
                    <span>{{ __('messages.whatsapp_docs') }}</span>
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
            <div class="alert alert-warning rounded-4 border-0 shadow-sm p-4 mb-4" style="background: rgba(245, 158, 11, 0.1); border-left: 5px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-warning text-white" style="width: 48px; height: 48px;">
                        <i class="bi bi-tools fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-warning-emphasis">
                            <span class="badge bg-warning text-dark me-2">{{ __('messages.whatsapp_dev_badge') }}</span>
                        </h5>
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

        <div class="row g-4">
            {{-- Connection Status Card --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-shield-check text-primary me-2"></i>{{ __('messages.whatsapp_device') }}
                            </h5>
                            @if($status['connected'] ?? false)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                    <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                                    {{ __('messages.whatsapp_connected') }}
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-x-circle-fill"></i>
                                    {{ __('messages.whatsapp_disconnected') }}
                                </span>
                            @endif
                        </div>

                        <div class="p-3 rounded-4 bg-light mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <span class="text-muted small d-block">{{ __('messages.Service URL') }}</span>
                                    <span class="fw-bold text-break font-monospace small">{{ config('whatsapp.api_url') }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">{{ __('messages.Bot Automation') }}</span>
                                    <span class="badge {{ config('whatsapp.bot_enabled') ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                        {{ config('whatsapp.bot_enabled') ? 'Enabled (ON)' : 'Disabled (OFF)' }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">{{ __('messages.Live Agent Timeout') }}</span>
                                    <span class="fw-bold">{{ config('whatsapp.live_agent_timeout_minutes') }} {{ __('messages.Minutes') }}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block">{{ __('messages.Daily JFT Broadcast') }}</span>
                                    <span class="fw-bold text-success">07:00 (Cairo)</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <form method="POST" action="{{ route('whatsapp.device.reconnect') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-clockwise"></i>
                                    <span>{{ __('messages.whatsapp_reconnect') }}</span>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('whatsapp.device.logout') }}" onsubmit="return confirm('{{ __('messages.Are you sure you want to disconnect WhatsApp?') }}')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-3 shadow-sm d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>{{ __('messages.whatsapp_logout') }}</span>
                                </button>
                            </form>

                            <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm d-flex align-items-center gap-2 ms-auto" id="btnRefreshQr">
                                <i class="bi bi-qr-code-scan"></i>
                                <span>{{ __('messages.whatsapp_scan_qr') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- QR Scanner Card --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4 text-center">
                        <h5 class="fw-bold mb-2">
                            <i class="bi bi-qr-code text-primary me-2"></i>{{ __('messages.whatsapp_scan_qr') }}
                        </h5>
                        <p class="text-muted small mb-4">
                            {{ __('messages.whatsapp_scan_qr_desc') }}
                        </p>

                        <div id="qrContainer" class="d-flex flex-column align-items-center justify-content-center p-4 rounded-4 bg-light border border-2 border-dashed mb-3" style="min-height: 280px;">
                            <div class="spinner-border text-primary mb-2" role="status" id="qrSpinner" style="display: none;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <img id="qrImage" src="" alt="WhatsApp QR Code" class="img-fluid rounded-3 shadow-sm" style="max-width: 220px; display: none;">
                            <div id="qrPlaceholder" class="text-muted text-center">
                                <i class="bi bi-qr-code display-4 text-secondary mb-2 d-block"></i>
                                <span class="small">{{ __('Click "Scan Pairing QR Code" to generate fresh pairing code') }}</span>
                            </div>
                        </div>

                        <div id="qrTimer" class="small text-muted" style="display: none;">
                            {{ __('QR expires in:') }} <span id="qrSeconds" class="fw-bold text-danger">30</span> {{ __('seconds') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Test Message Card --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg, #ffffff);">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-send-fill text-primary me-2"></i>{{ __('messages.whatsapp_send_test') }}
                        </h5>
                        <form method="POST" action="{{ route('whatsapp.device.test') }}">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">{{ __('messages.whatsapp_test_phone') }}</label>
                                    <input type="text" name="phone" class="form-control rounded-3" placeholder="2010xxxxxxxx" required value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">{{ __('messages.whatsapp_test_message') }}</label>
                                    <input type="text" name="message" class="form-control rounded-3" placeholder="Hello from NA Egypt WhatsApp service test" required value="{{ old('message', 'Test message from NA-Egypt WhatsApp Automation') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary rounded-pill w-100 shadow-sm py-2">
                                        <i class="bi bi-send me-1"></i>{{ __('messages.Send') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnRefreshQr = document.getElementById('btnRefreshQr');
            const qrImage = document.getElementById('qrImage');
            const qrSpinner = document.getElementById('qrSpinner');
            const qrPlaceholder = document.getElementById('qrPlaceholder');
            const qrTimer = document.getElementById('qrTimer');
            const qrSeconds = document.getElementById('qrSeconds');
            let countdownInterval = null;

            function fetchQr() {
                qrSpinner.style.display = 'block';
                qrImage.style.display = 'none';
                qrPlaceholder.style.display = 'none';
                qrTimer.style.display = 'none';
                clearInterval(countdownInterval);

                fetch("{{ route('whatsapp.device.qr') }}")
                    .then(response => response.json())
                    .then(data => {
                        qrSpinner.style.display = 'none';
                        if (data.success && data.qr_image) {
                            qrImage.src = data.qr_image;
                            qrImage.style.display = 'block';
                            qrTimer.style.display = 'block';

                            let timeLeft = data.qr_duration || 30;
                            qrSeconds.textContent = timeLeft;

                            countdownInterval = setInterval(() => {
                                timeLeft--;
                                qrSeconds.textContent = timeLeft;
                                if (timeLeft <= 0) {
                                    clearInterval(countdownInterval);
                                    fetchQr();
                                }
                            }, 1000);
                        } else {
                            qrPlaceholder.innerHTML = '<span class="text-danger">' + (data.error || 'WhatsApp is already connected or service unavailable.') + '</span>';
                            qrPlaceholder.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        qrSpinner.style.display = 'none';
                        qrPlaceholder.innerHTML = '<span class="text-danger">Failed to connect to WhatsApp microservice.</span>';
                        qrPlaceholder.style.display = 'block';
                    });
            }

            if (btnRefreshQr) {
                btnRefreshQr.addEventListener('click', fetchQr);
            }
        });
    </script>
    @endpush
</x-layout>
