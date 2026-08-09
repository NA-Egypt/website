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

      {{-- Admin Area --}}
      @can('is-super-admin')
      <li>
        <a href="#menuAdmin" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuAdmin" title="{{ __('messages.Admin Settings') }}">
          <div class="parent-icon"><i class="bi bi-gear-fill admin-icon"></i></div>
          <div class="menu-title">{{ __('messages.Admin Settings')}}</div>
        </a>
        <div class="collapse" id="menuAdmin">
          <ul>
            <li> <a href="{{route('users.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Users List')}}</a></li>
            <li> <a href="{{ route('permissions.index') }}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Permissions')}}</a></li>
            <li> <a href="{{route('roles.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Rules')}}</a></li>
            <li> <a href="{{route('subscribers.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Subscribers')}}</a></li>
          </ul>
        </div>
      </li>
      @endcan
      {{-- / Admin Area --}}

      {{-- Facebook Targeting Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('Committees')))
      <li>
        <a href="{{ route('facebook-targeting.index') }}" title="{{ app()->getLocale() === 'ar' ? 'استهداف فيسبوك' : 'Facebook Targeting' }}">
          <div class="parent-icon"><i class="bi bi-facebook"></i></div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'استهداف فيسبوك' : 'Facebook Targeting' }}</div>
        </a>
      </li>
      @endif

      <li class="menu-divider"><hr class="my-1 opacity-10" style="border-color: var(--glass-border);"></li>

      {{-- Sections Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('gsr') || auth()->user()->hasRole('rsc')))
      <li>
        <a href="#menuSections" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuSections" title="{{ __('messages.Section Details') }}">
          <div class="parent-icon"><i class="bi bi-grid"></i></div>
          <div class="menu-title">{{ __('messages.Section Details')}}</div>
        </a>
        <div class="collapse" id="menuSections">
          <ul>
            @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc'))
            <li><a href="{{ route('serviceBody.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Body')}}</a></li>
            <li><a href="{{ route('serviceCommittee.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Committees')}}</a></li>
            <li><a href="{{ route('city.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.City') }}</a></li>
            <li><a href="{{ route('neighborhood.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Neighborhood') }}</a></li>
            <li><a href="{{ route('topic.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Topics') }}</a></li>
            @endif
            <li><a href="{{ route('group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Groups') }}</a></li>
            @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc'))
            <li><a href="{{ route('direct-online-group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.legend_online') }} ({{ __('messages.Direct') ?? 'Direct' }})</a></li>
            <li><a href="{{ route('meeting.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Meetings') }}</a></li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      {{-- /Sections Area --}}

      {{-- Transactions Area --}}
      @can('is-super-admin')
      <li>
        <a href="{{ route('transactions.index') }}" title="{{ __('messages.Logs Details') }}">
          <div class="parent-icon"><i class="bi bi-receipt-cutoff"></i></div>
          <div class="menu-title">{{ __('messages.Logs Details') }}</div>
        </a>
      </li>
      @endcan
      {{-- /Transactions Area --}}

      <li class="menu-divider"><hr class="my-1 opacity-10" style="border-color: var(--glass-border);"></li>

      {{-- My Committee Details --}}
      @if(auth()->check() && auth()->user()->hasRole('Committees'))
        @php
          $myCommittee = \App\Models\ServiceCommittee::where('user_id', auth()->id())->first();
        @endphp
        @if($myCommittee)
          <li>
            <a href="{{ route('serviceCommittee.show', $myCommittee->id) }}" title="{{ __('messages.My Committee Details') ?? 'My Committee Details' }}">
              <div class="parent-icon"><i class="bi bi-info-circle-fill"></i></div>
              <div class="menu-title">{{ __('messages.My Committee Details') ?? 'My Committee Details' }}</div>
            </a>
          </li>
        @endif
      @endif

      {{-- Reports Area --}}
      @if(auth()->check() && !auth()->user()->hasRole('Store Manager'))
      <li>
        <a href="{{ route('committee-reports.index') }}" title="{{ __('messages.Committee Reports') }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="menu-title">{{ __('messages.Committee Reports') }}</div>
        </a>
      </li>
      @endif
      @if(auth()->check())
      <li>
        <a href="{{ route('committee-reports.archive') }}" title="{{ __('messages.Reports Archive') ?? 'Reports Archive' }}">
          <div class="parent-icon"><i class="bi bi-archive-fill"></i></div>
          <div class="menu-title">{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</div>
        </a>
      </li>
      @endif
      {{-- /Reports Area --}}

      <li class="menu-divider"><hr class="my-1 opacity-10" style="border-color: var(--glass-border);"></li>

      {{-- Agendas Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('rsc')))
      <li>
        <a href="{{ route('groups-agendas.archive') }}" title="{{ __('messages.Agendas Archive') ?? 'Agendas Archive' }}">
          <div class="parent-icon"><i class="bi bi-journals"></i></div>
          <div class="menu-title">{{ __('messages.Agendas Archive') ?? 'Agendas Archive' }}</div>
        </a>
      </li>
      @endif

      {{-- Service Body Agendas Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('rsc')))
      @php
        $user = auth()->user();
        $agendaTitle = __('messages.Service Body Agendas') ?? 'Service Body Agendas';
        if ($user && $user->hasRole('ServiceBody') && $user->service_body_id) {
            $sb = \App\Models\ServiceBody::find($user->service_body_id);
            if ($sb) {
                if (app()->getLocale() === 'ar') {
                    $agendaTitle = 'أجندات ' . $sb->ar_name;
                } else {
                    $agendaTitle = 'Agendas of ' . ($sb->en_name ?: $sb->ar_name);
                }
            }
        }
      @endphp
      <li>
        <a href="{{ route('service-body-agendas.index') }}" title="{{ $agendaTitle }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-medical"></i></div>
          <div class="menu-title">{{ $agendaTitle }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('service-body-agendas.archive') }}" title="{{ __('messages.Service Body Agendas Archive') ?? 'Service Body Agendas Archive' }}">
          <div class="parent-icon"><i class="bi bi-archive-fill"></i></div>
          <div class="menu-title">{{ __('messages.Service Body Agendas Archive') ?? 'Service Body Agendas Archive' }}</div>
        </a>
      </li>
      @endif

      <li class="menu-divider"><hr class="my-1 opacity-10" style="border-color: var(--glass-border);"></li>

      {{-- Calendar Area --}}
      @if(auth()->check() && !auth()->user()->hasRole('Store Manager'))
      <li>
        <a href="{{ route('calendar.index') }}" title="{{ __('messages.Yearly Calendar') }}">
          <div class="parent-icon"><i class="bi bi-calendar-check"></i></div>
          <div class="menu-title">{{ __('messages.Yearly Calendar') }}</div>
        </a>
      </li>
      @endif
      {{-- /Calendar Area --}}

      {{-- IT Change Requests Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('Committees') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('super admin')))
      <li>
        <a href="{{ route('change-requests.index') }}" title="{{ __('messages.IT Change Requests') }}">
          <div class="parent-icon"><i class="bi bi-cpu-fill"></i></div>
          <div class="menu-title">{{ __('messages.IT Change Requests') }}</div>
        </a>
      </li>
      @endif

      {{-- Forms Builder Area --}}
      @if(auth()->check() && auth()->user()->can('manage own forms'))
      <li>
        <a href="{{ route('forms.index') }}" title="{{ __('messages.Manage Forms') ?? 'Manage Forms' }}">
          <div class="parent-icon"><i class="bi bi-input-cursor-text"></i></div>
          <div class="menu-title">{{ __('messages.Manage Forms') ?? 'Manage Forms' }}</div>
        </a>
      </li>
      @endif

      {{-- Store Area --}}
      @if(auth()->check() && auth()->user()->can('manage store'))
      <li>
        <a href="{{ route('store.index') }}" title="{{ app()->getLocale() === 'ar' ? 'مخزون المستودع' : 'Store Inventory' }}">
          <div class="parent-icon"><i class="bi bi-box-seam"></i></div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'مخزون المستودع' : 'Store Inventory' }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('store.reports') }}" title="{{ app()->getLocale() === 'ar' ? 'تقارير المستودع' : 'Store Reports' }}">
          <div class="parent-icon"><i class="bi bi-bar-chart-line"></i></div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'تقارير المستودع' : 'Store Reports' }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('store.stocktaking.index') }}" title="{{ app()->getLocale() === 'ar' ? 'الجرد الفعلي' : 'Stocktaking' }}">
          <div class="parent-icon"><i class="bi bi-clipboard-check"></i></div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'الجرد الفعلي' : 'Stocktaking' }}</div>
        </a>
      </li>
      @endif

      {{-- Lit read-only Area --}}
      @if(auth()->check() && auth()->user()->can('view lit inventory') && !auth()->user()->can('manage store'))
      <li>
        <a href="{{ route('lit.index') }}" title="{{ app()->getLocale() === 'ar' ? 'مخزون المطبوعات' : 'Lit Inventory' }}">
          <div class="parent-icon"><i class="bi bi-book"></i></div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'مخزون المطبوعات' : 'Lit Inventory' }}</div>
        </a>
      </li>
      @endif

      {{-- Literature Requests Area --}}
      @if(auth()->check())
      @if(auth()->user()->hasRole('gsr'))
      <li>
        <a href="{{ route('literature-requests.cart') }}" title="{{ __('messages.Literature Request') }}">
          <div class="parent-icon"><i class="bi bi-cart"></i></div>
          <div class="menu-title">{{ __('messages.Literature Request') }}</div>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasRole('Treasurer') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('super admin'))
      <li>
        <a href="{{ route('literature-requests.treasurer') }}" title="{{ __('messages.Treasurer Dashboard') }}">
          <div class="parent-icon"><i class="bi bi-wallet2"></i></div>
          <div class="menu-title">{{ __('messages.Treasurer Dashboard') }}</div>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('Lit User'))
      <li>
        <a href="{{ route('literature-requests.committee') }}" title="{{ __('messages.Literature Requests') }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
          <div class="menu-title">{{ __('messages.Literature Requests') }}</div>
        </a>
      </li>
      @endif
      @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('Lit User') || auth()->user()->hasRole('Treasurer') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('gsr'))
      <li>
        <a href="{{ route('literature-requests.archive') }}" title="{{ __('messages.literature_requests_archive') }}">
          <div class="parent-icon"><i class="bi bi-archive"></i></div>
          <div class="menu-title">{{ __('messages.literature_requests_archive') }}</div>
        </a>
      </li>
      @endif
      @endif

    </ul>
    <!--end navigation-->

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var body = document.body;
        var sidebar = document.querySelector(".sidebar-wrapper");
        var searchInput = document.getElementById("sidebarSearchInput");
        var searchClear = document.getElementById("sidebarSearchClear");
        var pinBtn = document.getElementById("sidebarPinToggle");

        // 1. Restore Collapsed State from LocalStorage
        var isCollapsed = localStorage.getItem("sidebar_collapsed") === "true";
        if (isCollapsed && window.innerWidth >= 992) {
            body.classList.add("sidebar-collapsed");
            document.documentElement.classList.add("sidebar-collapsed");
        }

        // Toggle Sidebar state (64px vs 240px)
        function toggleSidebarState() {
            body.classList.toggle("sidebar-collapsed");
            document.documentElement.classList.toggle("sidebar-collapsed", body.classList.contains("sidebar-collapsed"));
            var wrapper = document.querySelector(".wrapper");
            if (wrapper) {
                wrapper.classList.toggle("toggled", body.classList.contains("sidebar-collapsed"));
            }
            var collapsed = body.classList.contains("sidebar-collapsed");
            localStorage.setItem("sidebar_collapsed", collapsed ? "true" : "false");
        }

        if (pinBtn) {
            pinBtn.addEventListener("click", function(e) {
                e.preventDefault();
                toggleSidebarState();
            });
        }

        var overlay = document.querySelector(".overlay");
        if (overlay) {
            overlay.addEventListener("click", function() {
                body.classList.remove("sidebar-open");
            });
        }

        // 2. Single Accordion Behavior
        if (sidebar) {
            var collapses = sidebar.querySelectorAll(".collapse");
            collapses.forEach(function(collapseEl) {
                collapseEl.addEventListener("show.bs.collapse", function() {
                    collapses.forEach(function(other) {
                        if (other !== collapseEl && other.classList.contains("show")) {
                            var bsCollapse = bootstrap.Collapse.getInstance(other);
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

        // 3. Active Route Parent Highlighting
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
  <div class="sidebar-overlay" onclick="window.toggleSidebarMenu()"></div>