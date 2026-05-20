<header class="top-header position-sticky top-0" style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); z-index: 1020;">        
    <nav class="navbar navbar-expand">
      @php
      $hasSidebar = auth()->check() && (
          auth()->user()->hasRole('super admin') ||
          auth()->user()->hasRole('Committees') ||
          in_array(strtolower(auth()->user()->email), ['rsc@naegypt.org', 'rcp@naegypt.org', 'rvcp@naegypt.org'])
      );
      @endphp
      @if($hasSidebar)
      <div class="mobile-toggle-icon d-xl-none p-2 rounded-3" style="color: var(--text-primary); cursor: pointer; transition: background 0.2s;">
        <x-fas-bars style="width:22px; height:22px;"/>
      </div>
      @else
      <a href="{{ route('dashboard') }}" class="d-xl-none p-2 rounded-3 text-decoration-none d-flex align-items-center justify-content-center" style="color: var(--text-primary);">
        <i class="bi bi-house-door-fill" style="font-size: 22px;"></i>
      </a>
      @endif
      <div class="top-navbar d-none d-xl-block">
        <a href="{{ route('dashboard') }}">

          <img src="{{ asset('assets/images/na.png') }}" alt="" width="150" height="50">
        </a>
      </div>



      <div class="top-navbar-left ms-auto ">
        <ul class="navbar-nav align-items-center">

          <!-- Language Switcher -->
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
          <li class="me-3">
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="btn btn-sm btn-light rounded-pill border shadow-sm d-flex align-items-center gap-2 px-3">
              <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" width="20" height="15" class="rounded-1">
              <span class="fw-bold text-dark small">{{ $properties['native'] }}</span>
            </a>
          </li>
          @endif
      
          {{-- User Menu --}}
          <li class="nav-item dropdown dropdown-large">
            <a class="nav-link p-0" href="#" data-bs-toggle="dropdown">
              <div class="user-setting d-flex align-items-center gap-2 rounded-pill p-1 pe-3" style="background: rgba(0,0,0,0.03); border: 1px solid var(--glass-border);">
                <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img rounded-circle" alt="" style="width: 32px; height: 32px; border: 1px solid var(--glass-border);">
                <div class="user-name d-none d-sm-block">
                  <span class="fw-bold text-dark small">
                  @auth
                    {{ Auth::user()->name }}
                  @else
                    Guest
                  @endauth
                  </span>
                </div>
              </div>
            </a>
            <ul class="dropdown-menu {{ app()->getLocale() === 'ar' ? 'dropdown-menu-start' : 'dropdown-menu-end' }} shadow-lg rounded-4 p-0 overflow-hidden mt-2" style="min-width: 240px; background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border);">
              <li>
                <div class="d-flex align-items-center p-3 border-bottom" style="background: rgba(0,0,0,0.02); border-color: var(--glass-border) !important;">
                    <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img rounded-circle p-1" alt="" style="width: 48px; height: 48px; background: rgba(0,0,0,0.05); border: 1px solid var(--glass-border);">
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                          @auth
                            {{ Auth::user()->name }}
                          @else
                            Guest
                          @endauth
                        </h6>
                        <small class="text-muted small">
                          @auth
                            {{ Str::limit(Auth::user()->email, 20) }}
                          @else
                            Welcome
                          @endauth
                        </small>
                    </div>
                </div>
              </li>
              <li class="p-1">
                  <a class="dropdown-item py-2 px-3 rounded-3 mb-1 d-flex align-items-center gap-3 neo-list-item" href="{{ route('frontend.home') }}" style="color: var(--text-primary);">
                      <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); color: #3b82f6;">
                          <i class="bi bi-globe fs-6"></i>
                      </div>
                      <span class="fw-medium">{{__('messages.Web Site')}}</span>
                  </a>
              </li>
              <li><hr class="dropdown-divider my-1" style="border-color: var(--glass-border);"></li>
              <li class="p-1">
                @auth
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-3 text-danger neo-list-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                      <div class="rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">
                          <i class="bi bi-box-arrow-right fs-6"></i>
                      </div>
                      <span class="fw-bold">{{__('messages.Logout')}}</span>
                    </a>
                  </form>
                @else
                  <a href="{{ url('/login/microsoft') }}" class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-3">
                     <svg width="24" height="24" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11.5 11.5H1V1H11.5V11.5Z" fill="#F25022"/>
                      <path d="M22 11.5H11.5V1H22V11.5Z" fill="#7FBA00"/>
                      <path d="M11.5 22H1V11.5H11.5V22Z" fill="#00A4EF"/>
                      <path d="M22 22H11.5V11.5H22V22Z" fill="#FFB900"/>
                    </svg>
                    <span class="fw-medium">Sign in</span>
                  </a>
                @endauth
              </li>
            </ul>
          </li>
          {{-- / User image --}}

        </ul>
      </div>
    </nav>
</header>