<x-frontend.layout>
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
      background: #ffffff !important;
      border: 1px solid rgba(50, 85, 127, 0.10) !important;
      border-radius: 18px !important;
      padding: 20px !important;
      margin: 0 0 0 -1.5px !important;
      width: calc(100% + 3px) !important;
      max-width: calc(100% + 3px) !important;
      display: block !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.02) !important;
      min-height: 270px !important;
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
                <div class="card-body text-center d-flex flex-column align-items-center">
                  <!-- Desktop Image -->
                  <img src="{{ asset('assets/images/we-do-recover.png') }}" alt="{{ __('messages.wedorecover') }}" class="img-fluid rounded mb-3 shadow-sm d-none d-md-block"
                    style="max-height: 300px; object-fit: contain; width: auto; max-width: 100%;">
                  <!-- Mobile Image -->
                  <img src="{{ asset('assets/images/we-do-recover-mobile.png') }}" alt="{{ __('messages.wedorecover') }}" class="img-fluid rounded mb-3 shadow-sm d-block d-md-none"
                    style="max-height: 300px; object-fit: contain; width: auto; max-width: 100%;">
                  <p class="card-text text-muted mt-2">{{ __('messages.wedorecovertxt') }}</p>
                </div>
              </div>
            </div>
          </div>
        </li>
        <!-- Slide 3 -->
        <li class="splide__slide">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <a href="{{ route('frontend.events') }}" class="text-decoration-none text-dark d-block">
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
                        <span class="mx-3 d-block mt-2 px-3">
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
        <div class="row justify-content-center mt-3">
          <div class="col-md-6 helpline-box mb-3 p-4 d-flex flex-column justify-content-between">
            <div>
              <h4 class="mb-3"><x-fas-envelope style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.Subscribe') }}</h4>
              @if (session('subscribed'))
                <div class="alert alert-success p-2">
                  {{ __('messages.' . session('subscribed')) }}
                </div>
              @endif
              <form action="{{ route('subscribers.store') }}" method="post">
                @csrf
                <div class="form-group mb-0">
                  <input type="email" name="email" class="form-control subscribe-input mb-3"
                    placeholder="{{ __('messages.Enter your email') }}" required>
                  <button class="subscribe-btn w-100" style="font-weight: 600;" type="submit">
                    {{ __('messages.Subscribe') }}&nbsp;<x-fas-envelope style="width:16px; height:16px;" />
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-md-6 calc-box mb-3 d-flex flex-column justify-content-between p-4">
            <div class="form-group mb-0 text-center">
              <h5 class="font-weight-bold mb-3 text-primary d-flex align-items-center justify-content-center gap-2" style="color: #32557f !important; font-weight: 700;">
                <i class="bi bi-calendar-check-fill"></i>&nbsp;{{ __('messages.calculator') }}
              </h5>
              <div id="cleantime-picker-container" class="mb-3">
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
  <div class="home-stats-wrap">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="home-stats-shell">
          <div class="stats-heading text-center">
            <h3>{{ __('messages.recovery_network') }}</h3>
            <p>{{ __('messages.recovery_network_desc') }}</p>
          </div>

          <div class="stats-grid-home">
            <div class="stat-card-home">
              <div class="stat-top">
                <span class="stat-icon-home"><i class="bi bi-calendar-week-fill"></i></span>
                <span class="stat-label-home">{{ __('messages.weekly_meetings') }}</span>
              </div>
              <div class="stat-value-home">{{ number_format($homeStats['weekly_meetings']) }}</div>
            </div>

            <div class="stat-card-home">
              <div class="stat-top">
                <span class="stat-icon-home"><i class="bi bi-people-fill"></i></span>
                <span class="stat-label-home">{{ __('messages.groups_count') }}</span>
              </div>
              <div class="stat-value-home">{{ number_format($homeStats['groups']) }}</div>
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
        <div class="home-stats-shell">
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: linear-gradient(145deg, #ffffff 0%, #f7fbff 100%);">
        <div class="modal-header border-0 bg-transparent pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
          <h5 class="modal-title" id="conventionModalLabel"></h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(50, 85, 127, 0.08); padding: 0.75rem; border-radius: 50%;"></button>
        </div>
        <div class="modal-body px-4 pb-4 pt-2 text-center d-flex flex-column align-items-center">
          <a href="https://egypt30convention.org" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-dark w-100">
            <img src="{{ asset('assets/images/conference-30.jpg') }}" alt="المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين" class="img-fluid rounded mb-4 shadow-sm"
              style="max-height: 320px; object-fit: contain; width: auto; max-width: 100%; transition: transform 0.3s ease;">
            
            <h3 class="card-title font-weight-bold mb-3 gradient-text text-center" dir="rtl" style="font-weight: 800; font-size: 1.3rem; text-align: center;">
              المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين مصر 2026
              <span class="fs-6 d-block mt-2 font-weight-bold gradient-text text-center">مسار يجمعنا</span>
            </h3>
            
            <div class="card-text text-muted text-center w-100" dir="rtl" style="font-size: 0.95rem; line-height: 1.8;">
              <div class="d-flex flex-wrap justify-content-center gap-3 my-3 py-3 bg-light rounded text-center border" style="border-color: rgba(50, 85, 127, 0.08) !important;">
                <span class="mx-3">
                  📅 <strong>التاريخ:</strong> 8 - 9 أكتوبر 2026
                </span>
                <span class="mx-3 border-start ps-3 border-secondary-subtle">
                  📍 <strong>المكان:</strong> الجامعة الأمريكية بالقاهرة
                </span>
              </div>
              <p class="px-2 text-center" style="color: #475569; max-width: 650px; margin: 0 auto 1.5rem;">
                يُعد المؤتمر السنوي فرصة مميزة لاجتماع الأعضاء والأصدقاء في أجواء من التعافي والخدمة والوحدة، من خلال برنامج متنوع يضم الاجتماعات، المشاركات، الأنشطة، والفعاليات التي تعكس رسالة الزمالة وروحها.
              </p>
            </div>
          </a>
          <div class="d-flex gap-2 justify-content-center w-100 mt-2">
            <!--<a href="https://egypt30convention.org" target="_blank" rel="noopener noreferrer" class="btn btn-primary px-4 py-2" style="border-radius: 12px; background: linear-gradient(135deg, #00698f 0%, #32557f 100%); border: none; font-weight: 600; box-shadow: 0 4px 12px rgba(50, 85, 127, 0.2);">
              التفاصيل والتسجيل
            </a>-->
            <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 12px; font-weight: 600;">
              إغلاق
            </button>
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

    document.addEventListener('DOMContentLoaded', function() {
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