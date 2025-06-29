<nav class="main-nav" style="box-shadow: 0 4px 4px rgba(0, 0, 0, 0.1);">
    <section class="top-nav">
      <div class="container-fluid px-3">
        <!-- Logo -->
        <div class="logo">
          <a href="{{ route('frontend.home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="NA Egypt" width="307">
          </a>
        </div>
  
        <!-- Menu Button for Mobile -->
        <input id="menu-toggle" type="checkbox" />
        <label class="menu-button-container" for="menu-toggle">
          <div class="menu-button"></div>
        </label>
  
        <!-- Main Menu -->
        <ul class="menu">
          <li><a href="https://facebook.com/OfficialNAEgyPage" target="_blank" style="color:white;"><x-fab-facebook style="width:32px; height:32px;"/></a></li>
          @if (Route::currentRouteName() !== 'frontend.home')
          <li><a href="{{ route('frontend.home') }}" class="btn btn-outline-light">
            <img src="{{ asset('assets/images/icons/na-logo.png') }}" alt="" style="width:18px; height:18px; vertical-align: sub;">
            &nbsp;{{ __('messages.Home') }}
           </a>
          </li>
          @endif
          @if (Route::currentRouteName() !== 'frontend.meetings')
          <li><a href="{{ route('frontend.meetings') }}" class="btn btn-outline-light"><x-fas-users style="width:16px; height:16px;"/>&nbsp;{{ __('messages.Meetings') }}</a></li>
          @endif
          @if (Route::currentRouteName() !== 'frontend.literature')
          <li><a href="{{ route('frontend.literature') }}" class="btn btn-outline-light"><x-fas-book style="width:16px; height:16px;"/>&nbsp;{{ __('messages.Literature') }}</a></li>
          @endif
          @if (Route::currentRouteName() !== 'frontend.forpublic')
          <li><a href="{{ route('frontend.forpublic') }}" class="btn btn-outline-light"><x-fas-bullhorn style="width:16px; height:16px;"/>&nbsp;{{ __('messages.forpublic') }}</a></li>
          @endif
          @if (Route::currentRouteName() !== 'contactus.create')
          <li><a href="{{ route('contactus.create') }}" class="btn btn-outline-light"><x-fas-message style="width:16px; height:16px;"/>&nbsp;{{ __('messages.contactus') }}</a></li>
          @endif
          <!-- Language Switcher -->
          @php
            $currentLocale = LaravelLocalization::getCurrentLocale();
            $supportedLocales = LaravelLocalization::getSupportedLocales();
            $otherLocales = collect($supportedLocales)->reject(function ($value, $key) use ($currentLocale) {
                return $key === $currentLocale;
            });
            $localeCode = $otherLocales->keys()->first();
            $properties = $otherLocales->first();
          @endphp
          @if ($localeCode && $properties)
          <li>
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="btn btn-outline-warning">
              <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" width="20" height="15">
              <span>{{ $properties['native'] }}</span>
            </a>
          </li>
          @endif
  
          <!-- User Dropdown -->
          <li class="dropdown">
    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      <x-fas-user style="width:16px; height:16px;"/>&nbsp;{{ __('messages.Account') }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="userDropdown">
      <li class="py-2">
        <img src="{{ asset('assets/images/icons/na-logo.png') }}" alt="" class="rounded-circle" width="40" height="40">
      </li>
      @auth
        <li><strong>{{ Auth::user()->name }}</strong></li>
        <li><small>{{ Auth::user()->email }}</small></li>
        <li><hr class="dropdown-divider"></li>
        @if(Auth::user()->hasRole('super admin'))
          <li><a class="dropdown-item" href="{{ route('dashboard') }}">{{ __('messages.Dashboard') }} <x-fas-gear style="width:16px; height:16px;"/></a></li>
        @elseif(Auth::user()->hasRole('gsr'))
          @php
            $group = \App\Models\Group::whereHas('user', function ($q) { $q->where('email', Auth::user()->email); })->first();
          @endphp
          @if ($group)
            <li><a class="dropdown-item" href="{{ route('group.show', ['group' => $group->id]) }}">{{ __('messages.Group Details') }}</a></li>
          @endif
        @endif
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
              {{ __('messages.Logout') }} <x-fas-sign-out-alt style="width:16px; height:16px;"/>
            </a>
          </form>
        </li>
      @else
        <li><a class="dropdown-item" href="{{ url('/login/microsoft') }}">{{ __('messages.Login') }} <x-fas-sign-in-alt style="width:16px; height:16px;"/></a></li>
      @endauth
    </ul>
  </li>
        </ul>
      </div>
    </section>
  </nav>