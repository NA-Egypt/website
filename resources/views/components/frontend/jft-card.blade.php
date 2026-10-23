@props([
    'jftContent' => '',
    'jftReading' => null,
])

@php
    $cairoNow = \Carbon\Carbon::now('Africa/Cairo');
    if (empty($jftReading)) {
        try {
            $jftReading = app(\App\Services\JftService::class)->getReading($cairoNow->format('Y-m-d'));
        } catch (\Throwable $e) {
            $jftReading = [];
        }
    }
    $jftDate = $jftReading['date'] ?? $cairoNow->format('Y-m-d');
    $jftPageDate = $jftReading['page_date'] ?? $cairoNow->locale(app()->getLocale())->translatedFormat('j F');
    $jftTitle = $jftReading['title'] ?? '';
    $jftQuote = $jftReading['quote'] ?? '';
    $jftQuoteSource = $jftReading['quote_source'] ?? '';
    $jftParagraphs = $jftReading['content'] ?? [];
    $jftThought = $jftReading['thought_for_the_day'] ?? '';
    $jftHtml = $jftReading['content_html'] ?? $jftContent;
    $todayDate = $cairoNow->format('Y-m-d');
    $formattedFullDate = $cairoNow->locale(app()->getLocale())->translatedFormat('l، j F Y');
@endphp

<section id="jftSection" class="na-section-shell p-0 overflow-hidden mb-5" aria-labelledby="jft-main-heading">
  <div class="na-jft-container" id="naJftCard">
    <!-- Subtle Watermark Emblem -->
    <svg viewBox="0 0 100 100" class="na-jft-watermark" aria-hidden="true">
      <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="4" />
      <rect x="23" y="23" width="54" height="54" rx="2" fill="none" stroke="currentColor" stroke-width="3" transform="rotate(45 50 50)" />
    </svg>

    <!-- Print Only Header -->
    <div class="d-none d-print-block text-center mb-4 pb-3 border-bottom">
      <h3 class="fw-bold mb-1">{{ __('messages.na_fellowship_egypt_badge') ?? 'زمالة المدمنين المجهولين — مصر' }}</h3>
      <h4 class="text-muted">{{ __('messages.jft_badge_title') ?? 'تأمل اليوم — لليوم فقط' }}</h4>
    </div>

    <div class="position-relative" style="z-index: 2;">
      <!-- ==================================================================
           TOOLBAR ROW: Navigation, Date Picker, Font Size, Print, Share
           ================================================================== -->
      <div class="jft-toolbar-wrapper d-print-none mb-4 pb-3 border-bottom border-light-subtle">
        <!-- Right / Start: Date & Reading Identity -->
        <div class="d-flex flex-column gap-1">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="jft-badge-pill">
              <i class="bi bi-sun-fill text-warning"></i>
              <span id="jftPageDateBadge">{{ $jftPageDate }}</span>
            </span>

            <span class="text-muted small d-inline-flex align-items-center gap-1">
              <i class="bi bi-calendar3 text-primary"></i>
              <span id="jftFullDateText">{{ $formattedFullDate }}</span>
            </span>
          </div>

          <h2 id="jft-main-heading" class="na-heading-2 mb-0 mt-1">
            {{ __('messages.just_for_today_heading') ?? 'قراءات لليوم فقط (Just For Today)' }}
          </h2>
        </div>

        <!-- Left / End: Actions & Date Controls -->
        <div class="jft-actions-wrapper">
          <!-- History Navigator (Quick Arrows + Calendar Input) -->
          <div class="jft-history-deck" role="group" aria-label="{{ __('messages.jft_pick_date') ?? 'تصفح تواريخ التأمل' }}">
            <button type="button" class="jft-nav-btn" onclick="navigateJftDay(-1)" title="{{ __('messages.jft_prev_day') ?? 'الأمس' }}" aria-label="{{ __('messages.jft_prev_day') ?? 'الأمس' }}">
              <i class="bi bi-chevron-right rtl-flip"></i>
              <span class="d-none d-sm-inline">{{ __('messages.jft_prev_day') ?? 'الأمس' }}</span>
            </button>

            <!-- Calendar Picker with Native Date Input Overlay -->
            <label class="jft-calendar-picker-label" title="{{ __('messages.jft_pick_date') ?? 'اختر يوماً من السنة' }}">
              <i class="bi bi-calendar-event text-primary"></i>
              <span id="jftPickerText" class="d-none d-md-inline">{{ $jftPageDate }}</span>
              <input type="date" id="jftDateInput" class="jft-native-date-input" value="{{ $jftDate }}" onchange="handleJftDateSelect(this.value)" aria-label="{{ __('messages.jft_pick_date') ?? 'اختر يوماً من السنة' }}">
            </label>

            <button type="button" class="jft-nav-btn" onclick="navigateJftDay(1)" title="{{ __('messages.jft_next_day') ?? 'الغد' }}" aria-label="{{ __('messages.jft_next_day') ?? 'الغد' }}">
              <span class="d-none d-sm-inline">{{ __('messages.jft_next_day') ?? 'الغد' }}</span>
              <i class="bi bi-chevron-left rtl-flip"></i>
            </button>

            <button type="button" id="jftTodayBtn" class="jft-nav-btn jft-today-pill d-none" onclick="jumpToTodayJft()" title="{{ __('messages.jft_today') ?? 'اليوم' }}">
              <span>{{ __('messages.jft_today') ?? 'اليوم' }}</span>
            </button>
          </div>

          <!-- Utility Tools: Font Size, Print, Share -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Font Size Adjuster (A- / A / A+) -->
            <div class="jft-font-controls" role="group" aria-label="{{ __('messages.jft_font_size') ?? 'حجم الخط' }}">
              <button type="button" class="jft-tool-btn" onclick="adjustJftFontSize(-1)" title="{{ __('messages.jft_font_decrease') ?? 'تصغير الخط' }}" aria-label="{{ __('messages.jft_font_decrease') ?? 'تصغير الخط' }}">
                <span class="fw-bold" style="font-size: 0.78rem;">A-</span>
              </button>
              <button type="button" class="jft-tool-btn" onclick="resetJftFontSize()" title="{{ __('messages.jft_font_reset') ?? 'الحجم الافتراضي' }}" aria-label="{{ __('messages.jft_font_reset') ?? 'الحجم الافتراضي' }}">
                <span class="fw-bold" style="font-size: 0.88rem;">A</span>
              </button>
              <button type="button" class="jft-tool-btn" onclick="adjustJftFontSize(1)" title="{{ __('messages.jft_font_increase') ?? 'تكبير الخط' }}" aria-label="{{ __('messages.jft_font_increase') ?? 'تكبير الخط' }}">
                <span class="fw-bold" style="font-size: 1.05rem;">A+</span>
              </button>
            </div>

            <!-- Print Button -->
            <button type="button" class="jft-tool-btn d-none d-sm-inline-flex" onclick="window.print()" title="{{ __('messages.jft_print') ?? 'طباعة التأمل' }}" aria-label="{{ __('messages.jft_print') ?? 'طباعة التأمل' }}">
              <i class="bi bi-printer"></i>
            </button>

            <!-- Share Button -->
            <button type="button" class="jft-share-btn" onclick="openJftShareModal()" aria-label="{{ __('messages.jft_share_reflection') ?? 'مشاركة التأمل' }}">
              <i class="bi bi-share-fill"></i>
              <span>{{ __('messages.jft_share_reflection') ?? 'مشاركة التأمل' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ==================================================================
           AUDIO NARRATION PLAYER (مشغل صوتي)
           ================================================================== -->
      <div id="jftAudioBar" class="jft-audio-bar d-print-none mb-4">
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
          <button type="button" id="jftAudioPlayBtn" class="jft-audio-play-btn" onclick="toggleJftAudioPlayback()" aria-label="{{ __('messages.jft_play') ?? 'استماع' }}">
            <i class="bi bi-play-fill" id="jftAudioPlayIcon"></i>
            <span id="jftAudioPlayText">{{ __('messages.jft_play') ?? 'استماع' }}</span>
          </button>

          <button type="button" id="jftAudioStopBtn" class="jft-audio-stop-btn d-none" onclick="stopJftAudio()" title="{{ __('messages.jft_stop') ?? 'إيقاف' }}" aria-label="{{ __('messages.jft_stop') ?? 'إيقاف' }}">
            <i class="bi bi-stop-fill"></i>
          </button>
        </div>

        <!-- Audio Progress & Status -->
        <div class="jft-audio-progress-container flex-grow-1">
          <div class="d-flex align-items-center justify-content-between mb-1 small">
            <span id="jftAudioStatusText" class="text-muted" style="font-size: 0.8rem;">
              <i class="bi bi-volume-up text-primary me-1"></i>
              <span>{{ __('messages.jft_audio_narration') ?? 'مشغل القراءة الصوتية' }}</span>
            </span>
            <span id="jftAudioProgressPercent" class="text-muted font-monospace" style="font-size: 0.78rem;">0%</span>
          </div>
          <div class="jft-audio-track" onclick="handleJftAudioSeek(event)">
            <div id="jftAudioFill" class="jft-audio-fill" style="width: 0%;"></div>
          </div>
        </div>

        <!-- Speed Selector Toggle -->
        <div class="d-flex align-items-center gap-1 flex-shrink-0">
          <button type="button" id="jftAudioSpeedBtn" class="jft-speed-pill" onclick="cycleJftAudioSpeed()" title="{{ __('messages.jft_speed') ?? 'السرعة' }}" aria-label="{{ __('messages.jft_speed') ?? 'السرعة' }}">
            <span id="jftAudioSpeedText">1.0x</span>
          </button>
        </div>
      </div>

      <!-- ==================================================================
           READING CONTENT ARCHITECTURE
           ================================================================== -->
      <div id="jftReadingContainer" class="jft-reading-card-surface position-relative" dir="rtl">
        <!-- Loading Overlay -->
        <div id="jftLoadingOverlay" class="jft-loading-overlay d-none">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ __('messages.jft_loading') ?? 'جاري تحميل التأمل...' }}</span>
          </div>
          <span class="text-primary fw-bold mt-2">{{ __('messages.jft_loading') ?? 'جاري تحميل التأمل...' }}</span>
        </div>

        <!-- Title & Subtitle Header -->
        <div class="text-center mb-4">
          <div class="d-inline-block text-primary fw-bold mb-1" id="jftHeaderDateSubtitle" style="font-size: 0.95rem; letter-spacing: 0.5px;">
            {{ $jftPageDate }}
          </div>
          <h1 id="jftTitle" class="na-heading-display fw-bold mb-2 text-dark" style="font-size: 2.1rem;">
            {{ $jftTitle ?: ($jftReading['title'] ?? '') }}
          </h1>
          <div class="jft-divider mx-auto"></div>
        </div>

        <!-- Opening Basic Text Quote Box -->
        <div id="jftQuoteBox" class="jft-quote-card mb-4 {{ empty($jftQuote) ? 'd-none' : '' }}">
          <div class="d-flex align-items-start gap-3">
            <div class="jft-quote-icon flex-shrink-0" aria-hidden="true">
              <i class="bi bi-quote"></i>
            </div>
            <div class="flex-grow-1">
              <blockquote id="jftQuoteText" class="jft-quote-body mb-2">
                {{ $jftQuote }}
              </blockquote>
              <div class="d-flex justify-content-end">
                <span id="jftQuoteSource" class="jft-quote-source-badge">
                  {{ $jftQuoteSource }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Body Paragraphs (Rich Typography & Ergonomic Line Height) -->
        <div id="jftParagraphsContainer" class="jft-body-paragraphs mb-4">
          @if(!empty($jftParagraphs) && count($jftParagraphs) > 0)
            @foreach($jftParagraphs as $index => $paragraph)
              <p class="jft-paragraph" data-chunk-index="{{ $index }}">{{ $paragraph }}</p>
            @endforeach
          @else
            {!! $jftHtml !!}
          @endif
        </div>

        <!-- Just For Today Affirmation Box ("لليوم فقط: ...") -->
        <div id="jftAffirmationBox" class="jft-affirmation-card {{ empty($jftThought) ? 'd-none' : '' }}">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="jft-affirmation-badge">
              <i class="bi bi-feather text-primary"></i>
              <span>{{ __('messages.jft_affirmation_title') ?? 'لليوم فقط' }}</span>
            </span>
          </div>
          <div id="jftAffirmationText" class="jft-affirmation-text">
            {{ $jftThought }}
          </div>
        </div>
      </div>

      <!-- Footer Micro Literature Link -->
      <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 text-muted small d-print-none">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-book text-primary fs-5"></i>
          <span>{{ __('messages.jft_quote_footer') ?? 'لليوم فقط: أفكاري ستكون مركزة على تعافيّ، وحياتي بدون مخدرات.' }}</span>
        </div>
        <a href="{{ route('frontend.literature') }}" class="text-primary fw-semibold text-decoration-none">
          {{ __('messages.browse_all_literature') ?? 'تصفح مكتبة الأدبيات الكاملة' }} &larr;
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ======================================================================
     ONE-TAP SHARE MODAL (مشاركة التأمل)
     ====================================================================== -->
<div class="modal fade" id="jftShareModal" tabindex="-1" aria-labelledby="jftShareModalLabel" aria-hidden="true" dir="rtl">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
    <div class="modal-content jft-share-modal-content">
      <div class="modal-header border-0 pb-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="jft-share-icon-wrap">
            <i class="bi bi-share-fill text-white"></i>
          </div>
          <h5 class="modal-title fw-bold m-0 text-dark" id="jftShareModalLabel">
            {{ __('messages.jft_share_modal_title') ?? 'مشاركة تأمل اليوم' }}
          </h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') ?? 'إغلاق' }}"></button>
      </div>

      <div class="modal-body p-4">
        <!-- Preview Snippet Box -->
        <div class="jft-snippet-preview mb-3 p-3 rounded-3">
          <div class="small text-muted mb-1 fw-bold">معاينة الرسالة:</div>
          <div id="jftSharePreviewText" class="jft-preview-code text-dark small" style="white-space: pre-wrap; max-height: 140px; overflow-y: auto;"></div>
        </div>

        <!-- Social Action Buttons -->
        <div class="d-grid gap-2 mb-3">
          <button type="button" class="btn btn-success d-flex align-items-center justify-content-center gap-2 py-2 fw-bold rounded-pill" onclick="shareJftToWhatsAppDirect()">
            <i class="bi bi-whatsapp fs-5"></i>
            <span>{{ __('messages.share_via_whatsapp') ?? 'مشاركة عبر واتساب' }}</span>
          </button>

          <button type="button" class="btn btn-info text-white d-flex align-items-center justify-content-center gap-2 py-2 fw-bold rounded-pill" style="background-color: #229ED9; border-color: #229ED9;" onclick="shareJftToTelegramDirect()">
            <i class="bi bi-telegram fs-5"></i>
            <span>{{ __('messages.telegram') ?? 'تيليجرام' }}</span>
          </button>
        </div>

        <!-- Copy Actions -->
        <div class="d-flex gap-2">
          <button type="button" id="jftCopyTextBtn" class="btn btn-outline-secondary flex-grow-1 rounded-pill py-2 small fw-bold d-flex align-items-center justify-content-center gap-1" onclick="copyJftShareText()">
            <i class="bi bi-clipboard" id="jftCopyTextIcon"></i>
            <span id="jftCopyTextLabel">{{ __('messages.jft_copy_text') ?? 'نسخ النص' }}</span>
          </button>

          <button type="button" id="jftCopyLinkBtn" class="btn btn-outline-secondary flex-grow-1 rounded-pill py-2 small fw-bold d-flex align-items-center justify-content-center gap-1" onclick="copyJftShareLink()">
            <i class="bi bi-link-45deg fs-5" id="jftCopyLinkIcon"></i>
            <span id="jftCopyLinkLabel">{{ __('messages.jft_copy_link') ?? 'نسخ الرابط' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  // JFT Interactive State
  let currentJftDate = "{{ $jftDate }}";
  const todayDate = "{{ $todayDate }}";
  let jftTitle = @json($jftTitle);
  let jftQuote = @json($jftQuote);
  let jftQuoteSource = @json($jftQuoteSource);
  let jftParagraphs = @json($jftParagraphs);
  let jftThought = @json($jftThought);
  let jftPageDate = @json($jftPageDate);

  // Speech Synthesis State
  let speechUtterance = null;
  let speechChunks = [];
  let currentChunkIdx = 0;
  let isPlayingSpeech = false;
  let isPausedSpeech = false;
  let currentSpeechRate = 1.0;
  let arabicVoice = null;

  // Initialize Speech Voices
  function initSpeechVoices() {
    if (!('speechSynthesis' in window)) {
      const audioBar = document.getElementById('jftAudioBar');
      if (audioBar) audioBar.classList.add('d-none');
      return;
    }
    const voices = window.speechSynthesis.getVoices();
    // Prioritize Arabic voices (e.g., ar-EG, ar-SA, ar)
    arabicVoice = voices.find(v => v.lang.startsWith('ar-EG')) ||
                  voices.find(v => v.lang.startsWith('ar-SA')) ||
                  voices.find(v => v.lang.startsWith('ar')) ||
                  null;
  }

  if ('speechSynthesis' in window) {
    initSpeechVoices();
    window.speechSynthesis.onvoiceschanged = initSpeechVoices;
  }

  // Load Saved Font Size
  try {
    const savedSize = localStorage.getItem('jft_font_size_preference');
    if (savedSize) {
      applyJftFontSize(parseFloat(savedSize));
    }
  } catch (e) {}

  // Check URL query parameter on initial load (e.g. ?jft_date=YYYY-MM-DD)
  window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const paramDate = urlParams.get('jft_date');
    if (paramDate && /^\d{4}-\d{2}-\d{2}$/.test(paramDate) && paramDate !== currentJftDate) {
      loadJftDate(paramDate);
    }
    updateTodayButtonVisibility();
  });

  // Global helper for Date Change via input
  window.handleJftDateSelect = function(val) {
    if (!val) return;
    loadJftDate(val);
  };

  // Navigate Prev / Next Day
  window.navigateJftDay = function(delta) {
    try {
      const curr = new Date(currentJftDate + 'T00:00:00');
      curr.setDate(curr.getDate() + delta);
      const nextY = curr.getFullYear();
      const nextM = String(curr.getMonth() + 1).padStart(2, '0');
      const nextD = String(curr.getDate()).padStart(2, '0');
      loadJftDate(`${nextY}-${nextM}-${nextD}`);
    } catch(e) {
      console.warn('Date navigation error', e);
    }
  };

  // Jump to Today
  window.jumpToTodayJft = function() {
    loadJftDate(todayDate);
  };

  function updateTodayButtonVisibility() {
    const todayBtn = document.getElementById('jftTodayBtn');
    if (!todayBtn) return;
    if (currentJftDate === todayDate) {
      todayBtn.classList.add('d-none');
    } else {
      todayBtn.classList.remove('d-none');
    }
  }

  // Fetch JFT via AJAX endpoint
  function loadJftDate(dateStr) {
    stopJftAudio();
    const overlay = document.getElementById('jftLoadingOverlay');
    if (overlay) overlay.classList.remove('d-none');

    fetch('/api/v1/jft?date=' + encodeURIComponent(dateStr))
      .then(res => res.json())
      .then(json => {
        if (!json || !json.data) return;
        const d = json.data;
        currentJftDate = d.date;
        jftTitle = d.title || '';
        jftQuote = d.quote || '';
        jftQuoteSource = d.quote_source || '';
        jftParagraphs = Array.isArray(d.content) ? d.content : [];
        jftThought = d.thought_for_the_day || '';
        jftPageDate = d.page_date || '';

        // Update DOM elements
        const titleEl = document.getElementById('jftTitle');
        if (titleEl) titleEl.textContent = jftTitle;

        const dateBadge = document.getElementById('jftPageDateBadge');
        if (dateBadge) dateBadge.textContent = jftPageDate;

        const headerDateSub = document.getElementById('jftHeaderDateSubtitle');
        if (headerDateSub) headerDateSub.textContent = jftPageDate;

        const pickerText = document.getElementById('jftPickerText');
        if (pickerText) pickerText.textContent = jftPageDate;

        const dateInput = document.getElementById('jftDateInput');
        if (dateInput) dateInput.value = currentJftDate;

        // Quote Box
        const quoteBox = document.getElementById('jftQuoteBox');
        const quoteText = document.getElementById('jftQuoteText');
        const quoteSource = document.getElementById('jftQuoteSource');
        if (jftQuote) {
          if (quoteBox) quoteBox.classList.remove('d-none');
          if (quoteText) quoteText.textContent = jftQuote;
          if (quoteSource) quoteSource.textContent = jftQuoteSource;
        } else {
          if (quoteBox) quoteBox.classList.add('d-none');
        }

        // Paragraphs
        const pContainer = document.getElementById('jftParagraphsContainer');
        if (pContainer) {
          pContainer.innerHTML = '';
          if (jftParagraphs.length > 0) {
            jftParagraphs.forEach((pText, idx) => {
              const p = document.createElement('p');
              p.className = 'jft-paragraph';
              p.setAttribute('data-chunk-index', idx);
              p.textContent = pText;
              pContainer.appendChild(p);
            });
          } else if (d.content_html) {
            pContainer.innerHTML = d.content_html;
          }
        }

        // Affirmation Box
        const affBox = document.getElementById('jftAffirmationBox');
        const affText = document.getElementById('jftAffirmationText');
        if (jftThought) {
          if (affBox) affBox.classList.remove('d-none');
          if (affText) affText.textContent = jftThought;
        } else {
          if (affBox) affBox.classList.add('d-none');
        }

        updateTodayButtonVisibility();

        // Update URL query string without reloading
        const url = new URL(window.location.href);
        url.searchParams.set('jft_date', currentJftDate);
        window.history.replaceState({}, '', url.toString());
      })
      .catch(err => {
        console.error('Failed to load JFT reading', err);
      })
      .finally(() => {
        if (overlay) overlay.classList.add('d-none');
      });
  }

  // =========================================================================
  // AUDIO NARRATION ENGINE (Web Speech API)
  // =========================================================================
  function buildSpeechChunks() {
    speechChunks = [];
    if (jftTitle) speechChunks.push('عنوان التأمل: ' + jftTitle);
    if (jftQuote) speechChunks.push(jftQuote + (jftQuoteSource ? ' — ' + jftQuoteSource : ''));
    if (jftParagraphs && jftParagraphs.length > 0) {
      jftParagraphs.forEach(p => {
        if (p.trim()) speechChunks.push(p.trim());
      });
    }
    if (jftThought) speechChunks.push(jftThought);
  }

  window.toggleJftAudioPlayback = function() {
    if (!('speechSynthesis' in window)) {
      alert("{{ __('messages.jft_speech_not_supported') ?? 'المتصفح الحالي لا يدعم ميزة تحويل النص إلى صوت.' }}");
      return;
    }

    if (isPlayingSpeech && !isPausedSpeech) {
      // Pause
      window.speechSynthesis.pause();
      isPausedSpeech = true;
      updateAudioUI('paused');
    } else if (isPlayingSpeech && isPausedSpeech) {
      // Resume
      window.speechSynthesis.resume();
      isPausedSpeech = false;
      updateAudioUI('playing');
    } else {
      // Start from beginning
      buildSpeechChunks();
      if (speechChunks.length === 0) return;
      currentChunkIdx = 0;
      isPlayingSpeech = true;
      isPausedSpeech = false;
      playCurrentChunk();
    }
  };

  function playCurrentChunk() {
    if (currentChunkIdx >= speechChunks.length) {
      stopJftAudio();
      return;
    }

    const chunkText = speechChunks[currentChunkIdx];
    speechUtterance = new SpeechSynthesisUtterance(chunkText);
    speechUtterance.rate = currentSpeechRate;
    speechUtterance.lang = 'ar';
    if (arabicVoice) speechUtterance.voice = arabicVoice;

    speechUtterance.onstart = function() {
      updateAudioUI('playing');
      highlightCurrentChunkInDOM(currentChunkIdx);
      updateAudioProgress();
    };

    speechUtterance.onend = function() {
      currentChunkIdx++;
      if (isPlayingSpeech && !isPausedSpeech) {
        playCurrentChunk();
      }
    };

    speechUtterance.onerror = function(e) {
      console.warn('Speech error', e);
      if (e.error !== 'canceled') {
        currentChunkIdx++;
        if (isPlayingSpeech && !isPausedSpeech) {
          playCurrentChunk();
        }
      }
    };

    window.speechSynthesis.speak(speechUtterance);
  }

  window.stopJftAudio = function() {
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
    }
    isPlayingSpeech = false;
    isPausedSpeech = false;
    currentChunkIdx = 0;
    updateAudioUI('stopped');
    clearParagraphHighlights();
  };

  window.cycleJftAudioSpeed = function() {
    if (currentSpeechRate === 1.0) currentSpeechRate = 1.25;
    else if (currentSpeechRate === 1.25) currentSpeechRate = 1.5;
    else currentSpeechRate = 1.0;

    const speedText = document.getElementById('jftAudioSpeedText');
    if (speedText) speedText.textContent = currentSpeechRate.toFixed(1) + 'x';

    // If currently speaking, restart current chunk at new rate
    if (isPlayingSpeech && !isPausedSpeech && speechUtterance) {
      window.speechSynthesis.cancel();
      playCurrentChunk();
    }
  };

  window.handleJftAudioSeek = function(e) {
    if (speechChunks.length === 0) buildSpeechChunks();
    if (speechChunks.length === 0) return;

    const track = e.currentTarget;
    const rect = track.getBoundingClientRect();
    const isRtl = document.documentElement.dir === 'rtl' || document.body.dir === 'rtl';
    const clickX = isRtl ? (rect.right - e.clientX) : (e.clientX - rect.left);
    const ratio = Math.max(0, Math.min(1, clickX / rect.width));

    currentChunkIdx = Math.floor(ratio * speechChunks.length);
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
    }
    isPlayingSpeech = true;
    isPausedSpeech = false;
    playCurrentChunk();
  };

  function updateAudioProgress() {
    if (speechChunks.length === 0) return;
    const pct = Math.round(((currentChunkIdx + 1) / speechChunks.length) * 100);
    const fill = document.getElementById('jftAudioFill');
    const label = document.getElementById('jftAudioProgressPercent');
    if (fill) fill.style.width = pct + '%';
    if (label) label.textContent = pct + '%';
  }

  function highlightCurrentChunkInDOM(chunkIdx) {
    clearParagraphHighlights();
    // Offset for title (0) and quote (1)
    const pIdx = chunkIdx - 2;
    if (pIdx >= 0) {
      const p = document.querySelector(`.jft-paragraph[data-chunk-index="${pIdx}"]`);
      if (p) p.classList.add('jft-speaking-active');
    }
  }

  function clearParagraphHighlights() {
    document.querySelectorAll('.jft-speaking-active').forEach(el => el.classList.remove('jft-speaking-active'));
  }

  function updateAudioUI(state) {
    const playBtn = document.getElementById('jftAudioPlayBtn');
    const playIcon = document.getElementById('jftAudioPlayIcon');
    const playText = document.getElementById('jftAudioPlayText');
    const stopBtn = document.getElementById('jftAudioStopBtn');
    const statusText = document.getElementById('jftAudioStatusText');

    if (!playIcon || !playText) return;

    if (state === 'playing') {
      playIcon.className = 'bi bi-pause-fill';
      playText.textContent = "{{ __('messages.jft_pause') ?? 'إيقاف مؤقت' }}";
      if (stopBtn) stopBtn.classList.remove('d-none');
      if (statusText) statusText.innerHTML = '<span class="text-success"><i class="bi bi-soundwave me-1"></i>جاري القراءة الصوتية...</span>';
    } else if (state === 'paused') {
      playIcon.className = 'bi bi-play-fill';
      playText.textContent = "{{ __('messages.jft_resume') ?? 'استئناف' }}";
      if (stopBtn) stopBtn.classList.remove('d-none');
      if (statusText) statusText.innerHTML = '<span class="text-warning"><i class="bi bi-pause-circle me-1"></i>متوقف مؤقتاً</span>';
    } else {
      // Stopped
      playIcon.className = 'bi bi-play-fill';
      playText.textContent = "{{ __('messages.jft_play') ?? 'استماع' }}";
      if (stopBtn) stopBtn.classList.add('d-none');
      if (statusText) statusText.innerHTML = '<i class="bi bi-volume-up text-primary me-1"></i><span>{{ __("messages.jft_audio_narration") ?? "مشغل القراءة الصوتية" }}</span>';
      const fill = document.getElementById('jftAudioFill');
      const label = document.getElementById('jftAudioProgressPercent');
      if (fill) fill.style.width = '0%';
      if (label) label.textContent = '0%';
    }
  }

  // =========================================================================
  // FONT SIZE CONTROLS (A- / A / A+)
  // =========================================================================
  let currentFontSize = 1.15; // rem
  const minFontSize = 0.95;
  const maxFontSize = 1.45;

  window.adjustJftFontSize = function(delta) {
    currentFontSize = Math.min(maxFontSize, Math.max(minFontSize, currentFontSize + (delta * 0.1)));
    applyJftFontSize(currentFontSize);
  };

  window.resetJftFontSize = function() {
    currentFontSize = 1.15;
    applyJftFontSize(currentFontSize);
  };

  function applyJftFontSize(size) {
    const container = document.getElementById('jftReadingContainer');
    if (container) {
      container.style.setProperty('--jft-font-size', size + 'rem');
    }
    try {
      localStorage.setItem('jft_font_size_preference', size);
    } catch(e) {}
  }

  // =========================================================================
  // ONE-TAP SHARE ACTIONS (Web Share API, Modal, WhatsApp, Telegram, Clipboard)
  // =========================================================================
  function getSharePayload() {
    const shareUrl = window.location.origin + window.location.pathname + '?jft_date=' + currentJftDate;
    const cleanQuote = jftQuote ? `\n"${jftQuote}"\n${jftQuoteSource ? '— ' + jftQuoteSource : ''}\n` : '';
    const cleanAff = jftThought ? `\n✨ ${jftThought}\n` : '';

    const textSnippet = `🌿 *تأمل لليوم فقط — زمالة المدمنين المجهولين (مصر)*\n📅 ${jftPageDate} | ${jftTitle}\n${cleanQuote}${cleanAff}\n🔗 لقراءة التأمل كاملاً من الموقع الرسمي:\n${shareUrl}`;

    return {
      title: `تأمل لليوم فقط — ${jftTitle} (${jftPageDate})`,
      text: textSnippet,
      url: shareUrl
    };
  }

  window.openJftShareModal = function() {
    const payload = getSharePayload();

    // If mobile Web Share API supported, try native share first
    if (navigator.share && /Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
      navigator.share({
        title: payload.title,
        text: payload.text,
        url: payload.url
      }).catch(err => {
        // Fallback to modal if dismissed or error
        if (err.name !== 'AbortError') {
          showShareModalDOM(payload);
        }
      });
    } else {
      showShareModalDOM(payload);
    }
  };

  function showShareModalDOM(payload) {
    const previewEl = document.getElementById('jftSharePreviewText');
    if (previewEl) previewEl.textContent = payload.text;

    const modalEl = document.getElementById('jftShareModal');
    if (modalEl && window.bootstrap) {
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    }
  }

  window.shareJftToWhatsAppDirect = function() {
    const payload = getSharePayload();
    const url = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(payload.text);
    window.open(url, '_blank');
  };

  window.shareJftToTelegramDirect = function() {
    const payload = getSharePayload();
    const url = 'https://t.me/share/url?url=' + encodeURIComponent(payload.url) + '&text=' + encodeURIComponent(payload.text);
    window.open(url, '_blank');
  };

  window.copyJftShareText = function() {
    const payload = getSharePayload();
    navigator.clipboard.writeText(payload.text).then(() => {
      const label = document.getElementById('jftCopyTextLabel');
      const icon = document.getElementById('jftCopyTextIcon');
      if (label && icon) {
        label.textContent = "{{ __('messages.jft_copied') ?? 'تم النسخ!' }}";
        icon.className = 'bi bi-check2 text-success';
        setTimeout(() => {
          label.textContent = "{{ __('messages.jft_copy_text') ?? 'نسخ النص' }}";
          icon.className = 'bi bi-clipboard';
        }, 2500);
      }
    });
  };

  window.copyJftShareLink = function() {
    const payload = getSharePayload();
    navigator.clipboard.writeText(payload.url).then(() => {
      const label = document.getElementById('jftCopyLinkLabel');
      const icon = document.getElementById('jftCopyLinkIcon');
      if (label && icon) {
        label.textContent = "{{ __('messages.jft_copied') ?? 'تم النسخ!' }}";
        icon.className = 'bi bi-check2 text-success';
        setTimeout(() => {
          label.textContent = "{{ __('messages.jft_copy_link') ?? 'نسخ الرابط' }}";
          icon.className = 'bi bi-link-45deg fs-5';
        }, 2500);
      }
    });
  };

})();
</script>
