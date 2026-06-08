<x-frontend.layout>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" integrity="sha384-3zSEDfvllQohrq0PHL1fOXJuC/jSOO34H46t6UQfobFOmxE5BpjjaIJY5F2/bMnU" crossorigin="anonymous"></script>
<x-section-head>{{__('messages.Recovery Meetings')}}</x-section-head>

<livewire:meeting-filter />

<button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Go to top">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 24px; height: 24px;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</button>

<button id="qrFloatingBtn" title="{{__('messages.scan_qr')}}">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-qr-code" viewBox="0 0 16 16">
        <path d="M2 2h2v2H2V2Z"/>
        <path d="M6 0v6H0V0h6ZM5 1H1v4h4V1ZM4 12H2v2h2v-2Z"/>
        <path d="M6 10v6H0v-6h6Zm-5 1v4h4v-4H1Zm11-9h2v2h-2V2Z"/>
        <path d="M10 0v6h6V0h-6Zm5 1v4h-4V1h4ZM8 1V0h1v2H8v2H7V1h1Zm0 5V4h1v2H8ZM6 8V7h1V6h1v2h1V7h5v1h-4v1h2v1h-1v2h1v1h-1v1h-1v-2h-1v2h-2v-1h2v-1h-3V8Zm2 2v1h2v-1H8Z"/>
        <path d="M10 11v1h2v-1h-2Zm2 2v1h1v-2h-1v1Zm-2-2v1h2v-1h-2Z"/>
    </svg>
</button>

<div id="qrModal" class="qr-modal">
    <div class="qr-modal-content">
        <span class="qr-close-btn">&times;</span>
        <h3 class="qr-modal-title">{{ __('messages.meetings_qr') }}</h3>
        <p class="qr-modal-subtitle">{{ __('messages.scan_qr') }}</p>
        <div class="qr-canvas-wrapper" style="background: white;">
            <div id="qrCanvasContainer" style="width: 256px; height: 256px; margin: 0 auto; display: flex; align-items: center; justify-content: center;"></div>
        </div>
        <button id="qrDownloadBtn" class="qr-download-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16" style="margin-inline-end: 8px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
            </svg>
            {{ __('messages.download_qr') }}
        </button>
    </div>
</div>

<style>
    #scrollToTopBtn {
        position: fixed;
        bottom: 80px;
        z-index: 99999;
        background-color: rgba(0, 105, 143, 0.8);
        color: white;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 12px;
        border-radius: 50%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s, background-color 0.3s;
    }
    html[dir="rtl"] #scrollToTopBtn {
        left: 20px;
    }
    html[dir="ltr"] #scrollToTopBtn,
    html:not([dir]) #scrollToTopBtn {
        right: 20px;
    }
    #scrollToTopBtn.show {
        opacity: 1;
        visibility: visible;
    }
    #scrollToTopBtn:hover {
        background-color: rgba(0, 80, 112, 1);
    }
    @media (min-width: 768px) {
        #scrollToTopBtn {
            bottom: 40px;
        }
        html[dir="rtl"] #scrollToTopBtn {
            left: 30px;
        }
        html[dir="ltr"] #scrollToTopBtn,
        html:not([dir]) #scrollToTopBtn {
            right: 30px;
        }
    }

    /* Floating QR button styling */
    #qrFloatingBtn {
        position: fixed;
        bottom: 140px;
        z-index: 99999;
        background-color: rgba(0, 105, 143, 0.9);
        color: white;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 12px;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: transform 0.2s, background-color 0.3s, box-shadow 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
    }
    html[dir="rtl"] #qrFloatingBtn {
        left: 20px;
    }
    html[dir="ltr"] #qrFloatingBtn,
    html:not([dir]) #qrFloatingBtn {
        right: 20px;
    }
    #qrFloatingBtn:hover {
        background-color: rgba(0, 80, 112, 1);
        transform: scale(1.1);
        box-shadow: 0 6px 15px rgba(0,0,0,0.4);
    }
    @media (min-width: 768px) {
        #qrFloatingBtn {
            bottom: 100px;
        }
        html[dir="rtl"] #qrFloatingBtn {
            left: 30px;
        }
        html[dir="ltr"] #qrFloatingBtn,
        html:not([dir]) #qrFloatingBtn {
            right: 30px;
        }
    }

    /* Modal Styling */
    .qr-modal {
        display: none;
        position: fixed;
        z-index: 100000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .qr-modal.show {
        display: flex;
        opacity: 1;
    }
    .qr-modal-content {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        width: 90%;
        max-width: 380px;
        text-align: center;
        position: relative;
        transform: scale(0.8);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .qr-modal.show .qr-modal-content {
        transform: scale(1);
    }
    .qr-close-btn {
        position: absolute;
        top: 15px;
        font-size: 28px;
        font-weight: bold;
        color: #888;
        cursor: pointer;
        transition: color 0.2s, transform 0.2s;
        line-height: 1;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    html[dir="rtl"] .qr-close-btn {
        left: 15px;
    }
    html[dir="ltr"] .qr-close-btn,
    html:not([dir]) .qr-close-btn {
        right: 15px;
    }
    .qr-close-btn:hover {
        color: #333;
        transform: scale(1.1);
        background-color: rgba(0,0,0,0.05);
    }
    .qr-modal-title {
        margin-top: 10px;
        font-size: 20px;
        font-weight: 700;
        color: #00698f;
    }
    .qr-modal-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }
    .qr-canvas-wrapper {
        background: white;
        padding: 15px;
        border-radius: 12px;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
        display: inline-block;
        margin-bottom: 20px;
        border: 1px solid #eee;
    }
    #qrCanvas {
        display: block;
        max-width: 100%;
        height: auto;
    }
    .qr-download-btn {
        background-color: #00698f;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.2s, box-shadow 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        box-shadow: 0 4px 6px rgba(0, 105, 143, 0.2);
    }
    .qr-download-btn:hover {
        background-color: #005070;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 105, 143, 0.35);
    }
    .qr-download-btn:active {
        transform: translateY(0);
    }
</style>

<script>
    if (!window.setupScrollToTop) {
        window.setupScrollToTop = true;
        window.addEventListener('scroll', function() {
            var btn = document.getElementById('scrollToTopBtn');
            if (!btn) return;
            
            var scrollPos = window.scrollY || window.pageYOffset || document.documentElement.scrollTop || 0;
            if (scrollPos > 100) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        }, { passive: true });
    }
    
    // Trigger once on initialization to handle case where page is already scrolled
    setTimeout(function() {
        window.dispatchEvent(new Event('scroll'));
    }, 100);

    // Floating QR Logic
    document.addEventListener('DOMContentLoaded', function() {
        const qrFloatingBtn = document.getElementById('qrFloatingBtn');
        const qrModal = document.getElementById('qrModal');
        const qrCloseBtn = document.querySelector('.qr-close-btn');
        const qrDownloadBtn = document.getElementById('qrDownloadBtn');
        const container = document.getElementById('qrCanvasContainer');
        let qrGenerated = false;

        // Open modal
        qrFloatingBtn.addEventListener('click', function() {
            qrModal.style.display = 'flex';
            setTimeout(() => {
                qrModal.classList.add('show');
            }, 10);
            
            if (!qrGenerated) {
                generateQRCode();
            }
        });

        // Close modal
        function closeModal() {
            qrModal.classList.remove('show');
            setTimeout(() => {
                qrModal.style.display = 'none';
            }, 300);
        }

        qrCloseBtn.addEventListener('click', closeModal);
        window.addEventListener('click', function(event) {
            if (event.target === qrModal) {
                closeModal();
            }
        });

        function generateQRCode() {
            const currentUrl = window.location.href;
            container.innerHTML = ""; // Clear
            
            // Generate QR Code using qrcode.js
            const qrcodeInstance = new QRCode(container, {
                text: currentUrl,
                width: 256,
                height: 256,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
            
            // Wait briefly for canvas rendering
            setTimeout(() => {
                const canvas = container.querySelector('canvas');
                if (!canvas) {
                    console.error('Canvas element not found in QR container.');
                    return;
                }
                
                // Add the NA logo to the center
                const ctx = canvas.getContext('2d');
                const logo = new Image();
                logo.onload = function() {
                    const logoSize = 64;
                    const x = (canvas.width - logoSize) / 2;
                    const y = (canvas.height - logoSize) / 2;
                    
                    // Draw a white border container under the logo
                    ctx.fillStyle = '#ffffff';
                    ctx.beginPath();
                    const radius = 8;
                    const padding = 4;
                    const rectX = x - padding;
                    const rectY = y - padding;
                    const rectSize = logoSize + padding * 2;
                    
                    if (ctx.roundRect) {
                        ctx.roundRect(rectX, rectY, rectSize, rectSize, radius);
                    } else {
                        ctx.rect(rectX, rectY, rectSize, rectSize);
                    }
                    ctx.fill();
                    
                    // Draw the logo image
                    ctx.drawImage(logo, x, y, logoSize, logoSize);
                    qrGenerated = true;
                };
                logo.onerror = function() {
                    console.error('Logo image failed to load.');
                    qrGenerated = true;
                };
                logo.src = "{{ asset('assets/images/na-logo-qr.jpg') }}";
            }, 150);
        }

        // Download functionality
        qrDownloadBtn.addEventListener('click', function() {
            const canvas = container.querySelector('canvas');
            if (!canvas) return;
            const dataUrl = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'na-meetings-qr.png';
            link.href = dataUrl;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>

</x-frontend.layout>