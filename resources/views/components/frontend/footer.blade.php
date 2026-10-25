<footer class="na-sanctuary-footer" role="contentinfo">
  <div class="container-fluid px-3 px-md-5" style="max-width: 1280px; margin: 0 auto;">
    <div class="row g-4">
      <!-- Col 1: Fellowship Identity & Emblem -->
      <div class="col-12 col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <svg viewBox="0 0 100 100" style="width: 38px; height: 38px; color: var(--na-teal-500);" aria-hidden="true">
            <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="5" />
            <rect x="23" y="23" width="54" height="54" rx="2" fill="none" stroke="currentColor" stroke-width="4.5" transform="rotate(45 50 50)" />
          </svg>
          <div>
            <div class="fw-bold fs-5 text-white lh-sm">{{ __('messages.na_egypt_full_name') ?? 'زمالة المدمنين المجهولين بمصر' }}</div>
            <div class="text-white-50 small">Narcotics Anonymous Egypt</div>
          </div>
        </div>

        <p class="small text-white-50 mb-3" style="line-height: 1.65;">
          {{ __('messages.na_mission_statement_short') ?? 'زمالة عالمية غير هادفة للربح للرجال والنساء الذين أصبحت المخدرات مشكلة رئيسية بالنسبة لهم. نحن مدمنون متعافون نجتمع معاً بانتظام لنساعد بعضنا البعض لنبقى ممتنعين.' }}
        </p>

        <!-- Social Channels -->
        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
          <a href="https://www.facebook.com/OfficialNAEgyPage" target="_blank" rel="noopener noreferrer" aria-label="NA Egypt on Facebook" class="footer-social-icon social-facebook">
            <x-fab-facebook style="width:18px; height:18px;" />
          </a>
          <a href="https://www.instagram.com/narcoticsanonymousegy" target="_blank" rel="noopener noreferrer" aria-label="NA Egypt on Instagram" class="footer-social-icon social-instagram">
            <x-fab-instagram style="width:18px; height:18px;" />
          </a>
          <a href="https://www.tiktok.com/@narcoticsanonymousegypt" target="_blank" rel="noopener noreferrer" aria-label="NA Egypt on TikTok" class="footer-social-icon social-tiktok">
            <x-fab-tiktok style="width:18px; height:18px;" />
          </a>
          <a href="https://wa.me/201060933888" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="footer-social-icon social-whatsapp">
            <x-fab-whatsapp style="width:18px; height:18px;" />
          </a>
          <a href="mailto:pr@naegypt.org" aria-label="Email" class="footer-social-icon social-email">
            <x-fas-envelope style="width:18px; height:18px;" />
          </a>
        </div>
      </div>

      <!-- Col 2: Recovery Literature Links -->
      <div class="col-6 col-md-4 col-lg-2">
        <h4 class="na-footer-heading">{{ __('messages.literature_heading') ?? 'أدبيات التعافي' }}</h4>
        <ul class="na-footer-links">
          <li><a href="{{ route('frontend.literature') }}">{{ __('messages.basic_text') ?? 'النص الأساسي' }}</a></li>
          <li><a href="{{ route('frontend.literature') }}">{{ __('messages.it_works_how_and_why') ?? 'إنه يعمل: كيف ولماذا' }}</a></li>
          <li><a href="{{ route('frontend.literature') }}">{{ __('messages.just_for_today_book') ?? 'لليوم فقط' }}</a></li>
          <li><a href="{{ route('frontend.literature') }}">{{ __('messages.step_working_guides') ?? 'دليل العمل بالخطوات' }}</a></li>
          <li><a href="{{ route('frontend.literature') }}">{{ __('messages.information_pamphlets') ?? 'كتيبات المعلومات (IPs)' }}</a></li>
          <li><a href="{{ route('frontend.literature') }}">{{ __('messages.literature_catalog_online') ?? 'طلب الأدبيات للمجموعات' }}</a></li>
        </ul>
      </div>

      <!-- Col 3: Service Committees & Bodies -->
      <div class="col-6 col-md-4 col-lg-3">
        <h4 class="na-footer-heading">{{ __('messages.service_structure_heading') ?? 'لجان وهيكل الخدمة' }}</h4>
        <ul class="na-footer-links">
          <li><a href="{{ route('frontend.forpublic') }}">{{ __('messages.regional_committee_rsc') ?? 'لجنة الخدمة الإقليمية بمصر (RSC)' }}</a></li>
          <li><a href="{{ route('frontend.forpublic') }}">{{ __('messages.pr_committee') ?? 'لجنة العلاقات العامة (PR)' }}</a></li>
          <li><a href="{{ route('frontend.forpublic') }}">{{ __('messages.hi_committee') ?? 'لجنة المستشفيات والمؤسسات (H&I)' }}</a></li>
          <li><a href="{{ route('frontend.forpublic') }}">{{ __('messages.helpline_committee') ?? 'فريق خطوط الهاتف' }}</a></li>
          <li><a href="{{ route('frontend.meetings', ['type' => 'online']) }}">{{ __('messages.direct_online_groups') ?? 'مجموعات الإنترنت المباشرة (زووم)' }}</a></li>
          <li><a href="{{ route('frontend.events') }}">{{ __('messages.calendar_events_nav') ?? 'تقويم الفعاليات والمؤتمرات' }}</a></li>
        </ul>
      </div>

      <!-- Col 4: Anonymous Helplines & Help -->
      <div class="col-12 col-md-4 col-lg-3">
        <h4 class="na-footer-heading">{{ __('messages.crisis_helplines_heading') ?? 'أرقام المساعدة المباشرة' }}</h4>
        <div class="d-flex flex-column gap-2 small">
          <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <div class="text-white-50">{{ __('messages.helpline_regional_1') ?? 'مصر ١ (القاهرة والمحافظات):' }}</div>
            <a href="tel:+201006979198" class="text-white fw-bold font-monospace fs-6" dir="ltr">+20 100 697 9198</a>
          </div>

          <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <div class="text-white-50">{{ __('messages.helpline_regional_2') ?? 'مصر ٢ (واتساب وهاتف):' }}</div>
            <a href="tel:+201060933888" class="text-white fw-bold font-monospace fs-6" dir="ltr">+20 106 093 3888</a>
          </div>

          <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <div class="text-white-50">{{ __('messages.helpline_alexandria') ?? 'الإسكندرية والساحل:' }}</div>
            <a href="tel:+201503884411" class="text-white fw-bold font-monospace fs-6" dir="ltr">+20 150 388 4411</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Sacred Anonymity Statement (Tradition 12) -->
    <div class="na-anonymity-box text-center">
      <div class="d-inline-flex align-items-center gap-2 fw-bold text-info mb-1">
        <i class="bi bi-shield-check fs-5"></i>
        <span>{{ __('messages.tradition_12_title') ?? 'التقليد الثاني عشر — المجهولية' }}</span>
      </div>
      <p class="mb-0 text-white-50 mx-auto" style="max-width: 860px;">
        {{ __('messages.tradition_12_text') ?? '«المجهولية هي الأساس الروحاني لكل تقاليدنا، فلنتذكر دائمًا وأبدًا أن نُقدم المبادئ على الشخصيات». كل ما يُقال في اجتماعاتنا أو عبر خطوط المساعدة يبقى في طي السرية والكتمان التام.' }}
      </p>
    </div>

    <!-- Bottom Copyright & Policies -->
    <div class="pt-4 mt-3 border-top border-white-10 d-flex flex-wrap align-items-center justify-content-between gap-2 text-white-50 small">
      <div>
        {{ __('messages.copyrights') }} &copy; {{ date('Y') }} {{ __('messages.Egypt') ?? 'Narcotics Anonymous Egypt' }}
        | <a href="{{ route('privacy.policy') }}" class="text-white-50 text-decoration-underline ms-2">{{ __('messages.privacy_policy') ?? 'سياسة الخصوصية' }}</a>
      </div>
      <div class="text-white-50">
        {{ __('messages.fellowship_nonprofit_notice') ?? 'الموقع الالكتروني لزمالة المدمنين المجهولين - مصر' }}
      </div>
    </div>
  </div>
</footer>