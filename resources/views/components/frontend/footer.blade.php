<footer class="text-center py-3 mt-4" role="contentinfo">
    <div class="mb-2 d-flex justify-content-center align-items-center flex-wrap gap-2">
        <a href="https://www.facebook.com/OfficialNAEgyPage" target="_blank" rel="noopener noreferrer" aria-label="NA Egypt on Facebook" class="footer-social-icon p-2 touch-target"><x-fab-facebook class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="https://www.instagram.com/narcoticsanonymousegy" target="_blank" rel="noopener noreferrer" aria-label="NA Egypt on Instagram" class="footer-social-icon p-2 touch-target"><x-fab-instagram class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="https://www.tiktok.com/@narcoticsanonymousegypt" target="_blank" rel="noopener noreferrer" aria-label="NA Egypt on TikTok" class="footer-social-icon p-2 touch-target"><x-fab-tiktok class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" aria-label="Chat with NA Egypt on WhatsApp" class="footer-social-icon p-2 touch-target"><x-fab-whatsapp class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="mailto:pr@naegypt.org" aria-label="Send email to NA Egypt Public Relations" class="footer-social-icon p-2 touch-target"><x-fas-envelope class="mx-1" style="width:24px; height:24px;" /></a>
    </div>
    <div class="small text-muted">
        {{ __('messages.copyrights') }} &copy; {{ __('messages.Egypt') }}
        | <a href="{{ route('privacy.policy') }}" class="text-secondary text-decoration-none ms-2 me-2">{{ __('messages.privacy_policy') ?? (app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy') }}</a>
    </div>
</footer>

<style>
    .footer-social-icon {
        color: #32557f;
        transition: all 0.3s ease;
        display: inline-block;
    }
    .footer-social-icon:hover {
        filter: drop-shadow(0 0 5px rgba(50, 85, 127, 0.5));
        color: #32557f;
        transform: translateY(-2px);
    }
</style>