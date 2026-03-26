<footer class="text-center py-3 mt-4">
    <div class="mb-2">
        <a href="https://www.facebook.com/OfficialNAEgyPage" target="_blank" class="footer-social-icon"><x-fab-facebook class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="https://www.instagram.com/narcoticsanonymousegy" target="_blank" class="footer-social-icon"><x-fab-instagram class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="https://www.tiktok.com/@narcoticsanonymousegypt" target="_blank" class="footer-social-icon"><x-fab-tiktok class="mx-1" style="width:24px; height:24px;" /></a>
        <a href="mailto:pr@naegypt.org" class="footer-social-icon"><x-fas-envelope class="mx-1" style="width:24px; height:24px;" /></a>
    </div>
    <div class="small">
        {{ __('messages.copyrights') }} &copy; {{ __('messages.Egypt') }}
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