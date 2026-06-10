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

    .home-stats-shell::after {
      content: '';
      position: absolute;
      inset-inline-end: -26px;
      top: -26px;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: rgba(50, 85, 127, 0.06);
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
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 14px;
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
  </style>

  <div id="infoCarousel" class="carousel carousel-dark slide mt-4 mb-5" data-bs-ride="carousel">
    <div class="carousel-inner">
      <!-- Slide 1 -->
      <div class="carousel-item active" data-bs-interval="15000">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card h-100 shadow-sm border-0 info-card p-4">
              <div class="card-body text-center">
                <img src="{{ asset('assets/images/slide-icon-na.png') }}" alt="NA Logo" class="mb-3"
                  style="width:80px; height:80px; object-fit: contain;">
                <h3 class="card-title font-weight-bold mb-3 gradient-text">{{ __('messages.whatistheprogram') }}</h3>
                <p class="card-text text-muted">{{ __('messages.whatistheprogramtxt') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Slide 2 -->
      <div class="carousel-item" data-bs-interval="15000">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card h-100 shadow-sm border-0 info-card p-4">
              <div class="card-body text-center">
                <img src="{{ asset('assets/images/slide-icon-recover.png') }}" alt="Recover Logo" class="mb-3"
                  style="width:80px; height:80px; object-fit: contain;">
                <h3 class="card-title font-weight-bold mb-3 gradient-text">{{ __('messages.wedorecover') }}</h3>
                <p class="card-text text-muted">{{ __('messages.wedorecovertxt') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Slide 3 -->
      <div class="carousel-item" data-bs-interval="15000">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <a href="{{ route('frontend.events') }}" class="text-decoration-none text-dark d-block">
              <div class="card h-100 shadow-sm border-0 info-card p-4">
                <div class="card-body text-center d-flex flex-column align-items-center">
                  <img src="{{ asset('assets/images/conference-30.jpg') }}" alt="المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين" class="img-fluid rounded mb-4 shadow-sm"
                    style="max-height: 300px; object-fit: contain; width: auto; max-width: 100%;">
                  
                  <h3 class="card-title font-weight-bold mb-3 gradient-text" dir="rtl">
                    المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين مصر 2026
                    <span class="fs-4 d-block mt-2 font-weight-bold gradient-text text-center">مسار يجمعنا</span>
                  </h3>
                  <div class="card-text text-muted text-end w-100" dir="rtl" style="font-size: 0.95rem; line-height: 1.8;">
                    <div class="d-flex flex-wrap justify-content-center gap-3 my-3 py-2 bg-light rounded text-center">
                      <span class="mx-3">
                        📅 <strong>التاريخ:</strong> 8 - 9 أكتوبر 2026
                      </span>
                      <span class="mx-3">
                        📍 <strong>المكان:</strong> الجامعة الأمريكية بالقاهرة
                      </span>
                      <span class="mx-3">
                      يُعد المؤتمر السنوي فرصة مميزة لاجتماع الأعضاء والأصدقاء في أجواء من التعافي والخدمة والوحدة، من خلال برنامج متنوع يضم الاجتماعات، المشاركات، الأنشطة، والفعاليات التي تعكس رسالة الزمالة وروحها.
                      تستمر الاستعدادات للمؤتمر من خلال فرق الخدمة المختلفة لضمان تقديم حدث يليق بهذه المناسبة المميزة،
                      نتطلع إلى لقائكم جميعًا في المؤتمر السنوي الثلاثون لزمالة المدمنين المجهولين في مصر، في حدث يجمعنا على طريق التعافي والخدمة والوحدة.
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#infoCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#infoCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="home-stats-shell">
        <div class="row justify-content-center mt-5">
          <div class="col-md-4 helpline-box mb-3">
            <h4><x-fas-headset style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.helplines') }}</h4>
            <p dir="ltr"><a href="tel:+201006979198">+201006979198</a><br /><a
                href="tel:+201060933888">+201060933888</a>
              <br />
              <a href="https://wa.me/201060933888" target="_blank"><x-fab-whatsapp
                  style="width:24px; height:24px;" /></a>
              <br />
              <x-fas-clock style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.regionaltiming') }}
              <br />
            </p>
          </div>
          <div class="col-md-4 helpline-box mb-3">
            <h4><x-fas-headset style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.helpline') }}</h4>
            <div>{{ __('messages.alexandria') }}</div>
            <p dir="ltr"><a href="tel:+201503884411">+201503884411</a><br />
              <a href="https://wa.me/201503884411" target="_blank"><x-fab-whatsapp
                  style="width:24px; height:24px;" /></a>
              <br />
              <x-fas-clock style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.leactiming') }}
              <br />
            </p>
          </div>
          <div class="col-md-4 helpline-box mb-3">
            <h4><x-fas-headset style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.helpline') }}</h4>
            <div>{{ __('messages.westgiza') }}</div>
            <p dir="ltr"><a href="tel:+201003694690">+201003694690</a><br />
              <a href="https://wa.me/201003694690" target="_blank"><x-fab-whatsapp
                  style="width:24px; height:24px;" /></a>
              <br />
              <x-fas-clock style="width:16px; height:16px;" />&NonBreakingSpace;{{ __('messages.ahramtiming') }}
              <br />
            </p>
          </div>
        </div>
        <div class="row justify-content-center mt-3">
          <div class="col-md-4 helpline-box mb-3">
            <div class="row px-4 py-1">
              <a class="btn btn-outline-light"
                href="{{ route('frontend.meetings') }}">{{ __('messages.Meetings') }}&nbsp;<x-fas-users
                  style="width:16px; height:16px;" /></a>
            </div>
            <div class="row px-4 py-1 mt-2">
              <a class="btn btn-outline-info"
                href="{{ route('frontend.comms') }}">{{ __('messages.Service Committees') }}&nbsp;<x-fas-users
                  style="width:16px; height:16px;" /></a>
            </div>
          </div>
          <div class="col-md-4 helpline-box mb-3">
            @if (session('subscribed'))
              <div class="alert alert-success p-2">
                {{ __('messages.' . session('subscribed')) }}
              </div>
            @endif
            <form action="{{ route('subscribers.store') }}" method="post">
              @csrf
              <div class="form-group">
                <input type="email" name="email" class="form-control"
                  placeholder="{{ __('messages.Enter your email') }}">
                <br />
                <button class="btn btn-outline-success"
                  type="submit">{{ __('messages.Subscribe') }}&nbsp;<x-fas-envelope
                    style="width:16px; height:16px;" /></button>
              </div>
            </form>
          </div>
          <div class="col-md-4 calc-box mb-3">
            <div class="form-group">
              <h5><label for="date">{{ __('messages.calculator') }}</label></h5>
              <input type="date" class="form-control mb-3" onchange="setDate(this)">
            </div>
            <form name="myForm">
              <div class="form-group" dir="ltr">
                <div class="input-group mb-2">
                  <input type="text" class="form-control" name="Fyears" placeholder="{{ __('messages.years') }}"
                    readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">{{ __('messages.years') }}</span>
                  </div>
                </div>
                <div class="input-group mb-2">
                  <input type="text" class="form-control" name="Fmonth" placeholder="{{ __('messages.months') }}"
                    readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">{{ __('messages.months') }}</span>
                  </div>
                </div>
                <div class="input-group mb-2">
                  <input type="text" class="form-control" name="FR" placeholder="{{ __('messages.days') }}" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">{{ __('messages.days') }}</span>
                  </div>
                </div>
              </div>
              <button type="button" onclick="findTime(document.myForm)"
                class="btn btn-info w-100">{{ __('messages.calculate') }}</button>
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

            <div class="stat-card-home">
              <div class="stat-top">
                <span class="stat-icon-home"><i class="bi bi-map-fill"></i></span>
                <span class="stat-label-home">{{ __('messages.governorates_count') }}</span>
              </div>
              <div class="stat-value-home">{{ number_format($homeStats['governorates']) }}</div>
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
      selectedDate = new Date(input.value);
    }

    function findTime(form) {
      if (!selectedDate) {
        alert("Please select a date first!");
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
    });
  </script>
</x-frontend.layout>