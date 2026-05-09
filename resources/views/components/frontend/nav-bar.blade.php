<style>
.text-glow {
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.8), 0 0 20px rgba(255, 255, 255, 0.6);
}
.hover-opacity-100:hover {
    opacity: 1 !important;
}
.transition-opacity {
    transition: opacity 0.3s ease, text-shadow 0.3s ease;
}
.logo-img {
    width: 307px;
    max-width: 100%;
    height: auto;
}
@media (max-width: 768px) {
    .logo-img {
        max-width: 220px;
    }
}
@media (max-width: 400px) {
    .logo-img {
        max-width: 180px;
    }
}
</style>
<nav class="main-nav" style="box-shadow: 0 4px 4px rgba(0, 0, 0, 0.1);">
    <section class="top-nav">
      <div class="container-fluid px-3 d-flex align-items-center justify-content-between h-100">
        <!-- Logo -->
        <div class="logo">
          <a href="{{ route('frontend.home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="NA Egypt" class="logo-img">
          </a>
        </div>
  
        <!-- Calculate variables for both dropdowns -->
        @php
          $currentLocale = LaravelLocalization::getCurrentLocale();
          $supportedLocales = LaravelLocalization::getSupportedLocales();
          $otherLocales = collect($supportedLocales)->reject(function ($value, $key) use ($currentLocale) {
              return $key === $currentLocale;
          });
          $localeCode = $otherLocales->keys()->first();
          $properties = $otherLocales->first();
        @endphp

        <!-- Menu Button for Mobile -->
        <input id="menu-toggle" type="checkbox" />
        <label class="menu-button-container d-lg-none" for="menu-toggle">
          <div class="menu-button"></div>
        </label>
  
        <!-- Main Menu (Mobile) -->
        <ul class="menu d-lg-none">
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
          @if (Route::currentRouteName() !== 'frontend.events')
          <li><a href="{{ route('frontend.events') }}" class="btn btn-outline-light"><x-fas-calendar-alt style="width:16px; height:16px;"/>&nbsp;{{ __('messages.Events') ?? 'Events' }}</a></li>
          @endif
          @if (Route::currentRouteName() !== 'frontend.literature')
          <li><a href="{{ route('frontend.literature') }}" class="btn btn-outline-light"><x-fas-book style="width:16px; height:16px;"/>&nbsp;{{ __('messages.Literature') }}</a></li>
          @endif
          @if (Route::currentRouteName() !== 'frontend.forpublic')
          <li><a href="{{ route('frontend.forpublic') }}" class="btn btn-outline-light"><x-fas-bullhorn style="width:16px; height:16px;"/>&nbsp;{{ __('messages.forpublic') }}</a></li>
          @endif
          <li>
            <a href="{{ route('frontend.questions') }}"
               class="btn {{ Route::currentRouteName() === 'frontend.questions' ? 'btn-light text-primary' : 'btn-outline-light' }}">
              <i class="bi bi-patch-question-fill" style="font-size:16px;"></i>&nbsp;{{ __('messages.test_page.nav.questions.title') }}
            </a>
          </li>
          @if (Route::currentRouteName() !== 'contactus.create')
          <li><a href="{{ route('contactus.create') }}" class="btn btn-outline-light"><x-fas-message style="width:16px; height:16px;"/>&nbsp;{{ __('messages.contactus') }}</a></li>
          @endif
          <!-- Language Switcher -->
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

        <!-- Desktop Menu -->
        <div class="d-none d-lg-flex align-items-center gap-4">
          <!-- Main Links -->
          <ul class="d-flex align-items-center gap-4 m-0 p-0 list-unstyled">
             <li>
                 <a href="{{ route('frontend.home') }}" 
                    class="text-decoration-none text-white transition-opacity {{ Route::currentRouteName() === 'frontend.home' ? 'text-glow fw-bold' : 'opacity-75 hover-opacity-100' }}">
                   {{ __('messages.Home') }}
                 </a>
             </li>
             <li>
                 <a href="{{ route('frontend.meetings') }}" 
                    class="text-decoration-none text-white transition-opacity {{ Route::currentRouteName() === 'frontend.meetings' ? 'text-glow fw-bold' : 'opacity-75 hover-opacity-100' }}">
                   {{ __('messages.Meetings') }}
                 </a>
             </li>
             <li>
                 <a href="{{ route('frontend.events') }}" 
                    class="text-decoration-none text-white transition-opacity {{ Route::currentRouteName() === 'frontend.events' ? 'text-glow fw-bold' : 'opacity-75 hover-opacity-100' }}">
                   {{ __('messages.Events') ?? 'Events' }}
                 </a>
             </li>
             <li>
                 <a href="{{ route('frontend.literature') }}" 
                    class="text-decoration-none text-white transition-opacity {{ Route::currentRouteName() === 'frontend.literature' ? 'text-glow fw-bold' : 'opacity-75 hover-opacity-100' }}">
                   {{ __('messages.Literature') }}
                 </a>
             </li>
             <li>
                 <a href="{{ route('frontend.forpublic') }}" 
                    class="text-decoration-none text-white transition-opacity {{ Route::currentRouteName() === 'frontend.forpublic' ? 'text-glow fw-bold' : 'opacity-75 hover-opacity-100' }}">
                   {{ __('messages.forpublic') }}
                 </a>
             </li>
             <li>
                 <a href="{{ route('frontend.questions') }}" 
                    class="text-decoration-none text-white transition-opacity {{ Route::currentRouteName() === 'frontend.questions' ? 'text-glow fw-bold' : 'opacity-75 hover-opacity-100' }}">
                   {{ __('messages.test_page.nav.questions.title') }}
                 </a>
             </li>
          </ul>

          <!-- Desktop Actions -->
          <div class="d-flex align-items-center gap-3">
             <a href="{{ route('contactus.create') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
               <x-fas-message style="width:14px; height:14px;"/>&nbsp;{{ __('messages.contactus') }}
             </a>

              <!-- Language Switcher Desktop -->
              @if ($localeCode && $properties)
                <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                  <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" width="16" height="12">
                  <span>{{ $properties['native'] }}</span>
                </a>
              @endif

              <!-- Account Desktop -->
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-light rounded-pill px-3 dropdown-toggle" type="button" id="desktopUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <x-fas-user style="width:14px; height:14px;"/>&nbsp;{{ __('messages.Account') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end text-center" aria-labelledby="desktopUserDropdown">
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
              </div>
          </div>
        </div>

      </div>
    </section>
  </nav>
