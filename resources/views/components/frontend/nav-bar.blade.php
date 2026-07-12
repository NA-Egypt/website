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
    max-height: 64px;
    width: auto;
}
</style>

@php
  $currentLocale = LaravelLocalization::getCurrentLocale();
  $supportedLocales = LaravelLocalization::getSupportedLocales();
  $otherLocales = collect($supportedLocales)->reject(function ($value, $key) use ($currentLocale) {
      return $key === $currentLocale;
  });
  $localeCode = $otherLocales->keys()->first();
  $properties = $otherLocales->first();
@endphp

<nav class="main-nav">
  <section class="top-nav" style="direction: ltr !important;">
    <div class="container-fluid px-0 d-flex align-items-center justify-content-between w-100 h-100" style="direction: ltr !important;">
      
      <!-- Logo (Left side) -->
      <div class="logo bg-white px-3 py-1 rounded-pill shadow-sm d-flex align-items-center justify-content-center">
        <a href="{{ route('frontend.home') }}" class="d-flex align-items-center">
          <img src="{{ asset('assets/images/logo.png') }}" alt="NA Egypt" class="logo-img">
        </a>
      </div>

      <!-- Desktop Navigation Menu (Center) -->
      <!-- Order is reversed so Home is on the far right in both LTR & RTL -->
      <div class="d-none d-lg-flex align-items-center gap-4">
        <ul class="d-flex align-items-center gap-4 m-0 p-0 list-unstyled" style="direction: ltr !important;">
          <!-- Dropdown: Resources -->
          <li class="nav-item-dropdown">
            <a href="#" class="nav-link-custom dropdown-trigger">
              {{ __('messages.Resources') }} <i class="bi bi-chevron-down ms-1 small"></i>
            </a>
            <ul class="nav-submenu shadow-lg">
              <li>
                <a href="{{ route('frontend.literature') }}" class="submenu-item {{ Route::currentRouteName() === 'frontend.literature' ? 'active' : '' }}">
                  <i class="bi bi-book-half me-2 text-info"></i> {{ __('messages.Literature') }}
                </a>
              </li>
              <li>
                <a href="{{ route('frontend.events') }}" class="submenu-item {{ Route::currentRouteName() === 'frontend.events' ? 'active' : '' }}">
                  <i class="bi bi-calendar-event-fill me-2 text-danger"></i> {{ __('messages.Events') ?? 'Events' }}
                </a>
              </li>
              <li>
                <a href="https://outlook.com/naegypt.org" target="_blank" rel="noopener noreferrer" class="submenu-item">
                  <i class="bi bi-envelope-fill me-2 text-primary"></i> {{ __('messages.Login to your email') }}
                </a>
              </li>
            </ul>
          </li>

          <!-- Dropdown: About NA -->
          <li class="nav-item-dropdown">
            <a href="#" class="nav-link-custom dropdown-trigger">
              {{ __('messages.About NA') }} <i class="bi bi-chevron-down ms-1 small"></i>
            </a>
            <ul class="nav-submenu shadow-lg">
              <li>
                <a href="{{ route('frontend.forpublic') }}" class="submenu-item {{ Route::currentRouteName() === 'frontend.forpublic' ? 'active' : '' }}">
                  <i class="bi bi-megaphone-fill me-2 text-info"></i> {{ __('messages.forpublic') }}
                </a>
              </li>
              <li>
                <a href="{{ route('frontend.comms') }}" class="submenu-item {{ Route::currentRouteName() === 'frontend.comms' ? 'active' : '' }}">
                  <i class="bi bi-people-fill me-2 text-success"></i> {{ __('messages.comms') ?? 'Committees' }}
                </a>
              </li>
              <li>
                <a href="{{ route('frontend.fdsurvey') }}" class="submenu-item {{ Route::currentRouteName() === 'frontend.fdsurvey' ? 'active' : '' }}">
                  <i class="bi bi-file-earmark-bar-graph-fill me-2 text-warning"></i> {{ __('messages.fdsurvey') ?? 'FD Survey' }}
                </a>
              </li>
              <li>
                <a href="{{ route('frontend.questions') }}" class="submenu-item {{ Route::currentRouteName() === 'frontend.questions' ? 'active' : '' }}">
                  <i class="bi bi-patch-question-fill me-2 text-primary"></i> {{ __('messages.test_page.nav.questions.title') }}
                </a>
              </li>
            </ul>
          </li>

          <li>
            <a href="{{ route('frontend.meetings') }}" 
               class="nav-link-custom {{ Route::currentRouteName() === 'frontend.meetings' ? 'active' : '' }}">
              {{ __('messages.Meetings') }}
            </a>
          </li>

          <li>
            <a href="{{ route('frontend.home') }}" 
               class="nav-link-custom {{ Route::currentRouteName() === 'frontend.home' ? 'active' : '' }}">
              {{ __('messages.Home') }}
            </a>
          </li>
        </ul>
      </div>

      <!-- Desktop Actions (Right side) -->
      <div class="d-none d-lg-flex align-items-center gap-3">
        <!-- Account Dropdown -->
        <div class="dropdown">
          <button class="btn btn-sm btn-glass dropdown-toggle d-flex align-items-center gap-2" type="button" id="desktopUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <x-fas-user style="width:14px; height:14px;"/>
            <span>{{ auth()->check() ? auth()->user()->name : __('messages.Account') }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-custom shadow-lg p-2 mt-2 dropdown-menu-end" aria-labelledby="desktopUserDropdown">
            @auth
              <li class="px-3 py-2 text-center border-bottom border-light-subtle">
                <img src="{{ asset('assets/images/icons/na-logo.png') }}" alt="" class="rounded-circle mb-1" width="36" height="36">
                <div class="fw-bold text-dark small">{{ auth()->user()->name }}</div>
                <div class="text-muted text-truncate small" style="max-width: 180px;">{{ auth()->user()->email }}</div>
              </li>
              @if(auth()->user()->hasRole('super admin'))
                <li><a class="dropdown-item rounded-2 mt-1" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> {{ __('messages.Dashboard') }}</a></li>
              @elseif(auth()->user()->hasRole('gsr'))
                @php
                  $group = \App\Models\Group::whereHas('user', function ($q) { $q->where('email', auth()->user()->email); })->first();
                @endphp
                @if ($group)
                  <li><a class="dropdown-item rounded-2 mt-1" href="{{ route('group.show', ['group' => $group->id]) }}"><i class="bi bi-card-text me-2"></i> {{ __('messages.Group Details') }}</a></li>
                @endif
              @endif
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <a class="dropdown-item rounded-2 text-danger" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i> {{ __('messages.Logout') }}
                  </a>
                </form>
              </li>
            @else
              <li><a class="dropdown-item rounded-2" href="{{ url('/login/microsoft') }}"><i class="bi bi-microsoft me-2"></i> {{ __('messages.Login') }}</a></li>
            @endauth
          </ul>
        </div>

        <!-- Language Switcher -->
        @if ($localeCode && $properties)
          <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="btn btn-sm btn-glass d-flex align-items-center gap-2">
            <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" width="18" height="12" class="rounded-sm">
            <span class="small">{{ $properties['native'] }}</span>
          </a>
        @endif

        <!-- Contact Us Button -->
        <a href="{{ route('contactus.create') }}" class="btn btn-sm btn-contact d-flex align-items-center gap-2">
          <x-fas-message style="width:12px; height:12px;"/>
          <span>{{ __('messages.contactus') }}</span>
        </a>
      </div>

      <!-- Mobile Toggle Button (Visible on mobile/tablet) -->
      <button class="btn btn-glass d-lg-none px-3 border-0 me-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNavbar" aria-controls="mobileNavbar">
        <i class="bi bi-list fs-2 text-white"></i>
      </button>

    </div>
  </section>
</nav>

<!-- Mobile Offcanvas Menu (Drawer) -->
<div class="offcanvas {{ app()->getLocale() === 'ar' ? 'offcanvas-start' : 'offcanvas-end' }} d-lg-none text-white mobile-drawer" tabindex="-1" id="mobileNavbar" aria-labelledby="mobileNavbarLabel">
  <div class="offcanvas-header bg-white py-3 border-bottom border-light-subtle">
    <h5 class="offcanvas-title" id="mobileNavbarLabel">
      <img src="{{ asset('assets/images/logo.png') }}" alt="NA Egypt" class="logo-img">
    </h5>
    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
    
    <!-- Menu Links -->
    <div class="mobile-menu-links">
      <ul class="list-unstyled p-0 m-0">
        <li class="mb-3">
          <a href="{{ route('frontend.home') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'frontend.home' ? 'active' : '' }}">
            <i class="bi bi-house-door-fill me-3"></i> {{ __('messages.Home') }}
          </a>
        </li>
        <li class="mb-3">
          <a href="{{ route('frontend.meetings') }}" class="mobile-nav-link {{ Route::currentRouteName() === 'frontend.meetings' ? 'active' : '' }}">
            <i class="bi bi-people-fill me-3"></i> {{ __('messages.Meetings') }}
          </a>
        </li>

        <!-- Mobile Accordion: About NA -->
        <li class="mb-3">
          <a class="mobile-nav-link d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" href="#collapseAbout" role="button" aria-expanded="false" aria-controls="collapseAbout">
            <span><i class="bi bi-info-circle-fill me-3"></i> {{ __('messages.About NA') }}</span>
            <i class="bi bi-chevron-down small"></i>
          </a>
          <div class="collapse mt-2 ps-3" id="collapseAbout">
            <ul class="list-unstyled ps-3 border-start border-light-subtle">
              <li class="mb-2">
                <a href="{{ route('frontend.forpublic') }}" class="mobile-sub-link {{ Route::currentRouteName() === 'frontend.forpublic' ? 'active' : '' }}">
                  {{ __('messages.forpublic') }}
                </a>
              </li>
              <li class="mb-2">
                <a href="{{ route('frontend.comms') }}" class="mobile-sub-link {{ Route::currentRouteName() === 'frontend.comms' ? 'active' : '' }}">
                  {{ __('messages.comms') ?? 'Committees' }}
                </a>
              </li>
              <li class="mb-2">
                <a href="{{ route('frontend.fdsurvey') }}" class="mobile-sub-link {{ Route::currentRouteName() === 'frontend.fdsurvey' ? 'active' : '' }}">
                  {{ __('messages.fdsurvey') ?? 'FD Survey' }}
                </a>
              </li>
              <li class="mb-2">
                <a href="{{ route('frontend.questions') }}" class="mobile-sub-link {{ Route::currentRouteName() === 'frontend.questions' ? 'active' : '' }}">
                  {{ __('messages.test_page.nav.questions.title') }}
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Mobile Accordion: Resources -->
        <li class="mb-3">
          <a class="mobile-nav-link d-flex align-items-center justify-content-between collapsed" data-bs-toggle="collapse" href="#collapseResources" role="button" aria-expanded="false" aria-controls="collapseResources">
            <span><i class="bi bi-box-fill me-3"></i> {{ __('messages.Resources') }}</span>
            <i class="bi bi-chevron-down small"></i>
          </a>
          <div class="collapse mt-2 ps-3" id="collapseResources">
            <ul class="list-unstyled ps-3 border-start border-light-subtle">
              <li class="mb-2">
                <a href="{{ route('frontend.literature') }}" class="mobile-sub-link {{ Route::currentRouteName() === 'frontend.literature' ? 'active' : '' }}">
                  {{ __('messages.Literature') }}
                </a>
              </li>
              <li class="mb-2">
                <a href="{{ route('frontend.events') }}" class="mobile-sub-link {{ Route::currentRouteName() === 'frontend.events' ? 'active' : '' }}">
                  {{ __('messages.Events') ?? 'Events' }}
                </a>
              </li>
              <li class="mb-2">
                <a href="https://outlook.com/naegypt.org" target="_blank" rel="noopener noreferrer" class="mobile-sub-link">
                  {{ __('messages.Login to your email') }}
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>

    <!-- Mobile Actions (Bottom Area) -->
    <div class="mobile-actions border-top border-light-subtle pt-4 mt-auto">
      <div class="d-grid gap-3">
        <!-- Contact Us -->
        <a href="{{ route('contactus.create') }}" class="btn btn-contact w-100 py-2 d-flex align-items-center justify-content-center gap-2">
          <x-fas-message style="width:14px; height:14px;"/>
          <span>{{ __('messages.contactus') }}</span>
        </a>

        <!-- Language Switcher -->
        @if ($localeCode && $properties)
          <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="btn btn-glass w-100 py-2 d-flex align-items-center justify-content-center gap-2">
            <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" width="20" height="15" class="rounded-sm">
            <span>{{ $properties['native'] }}</span>
          </a>
        @endif

        <!-- User Account Option -->
        @auth
          <div class="card bg-white-5 border border-white-10 p-3 rounded-3 text-center mb-2">
            <div class="d-flex align-items-center gap-3">
              <img src="{{ asset('assets/images/icons/na-logo.png') }}" alt="" class="rounded-circle" width="40" height="40">
              <div class="text-start">
                <div class="fw-bold text-white small">{{ auth()->user()->name }}</div>
                <div class="text-white-50 text-truncate small" style="max-width: 180px;">{{ auth()->user()->email }}</div>
              </div>
            </div>
            <div class="d-flex gap-2 mt-3">
              @if(auth()->user()->hasRole('super admin'))
                <a class="btn btn-sm btn-outline-light flex-grow-1" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> {{__('messages.Dashboard')}}</a>
              @endif
              <form method="POST" action="{{ route('logout') }}" class="flex-grow-1">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger w-100"><i class="bi bi-box-arrow-right"></i> {{ __('messages.Logout') }}</button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ url('/login/microsoft') }}" class="btn btn-glass w-100 py-2 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-microsoft"></i>
            <span>{{ __('messages.Login') }}</span>
          </a>
        @endauth
      </div>
    </div>

  </div>
</div>
