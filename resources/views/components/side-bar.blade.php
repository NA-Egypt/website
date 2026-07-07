<style>
/* Sidebar Wrapper Enhancement - Light/Dark Theme aware */
.sidebar-wrapper {
    background: var(--glass-bg) !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-right: 1px solid var(--glass-border) !important;
}

[dir="rtl"] .sidebar-wrapper {
    box-shadow: -4px 0 25px rgba(0, 0, 0, 0.05) !important;
    border-right: none !important;
    border-left: 1px solid var(--glass-border) !important;
}

/* Sidebar Menu Section Headers (Text hidden, acting as dividers) */
.sidebar-wrapper .navigation .menu-label {
    font-size: 0 !important;
    color: transparent !important;
    padding: 0 !important;
    height: 1px;
    border-top: 1px solid var(--glass-border) !important;
    margin: 8px 20px 4px 20px !important;
}

.sidebar-wrapper .navigation li:first-child .menu-label,
.sidebar-wrapper .navigation .menu-label:first-child {
    border-top: none !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    height: 0 !important;
}

/* Sidebar Links */
.sidebar-wrapper .navigation li a {
    color: var(--text-secondary) !important;
    padding: 6px 12px;
    margin: 2px 10px;
    display: flex;
    align-items: center;
    border-radius: 8px;
    transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    font-weight: 500;
    font-size: 14px;
    text-decoration: none;
}

/* Hover State */
.sidebar-wrapper .navigation li a:hover {
    color: #3461ff !important;
    background: rgba(52, 97, 255, 0.06) !important;
    transform: translateX(4px);
}

[dir="rtl"] .sidebar-wrapper .navigation li a:hover {
    transform: translateX(-4px);
}

/* Active State */
.sidebar-wrapper .navigation li.mm-active > a {
    color: #ffffff !important;
    background: linear-gradient(135deg, #3461ff 0%, #0d6efd 100%) !important;
    box-shadow: 0 2px 8px rgba(52, 97, 255, 0.2) !important;
    font-weight: 600;
}

/* Parent Icon & Text styling */
.sidebar-wrapper .navigation .parent-icon {
    font-size: 16px;
    line-height: 1;
    margin-right: 8px;
    transition: transform 0.2s ease;
    opacity: 0.8;
    display: flex;
    align-items: center;
    justify-content: center;
}

[dir="rtl"] .sidebar-wrapper .navigation .parent-icon {
    margin-right: 0;
    margin-left: 8px;
}

.sidebar-wrapper .navigation li a:hover .parent-icon {
    transform: scale(1.08);
    opacity: 1;
}

.sidebar-wrapper .navigation li.mm-active > a .parent-icon {
    opacity: 1;
}

/* Submenu dropdown list */
.sidebar-wrapper .navigation ul {
    background: rgba(0, 0, 0, 0.02) !important;
    padding: 3px 0;
    margin: 0 10px 2px 10px !important;
    list-style: none;
    border-radius: 6px;
}

.sidebar-wrapper .navigation ul li a {
    padding: 5px 12px 5px 28px;
    margin: 1px 0;
    font-size: 13px;
    opacity: 0.85;
}

[dir="rtl"] .sidebar-wrapper .navigation ul li a {
    padding: 5px 28px 5px 12px;
}

/* Mobile Back Button styling */
.sidebar-wrapper .nav-toggle-icon {
    border: 1px solid var(--glass-border) !important;
    color: var(--text-primary) !important;
    transition: all 0.2s ease;
}

.sidebar-wrapper .nav-toggle-icon:hover {
    background: rgba(0, 0, 0, 0.04) !important;
    color: #3461ff !important;
}
</style>

<aside class="sidebar-wrapper" data-simplebar="true">
        
    {{-- Logo --}} 
    {{-- <div class="sidebar-header">
      <div>
        <img src="{{ asset('assets/images/logo.png') }}" class="w-100" alt="logo icon">
      </div>
    </div> --}}
     {{--/ Logo --}}

    {{-- Mobile Back Button --}}
    <div class="sidebar-header d-flex justify-content-center align-items-center d-xl-none p-3 border-bottom mb-2">
        <div class="nav-toggle-icon btn btn-outline-secondary rounded-pill w-100 d-flex justify-content-center align-items-center gap-2" style="cursor: pointer;">
            <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i> 
            {{ __('messages.back') ?? 'Back' }}
        </div>
    </div>

    <!--navigation-->
    <ul class="navigation" id="menu">


      {{-- Admin Area --}}
      @can('is-super-admin')
      <li class="menu-label">{{ __('messages.Admin')}}</li>
      <li>
        <a href="#menuAdmin" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuAdmin">
          <div class="parent-icon"><i class="bi bi-gear-fill admin-icon"></i>
          </div>
          <div class="menu-title">{{ __('messages.Admin Settings')}}</div>
        </a>
        <div class="collapse" id="menuAdmin">
          <ul>
            <li> <a href="{{route('users.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Users List')}}</a>
            </li>
            <li> <a href="{{ route('permissions.index') }}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Permissions')}}</a>
            </li>
            <li> <a href="{{route('roles.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Rules')}}</a>
            </li>
          </ul>
        </div>
      </li>
      @endcan
      {{-- / Admin Area --}}

      {{-- Sections Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('gsr') || auth()->user()->hasRole('rsc')))
      <li class="menu-label">{{ __('messages.Sections')}}</li>
      <li>
        <a href="#menuSections" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuSections">
          <div class="parent-icon"><i class="bi bi-grid"></i>
          </div>
          <div class="menu-title">{{ __('messages.Section Details')}}</div>
        </a>
        <div class="collapse" id="menuSections">
          <ul>
            @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc'))
            <li> 
              <a href="{{ route('serviceBody.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Body')}}</a>
            </li>
            <li>
              <a href="{{ route('serviceCommittee.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Service Committees')}}</a>
            </li>
            <li> 
              <a href="{{ route('city.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.City') }}</a>
            </li>
            <li> 
              <a href="{{ route('neighborhood.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Neighborhood') }}</a>
            </li>
            <li> 
              <a href="{{ route('topic.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Topics') }}</a>
            </li>
            @endif
            <li> 
              <a href="{{ route('group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Groups') }}</a>
            </li>
            @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc'))
            <li> 
              <a href="{{ route('direct-online-group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.legend_online') }} ({{ __('messages.Direct') ?? 'Direct' }})</a>
            </li>
            @endif
            @if(auth()->user()->hasRole('super admin') || auth()->user()->hasRole('rsc'))
            <li> 
              <a href="{{ route('meeting.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Meetings') }}</a>
            </li>
            @endif
          </ul>
        </div>
      </li>
      @endif
      {{-- /Sections Area --}}

      {{-- Transactions Area --}}
      @can('is-super-admin')
      <li class="menu-label">{{ __('messages.Logs')}}</li>
      <li>
        <a href="#menuLogs" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuLogs">
          <div class="parent-icon"><i class="bi bi-receipt-cutoff"></i>
          </div>
          <div class="menu-title">{{ __('messages.Logs Details')  }}</div>
        </a>
        <div class="collapse" id="menuLogs">
          <ul>
            <li> <a href="{{ route('transactions.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Logs')}}</a>
            </li>
          </ul>
        </div>
      </li>
      @endcan
      {{-- /Transactions Area --}}

      {{-- My Committee Details for Committees Role --}}
      @if(auth()->check() && auth()->user()->hasRole('Committees'))
        @php
          $myCommittee = \App\Models\ServiceCommittee::where('user_id', auth()->id())->first();
        @endphp
        @if($myCommittee)
          <li class="menu-label">{{ __('messages.My Committee') ?? 'My Committee' }}</li>
          <li>
            <a href="{{ route('serviceCommittee.show', $myCommittee->id) }}">
              <div class="parent-icon"><i class="bi bi-info-circle-fill"></i>
              </div>
              <div class="menu-title">{{ __('messages.My Committee Details') ?? 'My Committee Details' }}</div>
            </a>
          </li>
        @endif
      @endif

      {{-- Reports Area --}}
      <li class="menu-label">{{ __('messages.Committee Reports') }}</li>
      <li>
        <a href="{{ route('committee-reports.index') }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="menu-title">{{ __('messages.Committee Reports') }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('committee-reports.archive') }}">
          <div class="parent-icon"><i class="bi bi-archive-fill"></i>
          </div>
          <div class="menu-title">{{ __('messages.Reports Archive') ?? 'Reports Archive' }}</div>
        </a>
      </li>
      {{-- /Reports Area --}}

      {{-- Agendas Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('rsc')))
      <li class="menu-label">{{ __('messages.Agendas') }}</li>
      <li>
        <a href="{{ route('groups-agendas.archive') }}">
          <div class="parent-icon"><i class="bi bi-journals"></i>
          </div>
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
      <li class="menu-label">{{ __('messages.Service Body Agendas') ?? 'Service Body Agendas' }}</li>
      <li>
        <a href="{{ route('service-body-agendas.index') }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-medical"></i>
          </div>
          <div class="menu-title">{{ $agendaTitle }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('service-body-agendas.archive') }}">
          <div class="parent-icon"><i class="bi bi-archive-fill"></i>
          </div>
          <div class="menu-title">{{ __('messages.Service Body Agendas Archive') ?? 'Service Body Agendas Archive' }}</div>
        </a>
      </li>
      @endif

      {{-- Calendar Area --}}
      <li class="menu-label">{{ __('messages.Calendar') }}</li>
      <li>
        <a href="{{ route('calendar.index') }}">
          <div class="parent-icon"><i class="bi bi-calendar-check"></i>
          </div>
          <div class="menu-title">{{ __('messages.Yearly Calendar') }}</div>
        </a>
      </li>
      {{-- /Calendar Area --}}

      {{-- IT Change Requests Area --}}
      @if(auth()->check() && (auth()->user()->hasRole('Committees') || auth()->user()->hasRole('ServiceBody') || auth()->user()->hasRole('super admin')))
      <li class="menu-label">{{ __('messages.IT Change Requests') }}</li>
      <li>
        <a href="{{ route('change-requests.index') }}">
          <div class="parent-icon"><i class="bi bi-cpu-fill"></i>
          </div>
          <div class="menu-title">{{ __('messages.IT Change Requests') }}</div>
        </a>
      </li>
      @endif

      {{-- Forms Builder Area --}}
      @if(auth()->check() && auth()->user()->can('manage own forms'))
      <li class="menu-label">{{ __('messages.Form Builder') ?? 'Forms Builder' }}</li>
      <li>
        <a href="{{ route('forms.index') }}">
          <div class="parent-icon"><i class="bi bi-input-cursor-text"></i>
          </div>
          <div class="menu-title">{{ __('messages.Manage Forms') ?? 'Manage Forms' }}</div>
        </a>
      </li>
      @endif

      {{-- Store Area --}}
      @if(auth()->check() && auth()->user()->can('manage store'))
      <li class="menu-label">{{ app()->getLocale() === 'ar' ? 'المخزن والمطبوعات' : 'Store & Lit' }}</li>
      <li>
        <a href="{{ route('store.index') }}">
          <div class="parent-icon"><i class="bi bi-box-seam"></i>
          </div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'مخزون المستودع' : 'Store Inventory' }}</div>
        </a>
      </li>
      <li>
        <a href="{{ route('store.reports') }}">
          <div class="parent-icon"><i class="bi bi-bar-chart-line"></i>
          </div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'تقارير المستودع' : 'Store Reports' }}</div>
        </a>
      </li>
      @endif

      {{-- Lit read-only Area --}}
      @if(auth()->check() && auth()->user()->can('view lit inventory') && !auth()->user()->can('manage store'))
      <li class="menu-label">{{ app()->getLocale() === 'ar' ? 'المطبوعات' : 'Literature' }}</li>
      <li>
        <a href="{{ route('lit.index') }}">
          <div class="parent-icon"><i class="bi bi-book"></i>
          </div>
          <div class="menu-title">{{ app()->getLocale() === 'ar' ? 'مخزون المطبوعات' : 'Lit Inventory' }}</div>
        </a>
      </li>
      @endif

    </ul>
    <!--end navigation-->

  </aside>