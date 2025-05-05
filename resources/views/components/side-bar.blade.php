<aside class="sidebar-wrapper" data-simplebar="true">
        
    {{-- Logo --}}
    <div class="sidebar-header">
      <div>
        <img src="{{ asset('assets/images/na.png') }}" class="logo-icon" alt="logo icon">
      </div>
    </div>
    {{-- / Logo --}}

    <!--navigation-->
    <ul class="metismenu" id="menu">


      {{-- Admin Area --}}
      <li class="menu-label">{{ __('messages.Admin')}}</li>
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="bi bi-gear-fill admin-icon"></i>
          </div>
          <div class="menu-title">{{ __('messages.Admin Settings')}}</div>
        </a>
        <ul>
          <li> <a href="{{route('users.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Users List')}}</a>
          </li>
          <li> <a href="{{ route('permissions.index') }}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Permissions')}}</a>
          </li>
          <li> <a href="{{route('roles.index')}}"><i class="bi bi-arrow-right-short"></i>{{__('messages.Rules')}}</a>
          </li>
        </ul>
      </li>
      {{-- / Admin Area --}}

      {{-- Sections Area --}}
      <li class="menu-label">{{ __('messages.Sections')}}</li>
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="bi bi-grid"></i>
          </div>
          <div class="menu-title">{{ __('messages.Section Details')}}</div>
        </a>
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
      </li>
      {{-- /Sections Area --}}

      {{-- Transactions Area --}}
      <li class="menu-label">{{ __('messages.Logs')}}</li>
      <li>
        <a href="javascript:;" class="has-arrow">
          <div class="parent-icon"><i class="bi bi-receipt-cutoff"></i>
          </div>
          <div class="menu-title">{{ __('messages.Logs Details')  }}</div>
        </a>
        <ul>
          <li> <a href="{{ route('transactions.index') }}"><i class="bi bi-arrow-right-short"></i>{{ __('messages.Logs')}}</a>
          </li>
          </li>
        </ul>
      </li>
      {{-- /Transactions Area --}}

      {{-- Reports Area --}}
{{--      <li class="menu-label">Reports</li>--}}
{{--      <li>--}}
{{--        <a href="javascript:" class="has-arrow">--}}
{{--          <div class="parent-icon"><i class="bi bi-file-earmark-text"></i>--}}
{{--          </div>--}}
{{--          <div class="menu-title">Standard Report</div>--}}
{{--        </a>--}}
{{--        <ul>--}}
{{--          <li> <a href="#"><i class="bi bi-arrow-right-short"></i>Report One</a>--}}
{{--          </li>--}}
{{--          <li> <a href="#"><i class="bi bi-arrow-right-short"></i>Report Two</a>--}}
{{--          </li>--}}
{{--          </li>--}}
{{--        </ul>--}}
{{--      </li>--}}
{{--      <li>--}}
{{--        <a href="javascript:" class="has-arrow">--}}
{{--          <div class="parent-icon"><i class="bi bi-graph-up"></i>--}}
{{--          </div>--}}
{{--          <div class="menu-title">Custom Report</div>--}}
{{--        </a>--}}
{{--        <ul>--}}
{{--          <li> <a href="#"><i class="bi bi-arrow-right-short"></i>Report One</a>--}}
{{--          </li>--}}
{{--          <li> <a href="#"><i class="bi bi-arrow-right-short"></i>Report Two</a>--}}
{{--          </li>--}}
{{--          </li>--}}
{{--        </ul>--}}
{{--      </li>--}}
      {{-- /Reports Area --}}

    </ul>
    <!--end navigation-->

  </aside>