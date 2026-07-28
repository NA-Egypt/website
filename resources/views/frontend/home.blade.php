<x-frontend.layout :title="__('messages.NA')" description="الموقع الرسمي لزمالة المدمنين المجهولين في مصر - ابحث عن التعافي والاجتماعات وأرقام المساعدة.">
  <x-section-head>{{ __('messages.NA') }}</x-section-head>

  <div class="row justify-content-center mb-3">
    <div class="col-12 text-center">
      <a href="https://www.facebook.com/OfficialNAEgyPage" target="_blank" class="social-icon"><x-fab-facebook
          class="mx-2" style="width:32px; height:32px;" /></a>
      <a href="https://www.instagram.com/narcoticsanonymousegy" target="_blank" class="social-icon"><x-fab-instagram
          class="mx-2" style="width:32px; height:32px;" /></a>
      <a href="https://www.tiktok.com/@narcoticsanonymousegypt" target="_blank" class="social-icon"><x-fab-tiktok
          class="mx-2" style="width:32px; height:32px;" /></a>
      <a href="https://wa.me/201060933888" target="_blank" class="social-icon"><x-fab-whatsapp class="mx-2"
          style="width:32px; height:32px;" /></a>
      <a href="mailto:pr@naegypt.org" class="social-icon"><x-fas-envelope class="mx-2"
          style="width:32px; height:32px;" /></a>
    </div>
  </div>

  <style>
    .home-stats-wrap {
      margin: 8px 0 34px;
    }

    .home-stats-shell {
      background:
        radial-gradient(circle at top right, rgba(50, 85, 127, 0.08), transparent 24%),
        linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
      border-radius: 20px;
      padding: 24px 22px;
      color: #1e293b;
      border: 1px solid rgba(50, 85, 127, 0.10);
      box-shadow: 0 14px 36px -28px rgba(50, 85, 127, 0.22);
      overflow: hidden;
      position: relative;
    }

    .home-stats-shell::before {
      content: '';
      position: absolute;
      inset-inline-end: -26px;
      top: -26px;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background-color: rgba(50, 85, 127, 0.06);
      z-index: 0;
    }

    .home-stats-shell::after {
      content: '';
      position: absolute;
      inset-inline-end: -26px;
      top: -26px;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background-image: url('/assets/images/na-symbol.png');
      background-size: 70px;
      background-position: center;
      background-repeat: no-repeat;
      opacity: 0.08;
      z-index: 0;
    }

    .stats-heading {
      position: relative;
      z-index: 1;
      margin-bottom: 20px;
    }

    .stats-heading h3 {
      font-size: clamp(1.35rem, 3vw, 1.9rem);
      font-weight: 800;
      margin-bottom: 6px;
      color: #32557f;
    }

    .stats-heading p {
      margin: 0;
      max-width: 700px;
      color: #64748b;
      line-height: 1.75;
      margin-inline: auto;
      font-size: 0.96rem;
    }

    .stats-grid-home {
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .helpline-box {
      background: rgba(255, 255, 255, 0.75) !important;
      backdrop-filter: blur(12px) saturate(190%) !important;
      -webkit-backdrop-filter: blur(12px) saturate(190%) !important;
      border: 1px solid rgba(255, 255, 255, 0.45) !important;
      border-radius: 20px !important;
      padding: 20px !important;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04) !important;
      position: relative;
      z-index: 1;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
      min-height: 270px !important;
      width: calc(100% + 3px) !important;
      margin-left: -1.5px !important;
    }
    .helpline-box:hover {
      transform: translateY(-5px) scale(1.01) !important;
      box-shadow: 0 20px 40px 0 rgba(50, 85, 127, 0.12), 0 0 0 1px rgba(50, 85, 127, 0.1) !important;
      border-color: rgba(50, 85, 127, 0.2) !important;
    }
    .helpline-box::before {
      display: none !important;
    }
    .helpline-box h4 {
      color: #32557f !important;
      font-weight: 700 !important;
      font-size: 1.15rem !important;
      margin-bottom: 12px !important;
      display: flex !important;
      align-items: center !important;
      gap: 8px !important;
    }
    .calc-box {
      background: rgba(255, 255, 255, 0.75) !important;
      backdrop-filter: blur(12px) saturate(190%) !important;
      -webkit-backdrop-filter: blur(12px) saturate(190%) !important;
      border: 1px solid rgba(255, 255, 255, 0.45) !important;
      border-radius: 20px !important;
      padding: 20px !important;
      margin: 0 0 0 -1.5px !important;
      width: calc(100% + 3px) !important;
      max-width: calc(100% + 3px) !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04) !important;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
      min-height: 270px !important;
    }
    .calc-box:hover {
      transform: translateY(-5px) scale(1.01) !important;
      box-shadow: 0 20px 40px 0 rgba(50, 85, 127, 0.12), 0 0 0 1px rgba(50, 85, 127, 0.1) !important;
      border-color: rgba(50, 85, 127, 0.2) !important;
    }

    .stat-card-home {
      background: #ffffff;
      border: 1px solid rgba(50, 85, 127, 0.10);
      border-radius: 18px;
      padding: 18px 16px;
      min-height: 132px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .stat-card-home:hover {
      transform: translateY(-3px);
      border-color: rgba(50, 85, 127, 0.18);
      box-shadow: 0 16px 30px -26px rgba(50, 85, 127, 0.25);
    }

    .stat-card-home .stat-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 12px;
    }

    .stat-card-home .stat-icon-home {
      width: 46px;
      height: 46px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(50, 85, 127, 0.08);
      color: #32557f;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .stat-card-home .stat-label-home {
      font-size: 0.92rem;
      line-height: 1.6;
      color: #64748b;
      font-weight: 600;
    }

    .stat-card-home .stat-value-home {
      font-size: clamp(1.8rem, 4vw, 2.35rem);
      line-height: 1;
      font-weight: 900;
      letter-spacing: -0.03em;
      color: #0f172a;
    }

    .info-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background: #fff;
      border-radius: 15px;
      overflow: hidden;
    }

    .card-icon-wrapper {
      height: 60px;
      width: 60px;
      background: rgba(0, 105, 143, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
    }

    .carousel-control-prev,
    .carousel-control-next {
      width: 5%;
    }

    .social-icon {
      color: #32557f;
      transition: all 0.3s ease;
    }

    .social-icon:hover,
    .social-icon:active,
    .social-icon:focus {
      filter: drop-shadow(0 0 5px rgba(50, 85, 127, 0.5));
      color: #32557f;
      /* Ensure color stays same */
    }

    .gradient-text {
      background: -webkit-linear-gradient(#eee, #000487);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    /* Ensure icon inherits the gradient if possible, or force it */
    .gradient-icon {
      background: -webkit-linear-gradient(#eee, #000487);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      display: inline-block;
      /* Required for transform/gradient sometimes */
    }

    .card-title {
      line-height: normal !important;
    }

    @media (max-width: 991px) {
      .stats-grid-home {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 767px) {
      .subscription-box-column {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
      }
      .subscription-box-wrapper {
        margin-left: auto !important;
        margin-right: auto !important;
        width: 100% !important;
        max-width: 440px !important;
        text-align: center !important;
        align-items: center !important;
        justify-content: center !important;
      }
      .subscription-box-wrapper > div {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        margin: auto !important;
      }
      .subscription-box-wrapper h4 {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        margin-left: auto !important;
        margin-right: auto !important;
        width: 100% !important;
      }
      .subscription-box-wrapper .subscribe-input {
        text-align: center !important;
      }
      .subscription-box-wrapper form {
        width: 100% !important;
      }
    }

    @media (max-width: 576px) {
      .home-stats-shell {
        padding: 20px 16px;
        border-radius: 18px;
      }

      .stats-grid-home {
        grid-template-columns: 1fr;
      }

      .stat-card-home {
        min-height: unset;
      }
    }
    .splide__pagination {
      bottom: -1.5rem !important;
    }
    .splide__pagination__page {
      background: rgba(50, 85, 127, 0.2) !important;
      transition: transform 0.2s ease, background-color 0.2s ease !important;
    }
    .splide__pagination__page.is-active {
      background: #32557f !important;
      transform: scale(1.4) !important;
    }
    .splide__arrow {
      background: rgba(50, 85, 127, 0.08) !important;
      color: #32557f !important;
      transition: all 0.3s ease !important;
    }
    .splide__arrow:hover {
      background: #32557f !important;
      color: #fff !important;
    }
    .splide__arrow svg {
      fill: currentColor !important;
    }
    #infoCarousel .splide__track {
      mask-image: radial-gradient(ellipse at center, black 70%, transparent 100%);
      -webkit-mask-image: radial-gradient(ellipse at center, black 70%, transparent 100%);
    }
    #infoCarousel .splide__slide {
      display: flex !important;
      height: auto !important;
    }
    #infoCarousel .info-card {
      display: flex !important;
      flex-direction: column !important;
      width: 100% !important;
      height: 100% !important;
    }

    .calc-box, .stat-card-home {
      position: relative !important;
      overflow: hidden !important;
      background: #ffffff !important; /* Pure solid white background! */
      z-index: 1 !important;
    }
    .helpline-box {
      position: relative !important;
      overflow: hidden !important;
      z-index: 1 !important;
    }
    .helpline-box::before, .calc-box::before, .stat-card-home::before {
      content: "" !important;
      position: absolute !important;
      inset: 0 !important;
      background-repeat: no-repeat !important;
      opacity: 0.035 !important; /* Fades the watermark down to 3.5% opacity */
      pointer-events: none !important;
      z-index: -1 !important;
    }
    
    .helpline-box::before {
      background-image: url('/assets/images/na-symbol.png') !important;
      background-size: 140px !important;
      background-position: right -20px bottom -20px !important;
    }
    [dir="rtl"] .helpline-box::before {
      background-position: left -20px bottom -20px !important;
    }

    .calc-box::before {
      background-image: url('/assets/images/na-symbol.png') !important;
      background-size: 140px !important;
      background-position: right -20px bottom -20px !important;
    }
    [dir="rtl"] .calc-box::before {
      background-position: left -20px bottom -20px !important;
    }

    .stat-card-home::before {
      background-image: url('/assets/images/na-watermark.png') !important;
      background-size: 120px !important;
      background-position: right -20px bottom -20px !important;
    }
    [dir="rtl"] .stat-card-home::before {
      background-position: left -20px bottom -20px !important;
    }

    .helpline-box h4 {
      font-size: 1.15rem !important;
      font-weight: 700 !important;
      color: #32557f !important;
      margin-bottom: 12px !important;
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
    }
    .helpline-region {
      font-weight: 600 !important;
      color: #64748b !important;
      margin-bottom: 6px !important;
      font-size: 0.9rem !important;
    }
    .helpline-num-link {
      font-size: 0.95rem !important;
      font-weight: 700 !important;
      color: #32557f !important;
      text-decoration: none !important;
      transition: all 0.3s ease !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      background: rgba(50, 85, 127, 0.05) !important;
      border: 1px solid rgba(50, 85, 127, 0.08) !important;
      padding: 8px 10px !important;
      border-radius: 12px !important;
      width: 100% !important;
    }
    .helpline-num-link:hover {
      color: #ffffff !important;
      background: linear-gradient(135deg, #32557f 0%, #00698f 100%) !important;
      border-color: transparent !important;
      box-shadow: 0 4px 12px rgba(50, 85, 127, 0.2) !important;
      transform: translateY(-2px) !important;
      text-decoration: none !important;
    }
    .whatsapp-float-btn {
      position: absolute !important;
      top: 16px !important;
      right: 16px !important;
      width: 36px !important;
      height: 36px !important;
      background: #25d366 !important;
      color: #ffffff !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3) !important;
      z-index: 10 !important;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
      animation: whatsapp-pulse 2s infinite !important;
    }
    [dir="rtl"] .whatsapp-float-btn {
      right: auto !important;
      left: 16px !important;
    }
    .whatsapp-float-btn:hover {
      transform: scale(1.15) !important;
      background: #20ba5a !important;
      box-shadow: 0 6px 16px rgba(37, 211, 102, 0.5) !important;
      color: #ffffff !important;
    }
    @keyframes whatsapp-pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
      }
    }
    .timing-badge-bottom {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 6px !important;
      background: rgba(50, 85, 127, 0.04) !important;
      border: 1px solid rgba(50, 85, 127, 0.06) !important;
      padding: 8px 12px !important;
      border-radius: 10px !important;
      font-size: 0.8rem !important;
      color: #64748b !important;
      font-weight: 600 !important;
      margin-top: 10px !important;
    }
    .subscribe-input {
      border: 2px solid rgba(50, 85, 127, 0.12) !important;
      border-radius: 12px !important;
      padding: 12px 16px !important;
      font-size: 0.95rem !important;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
      background: rgba(255, 255, 255, 0.6) !important;
    }
    .subscribe-input:focus {
      outline: none !important;
      border-color: #32557f !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 4px rgba(50, 85, 127, 0.15) !important;
    }
    .subscribe-btn {
      background: linear-gradient(135deg, #32557f 0%, #00698f 100%) !important;
      color: #ffffff !important;
      border: none !important;
      padding: 12px 20px !important;
      border-radius: 12px !important;
      font-weight: 700 !important;
      font-size: 0.95rem !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 8px !important;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
      box-shadow: 0 4px 12px rgba(50, 85, 127, 0.15) !important;
    }
    .subscribe-btn:hover {
      background: linear-gradient(135deg, #00698f 0%, #32557f 100%) !important;
      box-shadow: 0 6px 16px rgba(50, 85, 127, 0.25) !important;
      transform: translateY(-2px) !important;
    }
    .subscribe-btn:active {
      transform: translateY(0) !important;
    }
  </style>

  <div id="infoCarousel" class="splide mt-4 mb-5" aria-label="Information Carousel">
    <div class="splide__track">
      <ul class="splide__list">
        <!-- Slide 1 -->
        <li class="splide__slide">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="card h-100 shadow-sm border-0 info-card p-4">
                <div class="card-body">
                  <img src="{{ asset('assets/images/slide-icon-na.png') }}" alt="NA Logo" class="mb-3"
                    style="width:80px; height:80px; object-fit: contain;">
                  <h4 class="card-title font-weight-bold mb-3 gradient-text">{{ __('messages.whatistheprogram') }}</h3>
                  <p class="card-text text-muted mt-2">{{ __('messages.whatistheprogramtxt') }}</p>
                </div>
              </div>
            </div>
          </div>
        </li>
        <!-- Slide 2 -->
        <li class="splide__slide">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="card h-100 shadow-sm border-0 info-card p-4">
                <div class="card-body d-flex flex-column align-items-center">
                  <!-- Desktop Image -->
                  <img src="{{ asset('assets/images/we-do-recover.png') }}" alt="{{ __('messages.wedorecover') }}" class="img-fluid rounded mb-3 shadow-sm d-none d-md-block"
                    style="max-height: 300px; object-fit: contain; width: auto; max-width: 100%;">
                  <!-- Mobile Image -->
                  <img src="{{ asset('assets/images/we-do-recover-mobile.png') }}" alt="{{ __('messages.wedorecover') }}" class="img-fluid rounded mb-3 shadow-sm d-block d-md-none"
                    style="max-height: 300px; object-fit: contain; width: auto; max-width: 100%;">
                  <p class="card-text text-muted mt-2 text-start w-100">{{ __('messages.wedorecovertxt') }}</p>
                </div>
              </div>
            </div>
          </div>
        </li>
        <!-- Slide 3 -->
        <li class="splide__slide">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <a href="{{ route('frontend.events') }}" class="text-decoration-none text-dark d-block w-100 h-100">
                <div class="card h-100 shadow-sm border-0 info-card p-4">
                  <div class="card-body text-center d-flex flex-column align-items-center">
                    <img src="{{ asset('assets/images/conference-30.jpg') }}" alt="{{ __('messages.convention_title') }}" class="img-fluid rounded mb-3 shadow-sm"
                      style="max-height: 300px; object-fit: contain; width: auto; max-width: 100%;">
                    
                    <h4 class="card-title font-weight-bold mb-2 gradient-text text-center" style="font-size: 1.25rem;">
                      {{ __('messages.convention_title') }}
                      <span class="fs-6 d-block mt-1 font-weight-bold text-muted text-center">{{ __('messages.convention_subtitle') }}</span>
                    </h4>
                    <div class="card-text text-muted w-100" style="font-size: 0.9rem; line-height: 1.6;">
                      <div class="d-flex flex-wrap justify-content-center gap-3 my-2 py-1 bg-light rounded text-center">
                        <span class="mx-3">
                          📅 <strong>{{ __('messages.convention_date') }}</strong> {{ __('messages.convention_date_val') }}
                        </span>
                        <span class="mx-3">
                          📍 <strong>{{ __('messages.convention_location') }}</strong> {{ __('messages.convention_location_val') }}
                        </span>
                        <span class="mx-3 d-block mt-2 px-3 text-start w-100">
                          {{ __('messages.convention_desc') }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="home-stats-shell">
        <div class="row justify-content-center mt-5">
          <div class="col-md-4 helpline-box mb-3 p-4 d-flex flex-column justify-content-between">
            <a href="https://wa.me/201060933888" target="_blank" class="whatsapp-float-btn" title="WhatsApp">
              <x-fab-whatsapp style="width:20px; height:20px;" />
            </a>
            <div>
              <h4><x-fas-headset style="width:16px; height:16px;" />{{ __('messages.helplines') }}</h4>
            </div>
            <div dir="ltr" class="d-flex flex-column gap-2 my-auto py-3">
              <a class="helpline-num-link" href="tel:+201006979198">
                <i class="bi bi-telephone-fill"></i> +201006979198
              </a>
              <a class="helpline-num-link" href="tel:+201060933888">
                <i class="bi bi-telephone-fill"></i> +201060933888
              </a>
            </div>
            <div class="timing-badge-bottom">
              <x-fas-clock style="width:12px; height:12px; fill: currentColor;" />
              <span>{{ __('messages.regionaltiming') }}</span>
            </div>
          </div>

          <div class="col-md-4 helpline-box mb-3 p-4 d-flex flex-column justify-content-between">
            <a href="https://wa.me/201503884411" target="_blank" class="whatsapp-float-btn" title="WhatsApp">
              <x-fab-whatsapp style="width:20px; height:20px;" />
            </a>
            <div>
              <h4><x-fas-headset style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.helpline') }}</h4>
              <div class="helpline-region mt-2">{{ __('messages.alexandria') }}</div>
            </div>
            <div dir="ltr" class="d-flex flex-column gap-2 my-auto py-3">
              <a class="helpline-num-link" href="tel:+201503884411">
                <i class="bi bi-telephone-fill"></i> +201503884411
              </a>
            </div>
            <div class="timing-badge-bottom">
              <x-fas-clock style="width:12px; height:12px; fill: currentColor;" />
              <span>{{ __('messages.leactiming') }}</span>
            </div>
          </div>

          <div class="col-md-4 helpline-box mb-3 p-4 d-flex flex-column justify-content-between">
            <a href="https://wa.me/201003694690" target="_blank" class="whatsapp-float-btn" title="WhatsApp">
              <x-fab-whatsapp style="width:20px; height:20px;" />
            </a>
            <div>
              <h4><x-fas-headset style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.helpline') }}</h4>
              <div class="helpline-region mt-2">{{ __('messages.westgiza') }}</div>
            </div>
            <div dir="ltr" class="d-flex flex-column gap-2 my-auto py-3">
              <a class="helpline-num-link" href="tel:+201003694690">
                <i class="bi bi-telephone-fill"></i> +201003694690
              </a>
            </div>
            <div class="timing-badge-bottom">
              <x-fas-clock style="width:12px; height:12px; fill: currentColor;" />
              <span>{{ __('messages.ahramtiming') }}</span>
            </div>
          </div>
        </div>
        <div class="row justify-content-center align-items-stretch mt-3 g-3">
          <div class="col-md-4 mb-3 d-flex subscription-box-column">
            <div class="helpline-box subscription-box-wrapper w-100 p-4 d-flex flex-column justify-content-between">
              <div class="w-100 text-center">
                <div class="d-inline-flex align-items-center justify-content-center gap-2 px-3 py-2 rounded-pill mb-2 shadow-sm" style="background: rgba(50, 85, 127, 0.08); border: 1px solid rgba(50, 85, 127, 0.15);">
                  <i class="bi bi-envelope-paper-fill" style="color: #32557f; font-size: 1.15rem;"></i>
                  <span class="font-weight-bold" style="color: #32557f !important; font-weight: 700; font-size: 1.05rem;">{{ __('messages.Subscribe') }}</span>
                </div>
                <p class="text-muted small mb-3" style="font-size: 0.82rem; font-weight: 600;">
                  {{ app()->getLocale() === 'ar' ? 'احصل على جديد الأخبار والنشرات' : 'Get our latest updates and newsletter' }}
                </p>
                @if (session('subscribed'))
                  <div class="alert alert-success p-2 rounded-3 text-center small mb-3">
                    {{ __('messages.' . session('subscribed')) }}
                  </div>
                @endif
                <form action="{{ route('subscribers.store') }}" method="post" class="w-100">
                  @csrf
                  <div class="form-group mb-0">
                    <div class="position-relative mb-3">
                      <input type="email" name="email" class="form-control subscribe-input"
                        placeholder="{{ __('messages.Enter your email') }}" required>
                    </div>
                    <button class="subscribe-btn w-100" style="font-weight: 700;" type="submit">
                      <span>{{ __('messages.Subscribe') }}</span>&nbsp;<i class="bi bi-send-fill ms-1"></i>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="col-md-8 mb-3 d-flex">
            <div class="calc-box w-100 p-4 d-flex flex-column justify-content-between">
            <div class="form-group mb-2 text-center">
              <div class="d-inline-flex align-items-center justify-content-center gap-2 px-3 py-2 rounded-pill mb-2 shadow-sm" style="background: rgba(50, 85, 127, 0.08); border: 1px solid rgba(50, 85, 127, 0.15);">
                <i class="bi bi-calendar-check-fill" style="color: #32557f; font-size: 1.15rem;"></i>
                <span class="font-weight-bold" style="color: #32557f !important; font-weight: 700; font-size: 1.05rem;">{{ __('messages.calculator') }}</span>
              </div>
              <div id="cleantime-picker-container" class="mb-3 p-1 rounded-3 shadow-sm" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(8px); border: 1px solid rgba(50, 85, 127, 0.18); transition: all 0.3s ease;">
                <x-forms.datetime-picker id="cleantime_date_input" name="cleantime_date" type="date" placeholder="{{ __('messages.calculator') }}" />
              </div>
            </div>
            <form name="myForm" class="w-100">
              <!-- Hidden inputs to maintain form compatibility -->
              <input type="hidden" name="Fyears" value="0">
              <input type="hidden" name="Fmonth" value="0">
              <input type="hidden" name="FR" value="0">
              
              <!-- Beautiful Modern Stats Display -->
              <div class="row g-2 justify-content-center text-center mt-2">
                <div class="col-4">
                  <div class="p-3 rounded-4 bg-light border border-light-subtle shadow-sm" style="transition: transform 0.2s ease;">
                    <div id="years-result" class="fs-2 font-weight-bold text-dark" style="font-family: monospace; font-weight: 800; line-height: 1; color: #32557f !important;">0</div>
                    <div class="text-muted mt-1" style="font-size: 0.8rem; font-weight: 600;">{{ __('messages.years') }}</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="p-3 rounded-4 bg-light border border-light-subtle shadow-sm" style="transition: transform 0.2s ease;">
                    <div id="months-result" class="fs-2 font-weight-bold text-dark" style="font-family: monospace; font-weight: 800; line-height: 1; color: #32557f !important;">0</div>
                    <div class="text-muted mt-1" style="font-size: 0.8rem; font-weight: 600;">{{ __('messages.months') }}</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="p-3 rounded-4 bg-light border border-light-subtle shadow-sm" style="transition: transform 0.2s ease;">
                    <div id="days-result" class="fs-2 font-weight-bold text-dark" style="font-family: monospace; font-weight: 800; line-height: 1; color: #32557f !important;">0</div>
                    <div class="text-muted mt-1" style="font-size: 0.8rem; font-weight: 600;">{{ __('messages.days') }}</div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <div class="home-stats-wrap">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="home-stats-shell">
          <div class="stats-heading text-center">
            <h3>{{ __('messages.recovery_network') }}</h3>
            <p>{{ __('messages.recovery_network_desc') }}</p>
          </div>

          <div class="row justify-content-center mt-3">
            <div class="col-md-8 col-lg-7">
              <div data-vue-app="AnimatedStatCard"
                   data-weekly-meetings="{{ $homeStats['weekly_meetings'] }}"
                   data-groups-count="{{ $homeStats['groups'] }}"
                   data-weekly-meetings-label="{{ __('messages.weekly_meetings') }}"
                   data-groups-count-label="{{ __('messages.groups_count') }}">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if(!empty($jftContent))
  <div class="home-stats-wrap mb-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="home-stats-shell position-relative">
          <button type="button" class="whatsapp-float-btn border-0" onclick="shareJftToWhatsapp()" title="مشاركة عبر واتساب" style="cursor: pointer; right: 16px !important; left: auto !important;">
            <x-fab-whatsapp style="width:20px; height:20px;" />
          </button>
          <div class="jft-content text-right" dir="rtl" style="position: relative; z-index: 1;">
            {!! $jftContent !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Convention Popup Modal -->
  <div class="modal fade" id="conventionModal" tabindex="-1" aria-labelledby="conventionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
      <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 28px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #f8fafc;">
        <div class="modal-header border-0 pb-0 pt-3 px-4 position-relative" style="z-index: 10; min-height: 56px;">
          <div class="d-flex align-items-center gap-2" style="position: absolute; top: 16px; right: 16px; left: auto !important; z-index: 30; direction: rtl;">
            <button type="button" class="btn-close btn-close-white shadow-none opacity-75 hover-opacity-100" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(255, 255, 255, 0.15); padding: 0.65rem; border-radius: 50%;"></button>
            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: linear-gradient(90deg, #0284c7, #0d9488); color: #fff; font-size: 0.8rem; letter-spacing: 0.5px;">
              ✨ المؤتمر السنوي 30
            </span>
          </div>
        </div>
        <div class="modal-body p-4 pt-4">
          <div class="row align-items-center g-4">
            <!-- Left Poster Column -->
            <div class="col-lg-5 text-center">
              <div class="position-relative overflow-hidden rounded-4 shadow-lg group-hover-zoom" style="border: 1px solid rgba(255, 255, 255, 0.12); background: rgba(0,0,0,0.2);">
                <img src="{{ asset('assets/images/conference-30.jpg') }}" alt="المؤتمر السنوي الثلاثون - مسار يجمعنا" class="img-fluid w-100 h-auto rounded-4 transition-transform duration-500 hover-scale"
                  style="max-height: 380px; object-fit: cover;">
              </div>
            </div>

            <!-- Right Content Column -->
            <div class="col-lg-7 text-end" dir="rtl">
              <h3 class="fw-extrabold mb-1" style="color: #ffffff; font-size: 1.35rem; line-height: 1.4;">
                المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين
              </h3>
              <div class="mb-3">
                <span class="badge px-3 py-1.5 rounded-pill fw-bold text-white mt-1" style="background: rgba(14, 165, 233, 0.2); border: 1px solid rgba(56, 189, 248, 0.4); color: #38bdf8 !important;">
                  مسار يجمعنا • A PATH THAT UNITES US
                </span>
              </div>

              <!-- Live Countdown Timer -->
              <div class="mb-3 p-3 rounded-4" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="text-center small text-uppercase text-slate-400 mb-2 fw-semibold" style="letter-spacing: 1px; color: #94a3b8; font-size: 0.75rem;">
                  ⏳ المتبقي على انطلاق المؤتمر
                </div>
                <div class="d-flex justify-content-center align-items-center gap-2 dir-ltr" id="conventionCountdown">
                  <div class="text-center bg-slate-800 px-2 py-1 rounded-3 min-w-50" style="background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fw-bold text-cyan-400 fs-5" id="cd-days" style="color: #38bdf8;">00</div>
                    <div class="text-muted" style="font-size: 0.65rem; color: #94a3b8 !important;">أيام</div>
                  </div>
                  <span class="fw-bold text-slate-500">:</span>
                  <div class="text-center bg-slate-800 px-2 py-1 rounded-3 min-w-50" style="background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fw-bold text-cyan-400 fs-5" id="cd-hours" style="color: #38bdf8;">00</div>
                    <div class="text-muted" style="font-size: 0.65rem; color: #94a3b8 !important;">ساعات</div>
                  </div>
                  <span class="fw-bold text-slate-500">:</span>
                  <div class="text-center bg-slate-800 px-2 py-1 rounded-3 min-w-50" style="background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fw-bold text-cyan-400 fs-5" id="cd-minutes" style="color: #38bdf8;">00</div>
                    <div class="text-muted" style="font-size: 0.65rem; color: #94a3b8 !important;">دقائق</div>
                  </div>
                  <span class="fw-bold text-slate-500">:</span>
                  <div class="text-center bg-slate-800 px-2 py-1 rounded-3 min-w-50" style="background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="fw-bold text-cyan-400 fs-5" id="cd-seconds" style="color: #38bdf8;">00</div>
                    <div class="text-muted" style="font-size: 0.65rem; color: #94a3b8 !important;">ثواني</div>
                  </div>
                </div>
              </div>

              <!-- Event Details Info -->
              <div class="d-flex flex-column gap-2 mb-3 fs-6" style="color: #cbd5e1; font-size: 0.88rem;">
                <div class="d-flex align-items-center gap-2">
                  <span class="fs-5">📅</span>
                  <span><strong>التاريخ:</strong> 9 - 10 أكتوبر 2026 (الجمعة والسبت)</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="fs-5">📍</span>
                  <span><strong>المكان:</strong> الجامعة الأمريكية بالقاهرة - ميدان التحرير</span>
                </div>
              </div>

              <!-- Buttons Row -->
              <div class="d-flex flex-wrap gap-2 pt-2 justify-content-end align-items-center">
                <button type="button" class="btn px-4 py-2 fw-bold rounded-3 d-inline-flex align-items-center gap-2 shadow-sm transition-all" 
                  onclick="shareConventionModal()"
                  style="background: #25D366; color: #ffffff; border: none; font-size: 0.9rem;">
                  <x-fab-whatsapp style="width: 18px; height: 18px; fill: currentColor;" />
                  مشاركة عبر واتساب
                </button>

                <button type="button" class="btn btn-outline-light px-4 py-2 fw-semibold rounded-3" data-bs-dismiss="modal" style="font-size: 0.9rem; border-color: rgba(255,255,255,0.2);">
                  إغلاق
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let selectedDate = null;

    function setDate(input) {
      if (!input || !input.value) return;
      selectedDate = new Date(input.value);
    }

    document.addEventListener("DOMContentLoaded", () => {
      const pickerEl = document.querySelector('[data-id="cleantime_date_input"]');
      if (pickerEl) {
        pickerEl.addEventListener('picker-change', (e) => {
          const val = e.detail;
          if (val) {
            selectedDate = new Date(val);
            findTime(document.myForm);
          }
        });
      }
    });

    function findTime(form) {
      if (!selectedDate || isNaN(selectedDate.getTime())) {
        return;
      }

      const today = new Date();

      let totalYears = today.getFullYear() - selectedDate.getFullYear();
      let totalMonths = today.getMonth() - selectedDate.getMonth();
      let totalDays = today.getDate() - selectedDate.getDate();

      if (totalDays < 0) {
        totalMonths--;
        const previousMonth = new Date(today.getFullYear(), today.getMonth(), 0);
        totalDays += previousMonth.getDate();
      }

      if (totalMonths < 0) {
        totalYears--;
        totalMonths += 12;
      }

      form.Fyears.value = totalYears;
      form.Fmonth.value = totalMonths;
      form.FR.value = totalDays;

      // Update modern styled results display
      document.getElementById('years-result').textContent = totalYears;
      document.getElementById('months-result').textContent = totalMonths;
      document.getElementById('days-result').textContent = totalDays;

      console.log(`Difference: ${totalYears} years / ${totalMonths} months / ${totalDays} days`);
    }

    function shareJftToWhatsapp() {
      const container = document.querySelector('.jft-content');
      if (!container) return;

      const fullText = (container.innerText || container.textContent || '').trim();
      const message = fullText + '\n\n' + 'من موقع زمالة المدمنين المجهولين - مصر:\n' + window.location.href;
      
      const whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(message);
      window.open(whatsappUrl, '_blank');
    }

    function shareConventionModal() {
      const title = 'المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين مصر 2026 (مسار يجمعنا)';
      const details = '📅 9-10 أكتوبر 2026 | 📍 الجامعة الأمريكية بالقاهرة - ميدان التحرير';
      const url = 'https://egypt30convention.org';
      const message = `${title}\n${details}\n\nللمزيد من التفاصيل والمعلومات:\n${url}`;
      
      const whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(message);
      window.open(whatsappUrl, '_blank');
    }

    function initConventionCountdown() {
      const targetDate = new Date('2026-10-09T09:00:00+02:00').getTime();

      function updateTimer() {
        const now = new Date().getTime();
        const difference = targetDate - now;

        if (difference <= 0) {
          return;
        }

        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        const elDays = document.getElementById('cd-days');
        const elHours = document.getElementById('cd-hours');
        const elMinutes = document.getElementById('cd-minutes');
        const elSeconds = document.getElementById('cd-seconds');

        if (elDays) elDays.textContent = String(days).padStart(2, '0');
        if (elHours) elHours.textContent = String(hours).padStart(2, '0');
        if (elMinutes) elMinutes.textContent = String(minutes).padStart(2, '0');
        if (elSeconds) elSeconds.textContent = String(seconds).padStart(2, '0');
      }

      updateTimer();
      setInterval(updateTimer, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
      initConventionCountdown();

      if (!sessionStorage.getItem('convention_popup_shown')) {
        const conventionModalEl = document.getElementById('conventionModal');
        if (conventionModalEl && window.bootstrap) {
          const myModal = new window.bootstrap.Modal(conventionModalEl);
          myModal.show();
          sessionStorage.setItem('convention_popup_shown', 'true');
        }
      }

      if (window.Splide) {
        const dir = document.documentElement.getAttribute('dir') || 'ltr';
        new window.Splide('#infoCarousel', {
          type: 'loop',
          autoplay: true,
          interval: 15000,
          direction: dir,
          arrows: true,
          pagination: true,
          gap: '1rem',
          autoHeight: true,
        }).mount();
      }
    });
  </script>
</x-frontend.layout>