<style>
/* Sidebar Wrapper Enhancement - Light/Dark Theme aware */
.sidebar-wrapper,
.sidebar-wrapper [data-simplebar],
.sidebar-wrapper .simplebar-content-wrapper {
    background: var(--glass-bg) !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-right: 1px solid var(--glass-border) !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}

.sidebar-wrapper::-webkit-scrollbar,
.sidebar-wrapper *::-webkit-scrollbar,
.sidebar-wrapper .simplebar-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    opacity: 0 !important;
}

[dir="rtl"] .sidebar-wrapper {
    box-shadow: -4px 0 25px rgba(0, 0, 0, 0.05) !important;
    border-right: none !important;
    border-left: 1px solid var(--glass-border) !important;
}

/* Header & Toggle Section */
.sidebar-header-controls {
    padding: 12px 14px;
    border-bottom: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.sidebar-toggle-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.03);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s ease;
}

.sidebar-toggle-btn:hover {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
}

/* Sidebar Links */
.sidebar-wrapper .navigation li a {
    color: var(--text-secondary) !important;
    padding: 7px 12px;
    margin: 2px 8px;
    display: flex;
    align-items: center;
    border-radius: 8px;
    transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    font-weight: 500;
    font-size: 13.5px;
    text-decoration: none;
}

/* Hover State - Soft Sky Blue */
.sidebar-wrapper .navigation li a:hover {
    color: #2563eb !important;
    background: rgba(37, 99, 235, 0.06) !important;
    transform: translateX(3px);
}

[dir="rtl"] .sidebar-wrapper .navigation li a:hover {
    transform: translateX(-3px);
}

/* Active State - Lighter Soft Sky Blue Pill */
.sidebar-wrapper .navigation li.mm-active > a {
    color: #2563eb !important;
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12) !important;
    font-weight: 600;
}

/* Active Parent Glow State - Soft Sky Blue */
.sidebar-wrapper .navigation li.mm-active-parent > a {
    color: #2563eb !important;
    background: #eff6ff !important;
    border-left: 3px solid #2563eb;
    font-weight: 600;
}

[dir="rtl"] .sidebar-wrapper .navigation li.mm-active-parent > a {
    border-left: none;
    border-right: 3px solid #2563eb;
}

/* Parent Icon & Text styling */
.sidebar-wrapper .navigation .parent-icon {
    font-size: 16px;
    line-height: 1;
    margin-right: 10px;
    transition: transform 0.2s ease;
    opacity: 0.85;
    display: flex;
    align-items: center;
    justify-content: center;
}

[dir="rtl"] .sidebar-wrapper .navigation .parent-icon {
    margin-right: 0;
    margin-left: 10px;
}

.sidebar-wrapper .navigation li a:hover .parent-icon {
    transform: scale(1.1);
    opacity: 1;
}

.sidebar-wrapper .navigation li.mm-active > a .parent-icon,
.sidebar-wrapper .navigation li.mm-active-parent > a .parent-icon {
    opacity: 1;
    color: #2563eb !important;
}

/* Submenu dropdown list with Tree Connection Line */
.sidebar-wrapper .navigation ul {
    background: rgba(0, 0, 0, 0.02) !important;
    padding: 4px 0 4px 8px;
    margin: 2px 8px !important;
    list-style: none;
    border-radius: 8px;
    position: relative;
}

[dir="rtl"] .sidebar-wrapper .navigation ul {
    padding: 4px 8px 4px 0;
}

.sidebar-wrapper .navigation ul::before {
    content: "";
    position: absolute;
    top: 8px;
    bottom: 8px;
    left: 14px;
    width: 2px;
    background: rgba(37, 99, 235, 0.25);
    border-radius: 2px;
}

[dir="rtl"] .sidebar-wrapper .navigation ul::before {
    left: auto;
    right: 14px;
}

.sidebar-wrapper .navigation ul li a {
    padding: 5px 12px 5px 24px;
    margin: 1px 4px;
    font-size: 13px;
    opacity: 0.85;
    position: relative;
}

[dir="rtl"] .sidebar-wrapper .navigation ul li a {
    padding: 5px 24px 5px 12px;
}

/* Mobile Back Button styling */
.sidebar-wrapper .nav-toggle-icon {
    border: 1px solid var(--glass-border) !important;
    color: var(--text-primary) !important;
    transition: all 0.2s ease;
}

.sidebar-wrapper .nav-toggle-icon:hover {
    background: rgba(0, 0, 0, 0.04) !important;
    color: #2563eb !important;
}

/* Category Labels Styling */
.sidebar-wrapper .navigation li.menu-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.8px;
    color: var(--text-secondary, #94a3b8) !important;
    opacity: 0.75;
    padding: 14px 16px 4px 16px;
    margin-top: 4px;
}

[dir="rtl"] .sidebar-wrapper .navigation li.menu-label {
    letter-spacing: 0px;
    padding: 14px 16px 4px 16px;
}
</style>

<aside class="sidebar-wrapper" data-simplebar="true">
        
    {{-- Header & Controls --}}
    <div class="sidebar-header-controls d-lg-none">
        <span class="fw-bold small text-uppercase sidebar-title opacity-75" style="letter-spacing: 0.5px;">{{ __('messages.dashboard') ?? 'Menu' }}</span>
        <div class="sidebar-toggle-btn nav-toggle-icon" title="{{ __('messages.back') ?? 'Back' }}">
            <i class="bi bi-x-lg fs-6"></i>
        </div>
    </div>

    <!--navigation-->
    <ul class="navigation mt-1" id="menu">

      @php
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && $currentUser->hasRole('super admin');
        $isCommittees = $currentUser && $currentUser->hasRole('Committees');
        $isRsc = $currentUser && $currentUser->hasRole('rsc');
        $isWorkgroups = $currentUser && $currentUser->hasRole('Workgroups');
        $isGsr = $currentUser && $currentUser->hasRole('gsr');
        $isStoreManager = $currentUser && $currentUser->hasRole('Store Manager');
        $isServiceBody = $currentUser && $currentUser->hasRole('ServiceBody');

        // My Committee & Workgroup Resolution
        $myCommittee = null;
        if ($currentUser && ($isCommittees || $isRsc)) {
            $myCommittee = \App\Models\ServiceCommittee::where('user_id', $currentUser->id)
                ->orWhere('email', $currentUser->email)
                ->first();
            if (!$myCommittee && $isRsc) {
                $myCommittee = \App\Models\ServiceCommittee::find(83) ?: \App\Models\ServiceCommittee::where('email', 'RSC@naegypt.org')->first();
            }
        }

        $myWg = null;
        if ($currentUser && $isWorkgroups && !$isCommittees && !$isSuperAdmin) {
            $myWg = \App\Models\ServiceCommittee::workgroupsOnly()->where('user_id', $currentUser->id)->first();
        }

        $hasMyWorkspace = $myCommittee || $myWg || ($currentUser && !$isStoreManager);

        // Fellowship & Structure Conditions
        $hasFellowshipMeetings = $currentUser && ($isSuperAdmin || $isServiceBody || $isGsr || $isRsc);
        $hasFellowshipStructure = $currentUser && ($isSuperAdmin || $isRsc);
        $hasFellowshipSection = $hasFellowshipMeetings || $hasFellowshipStructure;

        // Reports & Agendas Conditions
        $canSeeAgendas = $currentUser && ($isSuperAdmin || $isServiceBody || $isRsc);
        $canSeeReports = $currentUser && !$isStoreManager;
        $hasReportsAndAgendas = $currentUser && ($canSeeReports || $canSeeAgendas);

        // Literature & Store Conditions
        $canStore = $currentUser && $currentUser->can('manage store');
        $canLitView = $currentUser && $currentUser->can('view lit inventory');
        $canReconcile = $currentUser && ($canLitView || $currentUser->hasRole('Lit User') || $isSuperAdmin);
        $canSlips = $currentUser && ($currentUser->can('view inventory slips') || $currentUser->hasRole('Lit User') || $isStoreManager || $isRsc || $isSuperAdmin);
        $canLedger = $currentUser && ($currentUser->can('view lit ledger') || $currentUser->hasRole('Lit User') || $isStoreManager || $isRsc || $isSuperAdmin);
        $canTreasurer = $currentUser && ($currentUser->hasRole('Treasurer') || $isServiceBody || $isSuperAdmin);
        $canLitRequests = $currentUser && ($isSuperAdmin || $currentUser->hasRole('Lit User'));
        $canCommitteeLit = $currentUser && ($isCommittees || $isSuperAdmin);
        $canArchiveLit = $currentUser && ($isSuperAdmin || $currentUser->hasRole('Lit User') || $currentUser->hasRole('Treasurer') || $isServiceBody || $isGsr);
        $hasLitOrStore = $isGsr || $canStore || $canLitView || $canReconcile || $canSlips || $canLedger || $canTreasurer || $canLitRequests || $canCommitteeLit || $canArchiveLit;

        // Services & Tools Conditions
        $canHelpline = $currentUser && ($currentUser->can('manage helpline') || $isSuperAdmin || $currentUser->hasRole('Phoneline') || in_array($currentUser->email, ['phone@naegypt.org', 'pr@naegypt.org']));
        $canForms = $currentUser && $currentUser->can('manage own forms');
        $canChangeRequests = $currentUser && ($isCommittees || $isServiceBody || $isSuperAdmin);
        $canFacebook = $currentUser && ($isSuperAdmin || $isCommittees);
        $hasServices = $canHelpline || $canForms || $canChangeRequests || $canFacebook;
      @endphp

      {{-- ======================================================== --}}
      {{-- 1. MY WORKSPACE                                          --}}
      {{-- ======================================================== --}}
      @if($hasMyWorkspace)
      <li class="menu-label">{{ __('messages.My Workspace') }}</li>

      @if($myCommittee)
      <li>
        <a href="{{ route('serviceCommittee.show', $myCommittee->id) }}" title="{{ __('messages.My Committee Details') ?? 'My Committee Details' }}">
          <div class="parent-icon"><i class="bi bi-info-circle-fill"></i></div>
          <div class="menu-title">{{ __('messages.My Committee Details') ?? 'My Committee Details' }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('workgroup.index') }}" title="{{ __('messages.My Workgroups') }}">
          <div class="parent-icon"><i class="bi bi-people-fill"></i></div>
          <div class="menu-title">{{ __('messages.My Workgroups') }}</div>
        </a>
      </li>
      @endif

      @if($myWg)
      <li>
        <a href="{{ route('workgroup.show', $myWg->id) }}" title="{{ __('messages.My Workgroup Details') }}">
          <div class="parent-icon"><i class="bi bi-people-fill"></i></div>
          <div class="menu-title">{{ __('messages.My Workgroup Details') }}</div>
        </a>
      </li>
      @endif

      @if($currentUser && !$isStoreManager)
      <li>
        <a href="{{ route('calendar.index') }}" title="{{ __('messages.Yearly Calendar') }}">
          <div class="parent-icon"><i class="bi bi-calendar-check"></i></div>
          <div class="menu-title">{{ __('messages.Yearly Calendar') }}</div>
        </a>
      </li>
      @endif
      @endif

      {{-- ======================================================== --}}
      {{-- 2. FELLOWSHIP & MEETINGS                                 --}}
      {{-- ======================================================== --}}
      @if($hasFellowshipSection)
      <li class="menu-label">{{ __('messages.Meetings & Groups') }}</li>

      {{-- Meetings & Groups Menu --}}
      @if($hasFellowshipMeetings)
      <li>
        <a href="#menuMeetings" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuMeetings" title="{{ __('messages.Meetings & Groups') }}">
          <div class="parent-icon"><i class="bi bi-calendar3-event"></i></div>
          <div class="menu-title">{{ __('messages.Meetings & Groups') }}</div>
        </a>
        <div class="collapse" id="menuMeetings">
          <ul>
            <li><a href="{{ route('group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Groups') }}</a></li>
            @if($isSuperAdmin || $isRsc)
            <li><a href="{{ route('direct-online-group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.legend_online') }} ({{ __('messages.Direct') ?? 'Direct' }})</a></li>
            <li><a href="{{ route('meeting.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Meetings') }}</a></li>
            <li><a href="{{ route('serviceBody.map') }}"><i class="bi bi-geo-alt"></i>{{ __('messages.Service Bodies Map') }}</a></li>
            @endif
          </ul>
        </div>
      </li>
      @endif

      {{-- Fellowship Structure Menu --}}
      @if($hasFellowshipStructure)
      <li>
        <a href="#menuStructure" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuStructure" title="{{ __('messages.Fellowship Structure') }}">
          <div class="parent-icon"><i class="bi bi-diagram-3-fill"></i></div>
          <div class="menu-title">{{ __('messages.Fellowship Structure') }}</div>
        </a>
        <div class="collapse" id="menuStructure">
          <ul>
            <li><a href="{{ route('serviceBody.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Body') }}</a></li>
            <li><a href="{{ route('serviceCommittee.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Committees') }}</a></li>
            <li><a href="{{ route('workgroup.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Workgroups') }}</a></li>
            <li><a href="{{ route('city.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.City') }}</a></li>
            <li><a href="{{ route('neighborhood.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Neighborhood') }}</a></li>
            <li><a href="{{ route('topic.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Topics') }}</a></li>
          </ul>
        </div>
      </li>
      @endif
      @endif

      {{-- ======================================================== --}}
      {{-- 3. REPORTS & AGENDAS                                     --}}
      {{-- ======================================================== --}}
      @if($hasReportsAndAgendas)
      <li class="menu-label">{{ __('messages.Reports & Agendas') }}</li>
      <li>
        <a href="#menuReportsAgendas" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuReportsAgendas" title="{{ __('messages.Reports & Agendas') }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="menu-title">{{ __('messages.Reports & Agendas') }}</div>
        </a>
        <div class="collapse" id="menuReportsAgendas">
          <ul>
            @if($canSeeReports)
            <li><a href="{{ route('committee-reports.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Committee Reports') }}</a></li>
            @endif
            @if($currentUser)
            <li><a href="{{ route('committee-reports.archive') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</a></li>
            @endif

            @if($canSeeAgendas)
              @php
                $agendaTitle = __('messages.Service Body Agendas') ?? 'Service Body Agendas';
                if ($currentUser->hasRole('ServiceBody') && $currentUser->service_body_id) {
                    $sb = \App\Models\ServiceBody::find($currentUser->service_body_id);
                    if ($sb) {
                        if (app()->getLocale() === 'ar') {
                            $agendaTitle = 'أجندات ' . $sb->ar_name;
                        } else {
                            $agendaTitle = 'Agendas of ' . (($sb->en_name) ?: $sb->ar_name);
                        }
                    }
                }
              @endphp
              <li><a href="{{ route('service-body-agendas.index') }}"><i class="bi bi-arrow-right-short"></i>{{ $agendaTitle }}</a></li>
              <li><a href="{{ route('service-body-agendas.archive') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Body Agendas Archive') ?? 'Service Body Agendas Archive' }}</a></li>
              <li><a href="{{ route('groups-agendas.archive') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Agendas Archive') ?? 'Agendas Archive' }}</a></li>
            @endif
          </ul>
        </div>
      </li>
      @endif

      {{-- ======================================================== --}}
      {{-- 4. LITERATURE & STORE                                    --}}
      {{-- ======================================================== --}}
      @if($hasLitOrStore)
      <li class="menu-label">{{ __('messages.Literature & Store') }}</li>

      {{-- Special 1-click Cart visibility for GSRs --}}
      @if($isGsr)
      <li>
        <a href="{{ route('literature-requests.cart') }}" title="{{ __('messages.Literature Request') }}">
          <div class="parent-icon"><i class="bi bi-cart3"></i></div>
          <div class="menu-title">{{ __('messages.Literature Request') }}</div>
        </a>
      </li>
      @endif

      <li>
        <a href="#menuLiteratureStore" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuLiteratureStore" title="{{ __('messages.Literature & Store') }}">
          <div class="parent-icon"><i class="bi bi-box-seam"></i></div>
          <div class="menu-title">{{ __('messages.Literature & Store') }}</div>
        </a>
        <div class="collapse" id="menuLiteratureStore">
          <ul>
            {{-- Store Inventory & Warehouse Management --}}
            @if($canStore)
            <li><a href="{{ route('store.index') }}"><i class="bi bi-arrow-right-short"></i>{{ app()->getLocale() === 'ar' ? 'مخزون المستودع' : 'Store Inventory' }}</a></li>
            <li><a href="{{ route('store.reports') }}"><i class="bi bi-arrow-right-short"></i>{{ app()->getLocale() === 'ar' ? 'تقارير المستودع' : 'Store Reports' }}</a></li>
            <li><a href="{{ route('store.stocktaking.index') }}"><i class="bi bi-arrow-right-short"></i>{{ app()->getLocale() === 'ar' ? 'الجرد الفعلي' : 'Stocktaking' }}</a></li>
            @endif

            {{-- Lit Read-only Inventory --}}
            @if($canLitView && !$canStore)
            <li><a href="{{ route('lit.index') }}"><i class="bi bi-arrow-right-short"></i>{{ app()->getLocale() === 'ar' ? 'مخزون المطبوعات' : 'Lit Inventory' }}</a></li>
            @endif

            {{-- Inventory Slips & Reconciliation --}}
            @if($canSlips)
            <li><a href="{{ route('slips.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.inventory_slips') }}</a></li>
            @endif

            @if($canReconcile)
            <li><a href="{{ route('lit.reconciliation') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.reconciliation_and_return') }}</a></li>
            @endif

            @if($canLedger)
            <li><a href="{{ route('lit.ledger') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.monthly_ledger') }}</a></li>
            @endif

            {{-- Literature Requests / Orders / Dashboards --}}
            @if($canTreasurer)
            <li><a href="{{ route('literature-requests.treasurer') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Treasurer Dashboard') }}</a></li>
            @endif

            @if($canLitRequests)
            <li><a href="{{ route('literature-requests.committee') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Literature Requests') }}</a></li>
            @endif

            @if($canCommitteeLit)
            <li><a href="{{ route('committee-literature.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.committee_literature_requests') ?? 'Committee Literature' }}</a></li>
            @endif

            @if($canArchiveLit)
            <li><a href="{{ route('literature-requests.archive') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.literature_requests_archive') }}</a></li>
            @endif
          </ul>
        </div>
      </li>
      @endif

      {{-- ======================================================== --}}
      {{-- 5. SERVICES & TOOLS                                      --}}
      {{-- ======================================================== --}}
      @if($hasServices)
      <li class="menu-label">{{ __('messages.Services & Tools') }}</li>

      {{-- Helpline Module --}}
      @if($canHelpline)
      <li>
        <a href="#menuHelpline" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuHelpline" title="{{ __('messages.Helpline') ?? 'خطوط المساعدة' }}">
          <div class="parent-icon"><i class="bi bi-telephone-inbound-fill"></i></div>
          <div class="menu-title">{{ __('messages.Helpline') ?? 'خطوط المساعدة' }}</div>
        </a>
        <div class="collapse" id="menuHelpline">
          <ul>
            <li><a href="{{ route('helpline.index') }}"><i class="bi bi-bar-chart-line"></i> {{ __('messages.Calls Report') ?? 'تقارير المكالمات' }}</a></li>
            <li><a href="{{ route('helpline.volunteers') }}"><i class="bi bi-people"></i> {{ __('messages.Volunteers') ?? 'إدارة المتطوعين' }}</a></li>
            <li><a href="{{ route('forms.helpline.show') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> {{ __('messages.Public Form') ?? 'النموذج العام' }}</a></li>
          </ul>
        </div>
      </li>
      @endif

      {{-- Forms Builder --}}
      @if($canForms)
      <li>
        <a href="{{ route('forms.index') }}" title="{{ __('messages.Manage Forms') ?? 'Manage Forms' }}">
          <div class="parent-icon"><i class="bi bi-input-cursor-text"></i></div>
          <div class="menu-title">{{ __('messages.Manage Forms') ?? 'Manage Forms' }}</div>
        </a>
      </li>
      @endif

      {{-- IT Change Requests --}}
      @if($canChangeRequests)
      <li>
        <a href="{{ route('change-requests.index') }}" title="{{ __('messages.IT Change Requests') }}">
          <div class="parent-icon"><i class="bi bi-cpu-fill"></i></div>
          <div class="menu-title">{{ __('messages.IT Change Requests') }}</div>
        </a>
      </li>
      @endif

      {{-- Facebook Targeting --}}
      @if($canFacebook)
      <li>
        <a href="{{ route('facebook-targeting.index') }}" title="{{ app()->getLocale() === 'ar' ? 'استهداف فيسبوك' : 'Facebook Targeting' }}">
          <div class="parent-icon"><i class="bi bi-facebook"></i></div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'استهداف فيسبوك' : 'Facebook Targeting' }}</div>
        </a>
      </li>
      @endif
      @endif

      {{-- ======================================================== --}}
      {{-- 6. SYSTEM ADMINISTRATION                                 --}}
      {{-- ======================================================== --}}
      @can('is-super-admin')
      <li class="menu-label">{{ __('messages.System Administration') }}</li>
      <li>
        <a href="#menuAdmin" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuAdmin" title="{{ __('messages.Admin Settings') }}">
          <div class="parent-icon"><i class="bi bi-gear-fill admin-icon"></i></div>
          <div class="menu-title">{{ __('messages.Admin Settings') }}</div>
        </a>
        <div class="collapse" id="menuAdmin">
          <ul>
            <li><a href="{{ route('users.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Users List') }}</a></li>
            <li><a href="{{ route('permissions.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Permissions') }}</a></li>
            <li><a href="{{ route('roles.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Rules') }}</a></li>
            <li><a href="{{ route('subscribers.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Subscribers') }}</a></li>
            <li><a href="{{ route('transactions.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Logs Details') }}</a></li>
            <li><a href="{{ route('admin.api_usage.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.API & Mobile Analytics') }}</a></li>
            <li><a href="{{ route('admin.apk_requests.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.apk_download_requests') }}</a></li>
          </ul>
        </div>
      </li>
      @endcan

    </ul>
    <!--end navigation-->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var body = document.body;
        var sidebar = document.querySelector(".sidebar-wrapper");
        var pinBtn = document.getElementById("sidebarPinToggle");

        if (pinBtn) {
            pinBtn.addEventListener("click", function(e) {
                e.preventDefault();
                if (typeof window.toggleSidebarMenu === 'function') {
                    window.toggleSidebarMenu();
                }
            });
        }

        // Auto-close mobile drawer on clicking direct navigation links
        if (sidebar) {
            sidebar.querySelectorAll("a[href]").forEach(function(link) {
                var href = link.getAttribute("href");
                if (href && href !== '#' && !href.startsWith('javascript:') && !link.hasAttribute("data-bs-toggle")) {
                    link.addEventListener("click", function() {
                        if (window.innerWidth <= 1025) {
                            if (typeof window.toggleSidebarMenu === 'function') {
                                window.toggleSidebarMenu(false);
                            } else {
                                body.classList.remove("sidebar-open");
                            }
                        }
                    });
                }
            });
        }

        // Single Accordion Behavior for Submenus
        if (sidebar) {
            var collapses = sidebar.querySelectorAll(".collapse");
            collapses.forEach(function(collapseEl) {
                collapseEl.addEventListener("show.bs.collapse", function() {
                    collapses.forEach(function(other) {
                        if (other !== collapseEl && other.classList.contains("show")) {
                            var bsCollapse = (typeof bootstrap !== 'undefined' && bootstrap.Collapse)
                                ? bootstrap.Collapse.getInstance(other)
                                : null;
                            if (bsCollapse) {
                                bsCollapse.hide();
                            } else {
                                other.classList.remove("show");
                            }
                        }
                    });
                });
            });
        }

        // Active Route Parent Highlighting
        var currentUrl = window.location.href.split('#')[0].split('?')[0];
        var menuLinks = sidebar ? sidebar.querySelectorAll("a[href]") : [];
        
        menuLinks.forEach(function(link) {
            var linkUrl = link.href.split('#')[0].split('?')[0];
            if (linkUrl && linkUrl === currentUrl && linkUrl !== 'javascript:;' && linkUrl !== '#') {
                var parentLi = link.closest("li");
                if (parentLi) {
                    parentLi.classList.add("mm-active");
                }
                
                var parentCollapse = link.closest(".collapse");
                if (parentCollapse) {
                    parentCollapse.classList.add("show");
                    var parentDropdownLi = parentCollapse.closest("li");
                    if (parentDropdownLi) {
                        parentDropdownLi.classList.add("mm-active-parent");
                        var parentToggleA = parentDropdownLi.querySelector('[data-bs-toggle="collapse"]');
                        if (parentToggleA) {
                            parentToggleA.setAttribute("aria-expanded", "true");
                        }
                    }
                }
            }
        });
    });
    </script>

  </aside>
  <div class="sidebar-overlay" onclick="typeof window.toggleSidebarMenu === 'function' ? window.toggleSidebarMenu(false) : document.body.classList.remove('sidebar-open')"></div>