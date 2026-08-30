<x-layout>
    @php
        $user = Auth::user();
        $direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    @endphp
    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Group')}}</x-backhead>

    <div class="container mb-5 mt-4" dir="{{ $direction }}">
        <form action="{{ route('group.update', $group->id) }}" method="post" class="col-md-12 col-lg-9 mx-auto mt-1">
            @csrf
            @method('PUT')

            {{-- SECTION 1: Basic Information --}}
            <div class="glass-card p-4 rounded-4 mb-4 shadow-sm border border-light">
                <h5 class="fw-bold mb-4 text-primary d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 text-info"></i>
                    {{ __('messages.Basic Information') ?? 'Basic Information' }}
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-forms.input name="ar_name" label="{{ __('messages.Arabic Group Name') }}" value="{{ $group->ar_name }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.input name="en_name" label="{{ __('messages.English Group Name') }}" value="{{ $group->en_name }}"/>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-forms.input name="ar_gsr_name" label="{{ __('messages.Arabic GSR Name')}}" value="{{ $group->ar_gsr_name }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.input name="en_gsr_name" label="{{ __('messages.English GSR Name')}}" value="{{ $group->en_gsr_name }}"/>
                    </div>
                </div>

                @auth
                    @can('is-super-admin')
                        <div class="row g-3">
                            <div class="col-12">
                                <x-forms.select :$users name="user_id" label="{{ __('messages.Email')}}" value="{{ $group->user_id }}"/>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="user_id" value="{{ $group->user_id }}"/>
                    @endcan
                @endauth
            </div>

            {{-- SECTION 2: Contact & Settings --}}
            <div class="glass-card p-4 rounded-4 mb-4 shadow-sm border border-light">
                <h5 class="fw-bold mb-4 text-primary d-flex align-items-center">
                    <i class="bi bi-gear-fill me-2 text-warning"></i>
                    {{ __('messages.Contact & Capacity') ?? 'Contact & Capacity' }}
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <x-forms.input name="phone" label="{{ __('messages.Phone')}}" value="{{ $group->phone }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.input name="capacity" label="{{ __('messages.Capacity')}}" type="number" value="{{ $group->capacity }}"/>
                    </div>
                </div>

                <input type="hidden" name="group_type" value="فعلي">
            </div>

            {{-- SECTION 3: Location Details --}}
            <div class="glass-card p-4 rounded-4 mb-4 shadow-sm border border-light">
                <h5 class="fw-bold mb-4 text-primary d-flex align-items-center">
                    <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                    {{ __('messages.Location Details') ?? 'Location Details' }}
                </h5>

                <div class="row g-3">
                    <div class="col-12">
                        <x-forms.input id="location" name="location" label="{{ __('messages.Locations') }}" value="{{ $group->location }}"/>
                    </div>
                </div>

                <div class="row g-3 address-fields">
                    <div class="col-md-6">
                        <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}" value="{{ $group->ar_address }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}" value="{{ $group->en_address }}"/>
                    </div>
                </div>

                @auth
                    @can('is-super-admin')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.select :$serviceBodies name="service_body_id" label="{{ __('messages.Service Body')}}" value="{{ $group->service_body_id ?? '' }}"/>
                            </div>
                            <div class="col-md-6">
                                <x-forms.select :$neighborhoods name="neighborhood_id" label="{{ __('messages.Neighborhood')}}" value="{{ $group->neighborhood_id ?? '' }}"/>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="service_body_id" value="{{ $group->service_body_id ?? '' }}">
                        <input type="hidden" name="neighborhood_id" value="{{ $group->neighborhood_id ?? '' }}">
                    @endcan
                @endauth
            </div>

            {{-- Action Buttons --}}
            <div class="row g-3 mt-4 mb-5">
                <div class="col-6">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary py-2 px-5 rounded-pill w-100 shadow-sm transition-hover">
                        <i class="bi bi-x-circle me-2"></i>{{ __('messages.Cancel') }}
                    </a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-primary py-2 px-5 rounded-pill w-100 shadow-sm transition-hover" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); border: none;">
                        <i class="bi bi-check-circle me-2"></i>{{ __('messages.Update') }}
                    </button>
                </div>
            </div>

        </form>
    </div>

    <style>
        .transition-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .transition-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.75);
        }
        [dir="rtl"] .form-check-input {
            float: right !important;
            margin-right: -1.5em;
            margin-left: 0;
        }
        .address-fields {
            transition: all 0.4s ease;
        }
        .widgets-icons {
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
    </style>
</x-layout>