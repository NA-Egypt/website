<x-frontend.layout 
    :title="__('messages.clean_time_title')" 
    :description="__('messages.clean_time_meta_desc')">

  @php
    $isArabic = app()->getLocale() === 'ar';
    $pageDir = $isArabic ? 'rtl' : 'ltr';
    $pageAlign = $isArabic ? 'right' : 'left';
  @endphp

  <div class="cleantime-page pb-5" dir="{{ $pageDir }}" style="text-align: {{ $pageAlign }};">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb py-2 px-3 rounded-pill bg-light border small d-inline-flex">
        <li class="breadcrumb-item">
          <a href="{{ route('frontend.home') }}" class="text-decoration-none text-muted">
            <i class="bi bi-house-door-fill me-1"></i> {{ __('messages.Home') }}
          </a>
        </li>
        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
          {{ __('messages.clean_time_title') }}
        </li>
      </ol>
    </nav>

    <!-- Hero Section -->
    <section class="cleantime-hero mb-4">
      <div class="hero-shell p-4 p-md-5 rounded-4 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
          <div class="col-12 col-lg-8">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(50, 85, 127, 0.08); color: #32557f; font-weight: 700; font-size: 0.88rem;">
              <i class="bi bi-stars"></i>
              <span>{{ __('messages.just_for_today') }} • {{ __('messages.miracle_quote_humble') }}</span>
            </div>
            <h1 class="display-6 fw-black text-dark mb-3 lh-sm">
              {{ __('messages.clean_time_title') }} (حاسبة وقت التعافي)
            </h1>
            <p class="lead text-secondary mb-3 lh-base fs-6">
              {{ __('messages.clean_time_subtitle') }}
            </p>
            <div class="d-flex align-items-center gap-3 flex-wrap text-muted small">
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <span>{{ $isArabic ? 'مجهولة الهوية 100%' : '100% Anonymous' }}</span>
              </span>
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-award-fill text-warning fs-5"></i>
                <span>{{ $isArabic ? 'كافة علامات وميداليات الزمالة الرسمية' : 'All official NA keytags' }}</span>
              </span>
              <span class="d-inline-flex align-items-center gap-1">
                <i class="bi bi-share-fill text-primary fs-5"></i>
                <span>{{ $isArabic ? 'مشاركة وتنزيل بطاقة الإنجاز فورياً' : 'Instant badge share & download' }}</span>
              </span>
            </div>
          </div>

          <div class="col-12 col-lg-4 text-center">
            <div class="p-3 rounded-4 bg-white shadow-sm border d-inline-block">
              <img src="{{ asset('assets/images/icons/na-logo.png') }}" alt="NA Emblem" class="img-fluid" style="max-height: 120px; opacity: 0.9;" onerror="this.style.display='none'">
              <div class="fw-bold small text-dark mt-2">{{ __('messages.na_egypt_full_name') ?? 'زمالة المدمنين المجهولين - مصر' }}</div>
              <div class="x-small text-muted">حُريّة • نية طيبة • تعافي مستمر</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Clean-Time Calculator Component -->
    <x-frontend.cleantime-calculator :isStandalone="true" />

    <!-- Educational Section: The Tradition of Keytags & Milestones in NA -->
    <section class="mt-5 pt-3">
      <div class="row g-4">
        <div class="col-12 col-lg-7">
          <div class="p-4 rounded-4 bg-white border shadow-sm h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-journal-bookmark-fill text-primary fs-4"></i>
              <h3 class="h5 fw-bold m-0 text-dark">
                {{ $isArabic ? 'تقليد ميداليات التعافي (Keytags) في الزمالة' : 'The Tradition of NA Recovery Keytags' }}
              </h3>
            </div>
            <p class="text-secondary small lh-base mb-3">
              {{ $isArabic 
                ? 'في زمالة المدمنين المجهولين، نحتفل بكل خطوة على طريق الامتناع المستمر. يبدأ كل عضو بميدالية الترحيب البيضاء التي ترمز إلى "الرغبة في الامتناع"، ثم تتوالى الميداليات الملونة كعلامات فارقة تعزز الأمل وتثبت للقادم الجديد أن البرنامج ينجح يوماً بيوم.'
                : 'In Narcotics Anonymous, we celebrate every milestone of continuous clean time. Every member begins with the white Welcome keytag representing a desire to stop using, followed by colored tags that reinforce hope and prove to the newcomer that the program works one day at a time.' }}
            </p>

            <div class="table-responsive">
              <table class="table table-hover table-sm align-middle small mb-0">
                <thead class="table-light">
                  <tr>
                    <th>{{ $isArabic ? 'الميدالية واللون' : 'Milestone & Color' }}</th>
                    <th>{{ $isArabic ? 'المدة' : 'Clean Time' }}</th>
                    <th>{{ $isArabic ? 'المبدأ الروحي' : 'Spiritual Principle' }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span class="badge bg-light text-dark border me-1">⚪</span> {{ __('messages.keytag_white_title') }}</td>
                    <td>1 {{ __('messages.day_singular') ?? 'يوم' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الرغبة والاستسلام (Surrender)' : 'Desire & Surrender' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-warning text-dark me-1" style="background-color: #EA580C !important; color:#fff !important;">🟠</span> {{ __('messages.keytag_orange_title') }}</td>
                    <td>30 {{ __('messages.day_singular') ?? 'يوم' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الأمل والانفتاح الذهني (Hope)' : 'Hope & Open-mindedness' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-success me-1">🟢</span> {{ __('messages.keytag_green_title') }}</td>
                    <td>60 {{ __('messages.day_singular') ?? 'يوم' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الإيمان والتطبيق (Faith)' : 'Faith & Action' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-danger me-1">🔴</span> {{ __('messages.keytag_red_title') }}</td>
                    <td>90 {{ __('messages.day_singular') ?? 'يوم' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الشجاعة والالتزام (Courage)' : 'Courage & Commitment' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-primary me-1">🔵</span> {{ __('messages.keytag_blue_title') }}</td>
                    <td>6 {{ __('messages.months_short') ?? 'شهور' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'السلام الداخلي والوضوح (Clarity)' : 'Inner Peace & Clarity' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-warning text-dark me-1">🟡</span> {{ __('messages.keytag_yellow_title') }}</td>
                    <td>9 {{ __('messages.months_short') ?? 'شهور' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'النمو الروحي والخدمة (Spiritual Growth)' : 'Spiritual Growth' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-info text-dark me-1" style="background-color: #D1FAE5 !important; border: 1px solid #10B981;">✨</span> {{ __('messages.keytag_glow_title') }}</td>
                    <td>1 {{ __('messages.one_year') ?? 'سنة' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الولادة الجديدة والحرية (Freedom)' : 'Rebirth & Freedom' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-secondary me-1">🔘</span> {{ __('messages.keytag_grey_title') }}</td>
                    <td>18 {{ __('messages.months_short') ?? 'شهراً' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الثبات والاستمرارية (Perseverance)' : 'Perseverance' }}</td>
                  </tr>
                  <tr>
                    <td><span class="badge bg-dark me-1" style="color: #EAB308;">⚫</span> {{ __('messages.keytag_multi_title') }}</td>
                    <td>{{ $isArabic ? 'سنتان فأكثر' : '2+ Years' }}</td>
                    <td class="text-muted">{{ $isArabic ? 'الامتنان والعطاء (Gratitude & Service)' : 'Gratitude & Service' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="p-4 rounded-4 bg-white border shadow-sm h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
                <h3 class="h5 fw-bold m-0 text-dark">
                  {{ $isArabic ? 'سرية الهوية (Anonymity) في التعافي' : 'Anonymity in Recovery' }}
                </h3>
              </div>
              <p class="text-secondary small lh-base mb-3">
                {{ $isArabic 
                  ? 'التقليد الحادي عشر: "تقوم سياستنا في العلاقات العامة على الجذب لا الترويج، وعلينا دائماً أن نحتفظ بسرية هوياتنا الشخصية على مستوى الصحافة والإذاعة والسينما والإنترنت".'
                  : 'Tradition Eleven: "Our public relations policy is based on attraction rather than promotion; we need always maintain personal anonymity at the level of press, radio, and films."' }}
              </p>
              <div class="p-3 rounded-3 bg-light border small text-dark mb-3">
                <div class="fw-bold mb-1"><i class="bi bi-check-circle-fill text-success me-1"></i> {{ $isArabic ? 'بطاقة آمنة للمشاركة العامة' : 'Safe for Public Sharing' }}</div>
                <div class="text-muted x-small">
                  {{ $isArabic 
                    ? 'بطاقة الإنجاز التي تم إنشاؤها عبر هذه الحاسبة لا تحتوي على اسمك أو أي وسيلة تعريفية شخصية، مما يتيح لك مشاركة فرحة التعافي وإلهام الآخرين دون المساس بمبدأ سرية الهوية الروحي.'
                    : 'The milestone card generated by this calculator contains no personal names or identifying info, allowing you to share the joy of recovery while honoring spiritual anonymity.' }}
                </div>
              </div>
            </div>

            <!-- Quick Help / Meeting Action Box -->
            <div class="p-3 rounded-3 text-white text-center" style="background: linear-gradient(135deg, #32557f 0%, #1b2a4a 100%);">
              <div class="fw-bold small mb-1">{{ $isArabic ? 'هل تبحث عن اجتماع أو تحتاج لمساعدة؟' : 'Looking for a meeting or need help?' }}</div>
              <div class="x-small text-white-50 mb-3">{{ $isArabic ? 'اجتماعات الزمالة متاحة حضورياً وأونلاين في كافة المحافظات' : 'NA meetings available in-person and online across all governorates' }}</div>
              <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <a href="{{ route('frontend.home') }}#quick-meetings" class="btn btn-light btn-sm fw-bold px-3 rounded-pill text-dark">
                  <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('messages.Find Meetings') ?? 'دليل الاجتماعات' }}
                </a>
                <a href="tel:01060933888" class="btn btn-outline-light btn-sm fw-bold px-3 rounded-pill">
                  <i class="bi bi-telephone-fill me-1"></i> 01060933888
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <style>
    .cleantime-page {
      min-height: 100vh;
    }

    .hero-shell {
      background:
        radial-gradient(circle at top left, rgba(50, 85, 127, 0.08), transparent 28%),
        linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid rgba(50, 85, 127, 0.15);
    }

    .x-small {
      font-size: 0.78rem;
    }
  </style>
</x-frontend.layout>
