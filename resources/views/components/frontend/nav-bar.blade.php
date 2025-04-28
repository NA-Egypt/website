<nav class="main-nav">
    <section class="top-nav">
      <div class="container-fluid px-3">
        <!-- Logo -->
        <div class="logo">
          <a href="{{ route('frontend.home') }}">
            <img src="{{ asset('assets/images/na.png') }}" alt="NA Egypt" width="210" height="70">
          </a>
        </div>
  
        <!-- Menu Button for Mobile -->
        <input id="menu-toggle" type="checkbox" />
        <label class="menu-button-container" for="menu-toggle">
          <div class="menu-button"></div>
        </label>
  
        <!-- Main Menu -->
        <ul class="menu">
          <li><a href="{{ route('frontend.home') }}">{{ __('messages.Home') }}</a></li>
          <li><a href="{{ route('frontend.meetings') }}">{{ __('messages.Meetings') }}</a></li>
  
          <!-- Language Switcher -->
          @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
          <li class="dropdown">
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
              <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" width="20" height="15">
              <span>{{ $properties['native'] }}</span>
            </a>
          </li>
          @endforeach
  
          <!-- User Dropdown -->
          <li class="dropdown">
            <a href="#">
              <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img" alt="">
              <span>@auth {{ Auth::user()->name }} @else Guest @endauth</span>
            </a>
            {{--<ul class="dropdown-content">--}}
              @auth
              <li><a href="#">{{ Auth::user()->email }}</a></li>
              @if(Auth::user()->hasRole('super admin'))
              <li><a href="{{ route('dashboard') }}">{{ __('messages.Dashboard') }}</a></li>
              @elseif(Auth::user()->hasRole('gsr'))
              @php
                $group = \App\Models\Group::whereHas('user', function ($q) { $q->where('email', Auth::user()->email); })->first();
              @endphp
              @if ($group)
              <li><a href="{{ route('group.show', ['group' => $group->id]) }}">{{ __('messages.Group Details') }}</a></li>
              @endif
              @endif
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('messages.Logout') }}</a>
                </form>
              </li>
              @else
              <li>
                <a href="{{ url('/login/microsoft') }}" class="btn btn-primary">Sign-in with Microsoft</a>
              </li>
              @endauth
            {{--</ul>--}}
          </li>
        </ul>
      </div>
    </section>
  </nav>