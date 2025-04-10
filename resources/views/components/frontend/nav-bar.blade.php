<nav class="navbar navbar-expand border border-slate-400">
    <div class="container-fluid px-3">
        <div class="top-navbar d-block d-xl-block">
            <a href="{{ route('frontend.home') }}">
                <img src="{{ asset('assets/images/na.png') }}" alt="" width="240" height="80">
            </a>
        </div>
        
        <div class="top-navbar-center ms-auto">
{{--            <a class="admin-link" href="{{ route('dashboard') }}">{{__('messages.Admin Panel')}}</a>--}}
            <a class="admin-link mx-3" href="{{ route('frontend.meetings') }}">{{__('messages.Meetings')}}</a>
            <a class="admin-link mx-3" href="{{ route('frontend.home') }}">{{__('messages.Home')}}</a>
        </div>

        <div class="top-navbar-right ms-auto ">
            <ul class="navbar-nav align-items-center">

                {{-- Languages Switcher --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <div class="user-name">
                            <img src="{{ asset('assets/images/flags/global.png') }}" alt="global Flag" style="width: 25px; height: 25px;">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li>
                                <a class="dropdown-item" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('assets/images/flags/'.$localeCode.'.png') }}" alt="{{ $localeCode }} Flag" style="width: 20px; height: 20px;">
                                        <div class="setting-text ms-3"><span>{{ $properties['native'] }}</span></div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>


                {{-- User Image --}}
                <li class="nav-item dropdown dropdown-large">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <div class="user-setting d-flex align-items-center gap-1">
                            <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img" alt="">
                            <div class="user-name d-none d-sm-block text-white">
                                @auth
                                    {{ Auth::user()->name }} <!-- Displays the logged-in user's name -->
                                @else
                                    Guest <!-- Fallback if no user is logged in (optional) -->
                                @endauth
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/images/icons/na-logo.png') }}" class="user-img" alt="">
                                    <div class="ms-3">
                                        <h6 class="mb-0 dropdown-user-name ">
                                            @auth
                                                {{ Auth::user()->name }} <!-- Displays the logged-in user's name -->
                                            @else
                                                Guest <!-- Fallback if no user is logged in (optional) -->
                                            @endauth
                                        </h6>
                                        <small class="mb-0 dropdown-user-designation text-secondary">
                                            @auth
                                                {{ Auth::user()->email }} <!-- Displays the logged-in user's name -->
                                            @else
                                                Guest <!-- Fallback if no user is logged in (optional) -->
                                            @endauth
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
{{--                        <li>--}}
{{--                            <a class="dropdown-item" href="pages-user-profile.html">--}}
{{--                                <div class="d-flex align-items-center">--}}
{{--                                    <div class="setting-icon"><i class="bi bi-person-fill"></i></div>--}}
{{--                                    <div class="setting-text ms-3"><span>Profile</span></div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a class="dropdown-item" href="#">--}}
{{--                                <div class="d-flex align-items-center">--}}
{{--                                    <div class="setting-icon"><i class="bi bi-gear-fill"></i></div>--}}
{{--                                    <div class="setting-text ms-3"><span>Setting</span></div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <div class="d-flex align-items-center">
                                    <div class="setting-icon"><i class="bi bi-speedometer"></i></div>
                                    <div class="setting-text ms-3"><span>Dashboard</span></div>
                                </div>
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            @auth
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <div class="d-flex align-items-center">
                                            <div class="setting-icon"><i class="bi bi-lock-fill"></i></div>
                                            <div class="setting-text ms-3"><span>Logout</span></div>
                                        </div>
                                    </a>
                                </form>
                            @else
                                <a href="{{ url('/login/microsoft') }}" class="btn btn-primary d-flex align-items-center">
                                    <svg width="20" height="20" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                        <path d="M11.5 11.5H1V1H11.5V11.5Z" fill="#F25022"/> <!-- Red -->
                                        <path d="M22 11.5H11.5V1H22V11.5Z" fill="#7FBA00"/> <!-- Green -->
                                        <path d="M11.5 22H1V11.5H11.5V22Z" fill="#00A4EF"/> <!-- Blue -->
                                        <path d="M22 22H11.5V11.5H22V22Z" fill="#FFB900"/> <!-- Yellow -->
                                    </svg>
                                    Sign in with Microsoft
                                </a>
                            @endauth

                        </li>
                    </ul>
                </li>
                {{-- / User image --}}

            </ul>
        </div>

    </div>
</nav>