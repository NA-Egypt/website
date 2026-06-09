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
      @if(auth()->check() && (auth()->user()->hasRole('super admin') || auth()->user()->hasRole('ServiceBody') || in_array(strtolower(auth()->user()->email), ['rsc@naegypt.org', 'rcp@naegypt.org', 'rvcp@naegypt.org'])))
      <li>
        <a href="{{ route('groups-agendas.archive') }}">
          <div class="parent-icon"><i class="bi bi-journals"></i>
          </div>
          <div class="menu-title">{{ __('messages.Agendas Archive') ?? 'Agendas Archive' }}</div>
        </a>
      </li>
      @endif
      {{-- /Reports Area --}}

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

    </ul>
    <!--end navigation-->

  </aside>