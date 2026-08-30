@props([
    'url' => '',
    'title' => '',
    'cardClass' => 'mt-4'
])

@php
    $uniqueId = 'zoom_qr_' . \Illuminate\Support\Str::random(8);
    $cleanTitle = $title ? strip_tags($title) : 'Zoom Meeting';
    $slug = \Illuminate\Support\Str::slug($cleanTitle) ?: 'zoom-meeting';
@endphp

@if(!empty($url))
<div id="{{ $uniqueId }}" class="glass-card p-4 rounded-4 shadow-sm border {{ $cardClass }}" style="border-color: rgba(13, 110, 253, 0.15) !important; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px);">
    <div class="row align-items-center g-4">
        <!-- QR Code Display Column -->
        <div class="col-12 col-md-auto text-center">
            <div class="p-3 bg-white rounded-4 shadow-sm d-inline-block border position-relative" style="border-color: rgba(0,0,0,0.06) !important;">
                <div class="qr-canvas-target" style="width: 180px; height: 180px; display: flex; align-items: center; justify-content: center;">
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading QR...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info & Action Buttons Column -->
        <div class="col-12 col-md">
            <div class="d-flex flex-column h-100 justify-content-center">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-xs" style="background-color: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #bae6fd !important; font-size: 0.85rem;">
                        <i class="bi bi-camera-video-fill" style="color: #0284c7;"></i>
                        <span>{{ __('messages.zoom_meeting_qr') }}</span>
                    </span>
                    @if($title)
                        <span class="badge rounded-pill px-3 py-2 fw-semibold text-truncate shadow-xs" style="background-color: #f1f5f9 !important; color: #0f172a !important; border: 1px solid #e2e8f0 !important; max-width: 280px; font-size: 0.85rem;" title="{{ $title }}">
                            <i class="bi bi-people-fill me-1 text-secondary"></i>
                            {{ $title }}
                        </span>
                    @endif
                </div>

                <p class="text-muted small mb-3">
                    <i class="bi bi-phone me-1 text-primary"></i>
                    {{ __('messages.scan_to_join') }}
                </p>

                <!-- Zoom URL preview -->
                <div class="p-2 px-3 rounded-3 bg-light border mb-3 text-truncate d-flex align-items-center justify-content-between gap-2" style="font-size: 0.85rem;">
                    <span class="text-break text-truncate text-secondary font-monospace" title="{{ $url }}">{{ $url }}</span>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary copy-trigger-icon" title="{{ __('messages.copy_zoom_link') }}">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1 shadow-sm hover-scale transition-all">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>{{ __('messages.join_zoom_meeting') }}</span>
                    </a>

                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 copy-trigger-btn">
                        <i class="bi bi-clipboard"></i>
                        <span class="copy-label">{{ __('messages.copy_zoom_link') }}</span>
                    </button>

                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 download-qr-btn">
                        <i class="bi bi-download"></i>
                        <span>{{ __('messages.download_zoom_qr') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initQR() {
        const root = document.getElementById('{{ $uniqueId }}');
        if (!root) return;

        const container = root.querySelector('.qr-canvas-target');
        const copyBtn = root.querySelector('.copy-trigger-btn');
        const copyIcon = root.querySelector('.copy-trigger-icon');
        const copyLabel = root.querySelector('.copy-label');
        const downloadBtn = root.querySelector('.download-qr-btn');
        const url = @json($url);
        const fileName = '{{ $slug }}-zoom-qr.png';

        if (!container || !url) return;

        function loadQRLib(callback) {
            if (typeof QRCode !== 'undefined') {
                callback();
                return;
            }
            if (document.getElementById('qrcodejs-cdn')) {
                const checkInterval = setInterval(() => {
                    if (typeof QRCode !== 'undefined') {
                        clearInterval(checkInterval);
                        callback();
                    }
                }, 50);
                return;
            }
            const script = document.createElement('script');
            script.id = 'qrcodejs-cdn';
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
            script.onload = () => callback();
            document.head.appendChild(script);
        }

        loadQRLib(function() {
            container.innerHTML = '';
            new QRCode(container, {
                text: url,
                width: 180,
                height: 180,
                colorDark: '#0b192c',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            setTimeout(() => {
                const canvas = container.querySelector('canvas');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');
                const logo = new Image();
                logo.onload = function() {
                    const logoSize = 44;
                    const x = (canvas.width - logoSize) / 2;
                    const y = (canvas.height - logoSize) / 2;
                    const padding = 3;
                    const rectSize = logoSize + (padding * 2);
                    const rectX = x - padding;
                    const rectY = y - padding;

                    // Draw white rounded container behind logo for contrast
                    ctx.fillStyle = '#ffffff';
                    ctx.beginPath();
                    if (ctx.roundRect) {
                        ctx.roundRect(rectX, rectY, rectSize, rectSize, 6);
                    } else {
                        ctx.rect(rectX, rectY, rectSize, rectSize);
                    }
                    ctx.fill();

                    // Draw logo
                    ctx.drawImage(logo, x, y, logoSize, logoSize);
                };
                logo.src = '{{ asset("assets/images/na-logo-qr.jpg") }}';
            }, 100);
        });

        // Copy Link
        function handleCopy() {
            navigator.clipboard.writeText(url).then(() => {
                const originalText = copyLabel ? copyLabel.textContent : '';
                if (copyLabel) copyLabel.textContent = '{{ __("messages.copied") }}';
                if (copyBtn) {
                    copyBtn.classList.remove('btn-outline-secondary');
                    copyBtn.classList.add('btn-success');
                    const icon = copyBtn.querySelector('i');
                    if (icon) icon.className = 'bi bi-check-lg';
                }
                setTimeout(() => {
                    if (copyLabel) copyLabel.textContent = originalText;
                    if (copyBtn) {
                        copyBtn.classList.remove('btn-success');
                        copyBtn.classList.add('btn-outline-secondary');
                        const icon = copyBtn.querySelector('i');
                        if (icon) icon.className = 'bi bi-clipboard';
                    }
                }, 2000);
            }).catch(() => {
                // Fallback prompt
                prompt('Copy Zoom Link:', url);
            });
        }

        if (copyBtn) copyBtn.addEventListener('click', handleCopy);
        if (copyIcon) copyIcon.addEventListener('click', handleCopy);

        // Download QR as PNG
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                const canvas = container.querySelector('canvas');
                if (!canvas) return;
                const dataUrl = canvas.toDataURL('image/png');
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQR);
    } else {
        initQR();
    }
})();
</script>
@endif
