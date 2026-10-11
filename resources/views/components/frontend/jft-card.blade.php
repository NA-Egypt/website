@props([
    'jftContent' => '',
])

@php
    $cairoDate = \Carbon\Carbon::now('Africa/Cairo');
    $formattedDate = $cairoDate->locale(app()->getLocale())->translatedFormat('l، j F Y');
@endphp

@if(!empty($jftContent))
<section class="na-section-shell p-0 overflow-hidden mb-5" aria-labelledby="jft-heading">
  <div class="na-jft-container">
    <!-- Background Watermark Emblem -->
    <svg viewBox="0 0 100 100" class="na-jft-watermark" aria-hidden="true">
      <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="4" />
      <rect x="23" y="23" width="54" height="54" rx="2" fill="none" stroke="currentColor" stroke-width="3" transform="rotate(45 50 50)" />
      <circle cx="50" cy="50" r="6" fill="currentColor" />
    </svg>

    <div class="position-relative" style="z-index: 1;">
      <!-- Card Header -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom border-teal-10">
        <div>
          <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(14, 131, 136, 0.12); color: var(--na-teal-700); font-size: 0.85rem; font-weight: 700;">
            <i class="bi bi-sun-fill text-warning"></i>
            <span>{{ __('messages.jft_badge_title') ?? 'تأمل اليوم — لليوم فقط' }}</span>
          </div>
          <h2 id="jft-heading" class="na-heading-2 mb-1">
            {{ __('messages.just_for_today_heading') ?? 'قراءات لليوم فقط (Just For Today)' }}
          </h2>
          <div class="text-muted small">
            <i class="bi bi-calendar3 me-1 text-primary"></i>
            <span>{{ $formattedDate }}</span>
          </div>
        </div>

        <!-- Action Tools: WhatsApp Share -->
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="na-whatsapp-share-btn" onclick="shareJftToWhatsApp()" aria-label="{{ __('messages.share_via_whatsapp') ?? 'مشاركة عبر واتساب' }}">
            <i class="bi bi-whatsapp fs-5"></i>
            <span>{{ __('messages.share_via_whatsapp') ?? 'مشاركة التأمل' }}</span>
          </button>
        </div>
      </div>

      <!-- Meditation Body -->
      <div id="jftTextContainer" class="na-jft-content-box" dir="rtl">
        {!! $jftContent !!}
      </div>

      <!-- Footer Micro Quote -->
      <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 text-muted small">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-quote text-primary fs-5"></i>
          <span>{{ __('messages.jft_quote_footer') ?? 'لليوم فقط: أفكاري ستكون مركزة على تعافيّ، وحياتي بدون مخدرات.' }}</span>
        </div>
        <a href="{{ route('frontend.literature') }}" class="text-primary fw-semibold text-decoration-none">
          {{ __('messages.browse_all_literature') ?? 'تصفح مكتبة الأدبيات الكاملة' }} &larr;
        </a>
      </div>
    </div>
  </div>
</section>

<script>
  function shareJftToWhatsApp() {
    const container = document.getElementById('jftTextContainer');
    if (!container) return;

    const rawText = (container.innerText || container.textContent || '').trim();
    // Truncate cleanly if too large for URL payload, retaining the core principle
    const cleanExcerpt = rawText.length > 500 ? rawText.substring(0, 480) + '...' : rawText;
    const shareMessage = '🌿 *تأمل لليوم فقط — زمالة المدمنين المجهولين (مصر)*\n\n' + cleanExcerpt + '\n\n' + '🔗 قراءة التأمل كاملاً من الموقع الرسمي:\n' + window.location.origin;

    const whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(shareMessage);
    window.open(whatsappUrl, '_blank');
  }
</script>
@endif
