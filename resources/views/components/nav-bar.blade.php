<header class="top-header">        
    <nav class="navbar navbar-expand">
      <div class="mobile-toggle-icon d-xl-none">
        <x-fas-bars style="width:16px; height:16px;"/>
        {{-- <i class="bi bi-list"></i> --}}
      </div>
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
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret p-0" href="#" data-bs-toggle="dropdown">
              <div class="user-setting d-flex align-items-center gap-2 bg-light rounded-pill p-1 pe-3 border">
                <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img rounded-circle border" alt="" style="width: 32px; height: 32px;">
                <div class="user-name d-none d-sm-block">
                  <span class="fw-bold text-dark small">
                  @auth
                    {{ Auth::user()->name }}
                  @else
                    Guest
                  @endauth
                  </span>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
              </div>
            </a>
            <ul class="dropdown-menu {{ app()->getLocale() === 'ar' ? 'dropdown-menu-start' : 'dropdown-menu-end' }} shadow-lg border-0 rounded-4 p-0 overflow-hidden mt-2" style="min-width: 240px;">
              <li>
                <div class="d-flex align-items-center p-3 bg-light bg-gradient border-bottom">
                    <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img rounded-circle border bg-white p-1" alt="" style="width: 48px; height: 48px;">
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold text-dark">
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
                  <a class="dropdown-item py-2 px-3 rounded-3 mb-1 d-flex align-items-center gap-3 hover-bg-light" href="{{ route('frontend.home') }}">
                      <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                          <i class="bi bi-globe fs-6"></i>
                      </div>
                      <span class="fw-medium">{{__('messages.Web Site')}}</span>
                  </a>
              </li>
              <li><hr class="dropdown-divider my-1"></li>
              <li class="p-1">
                @auth
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-3 text-danger hover-bg-danger-light" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                      <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
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