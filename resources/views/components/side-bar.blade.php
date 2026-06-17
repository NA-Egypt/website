<style>
/* Sidebar Wrapper Enhancement - Light Theme */
.sidebar-wrapper {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-right: 1px solid rgba(0, 0, 0, 0.08) !important;
}

[dir="rtl"] .sidebar-wrapper {
    box-shadow: -4px 0 25px rgba(0, 0, 0, 0.08) !important;
    border-right: none !important;
    border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
}

/* Sidebar Menu Section Headers (Text hidden, acting as dividers) */
.sidebar-wrapper .navigation .menu-label {
    font-size: 0 !important;
    color: transparent !important;
    padding: 0 !important;
    height: 1px;
    border-top: 1px solid rgba(0, 0, 0, 0.08) !important;
    margin: 20px 24px 10px 24px !important;
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
    color: rgba(33, 37, 41, 0.75) !important;
    padding: 12px 24px;
    display: flex;
    align-items: center;
    border-left: 3px solid transparent;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    text-decoration: none;
}

[dir="rtl"] .sidebar-wrapper .navigation li a {
    border-left: none;
    border-right: 3px solid transparent;
    justify-content: flex-start;
}

/* Hover State */
.sidebar-wrapper .navigation li a:hover {
    color: #0d6efd !important;
    background: rgba(13, 110, 253, 0.04);
    padding-left: 30px;
}

[dir="rtl"] .sidebar-wrapper .navigation li a:hover {
    padding-left: 24px;
    padding-right: 30px;
}

/* Active State */
.sidebar-wrapper .navigation li.mm-active > a {
    color: #0d6efd !important;
    background: rgba(13, 110, 253, 0.08);
    border-left-color: #0d6efd !important; /* Blue brand color */
    font-weight: 600;
}

[dir="rtl"] .sidebar-wrapper .navigation li.mm-active > a {
    border-left-color: transparent !important;
    border-right-color: #0d6efd !important;
}

/* Parent Icon & Text styling */
.sidebar-wrapper .navigation .parent-icon {
    font-size: 18px;
    line-height: 1;
    margin-right: 12px;
    transition: transform 0.25s ease;
    opacity: 0.8;
    display: flex;
    align-items: center;
    justify-content: center;
}

[dir="rtl"] .sidebar-wrapper .navigation .parent-icon {
    margin-right: 0;
    margin-left: 12px;
}

.sidebar-wrapper .navigation li a:hover .parent-icon {
    transform: scale(1.12);
    opacity: 1;
}

/* Submenu dropdown list */
.sidebar-wrapper .navigation ul {
    background: rgba(0, 0, 0, 0.03) !important;
    padding: 5px 0;
    list-style: none;
}

.sidebar-wrapper .navigation ul li a {
    padding: 8px 24px 8px 45px;
    font-size: 14px;
    opacity: 0.85;
}

[dir="rtl"] .sidebar-wrapper .navigation ul li a {
    padding: 8px 45px 8px 24px;
}

/* Mobile Back Button styling */
.sidebar-wrapper .nav-toggle-icon {
    border: 1px solid rgba(0, 0, 0, 0.12) !important;
    color: rgba(33, 37, 41, 0.8) !important;
    transition: all 0.2s ease;
}

.sidebar-wrapper .nav-toggle-icon:hover {
    background: rgba(0, 0, 0, 0.05) !important;
    color: #0d6efd !important;
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
      @can('is-super-admin')
      <li class="menu-label">{{ __('messages.Sections')}}</li>
      <li>
        <a href="#menuSections" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuSections">
          <div class="parent-icon"><i class="bi bi-grid"></i>
          </div>
          <div class="menu-title">{{ __('messages.Section Details')}}</div>
        </a>
        <div class="collapse" id="menuSections">
          <ul>
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
            <li> 
              <a href="{{ route('group.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Groups') }}</a>
            </li>
            <li> 
              <a href="{{ route('meeting.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Meetings') }}</a>
            </li>
          </ul>
        </div>
      </li>
      @endcan
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
      <li class="menu-label">{{ __('messages.Service Body Agendas') ?? 'Service Body Agendas' }}</li>
      <li>
        <a href="{{ route('service-body-agendas.index') }}">
          <div class="parent-icon"><i class="bi bi-file-earmark-medical"></i>
          </div>
          <div class="menu-title">{{ __('messages.Service Body Agendas') ?? 'Service Body Agendas' }}</div>
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

    </ul>
    <!--end navigation-->

  </aside>