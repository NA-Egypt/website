@if(session()->has('impersonated_by'))
    <div class="impersonation-banner py-2 px-3 shadow-sm border-bottom text-dark position-relative" 
         style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); z-index: 1055; font-size: 0.9rem;">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-2 py-1 border border-dark rounded-pill fw-bold">
                    <i class="bi bi-incognito me-1"></i> {{ __('messages.currently_impersonating') }}
                </span>
                <span class="fw-bold text-dark me-1">
                    {{ auth()->user()->display_name ?? auth()->user()->name }}
                </span>
                <span class="text-muted small">
                    ({{ auth()->user()->email }})
                </span>
            </div>

            <form action="{{ route('users.stop_impersonating') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3 py-1 bg-white shadow-sm d-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>{{ __('messages.stop_impersonating') }}</span>
                </button>
            </form>
        </div>
    </div>
    <style>
        /* Adjust layout spacing when banner is active */
        body.has-sidebar .top-header .navbar {
            top: 40px !important;
        }
        body.has-sidebar .sidebar-wrapper {
            top: 110px !important;
            height: calc(100vh - 110px) !important;
        }
        .page-content {
            margin-top: 110px !important;
        }
        @media (max-width: 1025.98px) {
            .sidebar-wrapper {
                top: 110px !important;
                height: calc(100vh - 110px) !important;
            }
        }
    </style>
@endif
