<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="public-link-copy-toast" class="toast align-items-center text-white bg-success border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2 py-2.5 px-3">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span class="fw-semibold">{{ __('messages.public_link_copied') }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    (function () {
        function initPublicLinkCopy() {
            const copyButtons = document.querySelectorAll('.copy-public-link-btn');
            const toastEl = document.getElementById('public-link-copy-toast');
            const toast = (toastEl && typeof bootstrap !== 'undefined' && bootstrap.Toast) 
                ? new bootstrap.Toast(toastEl, { delay: 3000 }) 
                : null;

            copyButtons.forEach(btn => {
                if (btn.dataset.copyInitialized) return;
                btn.dataset.copyInitialized = 'true';

                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    if (!url) return;

                    const originalHtml = this.innerHTML;
                    const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.getAttribute('lang') === 'ar';
                    const copiedFeedback = isRtl ? 'تم النسخ!' : 'Copied!';

                    const copySuccess = () => {
                        this.innerHTML = '<i class="bi bi-check2-circle"></i> <span>' + copiedFeedback + '</span>';
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success', 'text-white');
                        if (toast) {
                            toast.show();
                        }
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                            this.classList.remove('btn-success', 'text-white');
                            this.classList.add('btn-outline-success');
                        }, 2500);
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(url).then(copySuccess).catch(() => fallbackCopy(url, copySuccess));
                    } else {
                        fallbackCopy(url, copySuccess);
                    }
                });
            });

            function fallbackCopy(text, onSuccess) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-9999px';
                textArea.style.top = '-9999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    if (successful && typeof onSuccess === 'function') {
                        onSuccess();
                    } else {
                        window.prompt(isRtl ? 'انسخ الرابط:' : 'Copy link:', text);
                    }
                } catch (err) {
                    window.prompt('Copy link:', text);
                }
                document.body.removeChild(textArea);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPublicLinkCopy);
        } else {
            initPublicLinkCopy();
        }
    })();
</script>

