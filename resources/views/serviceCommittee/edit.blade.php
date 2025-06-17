<x-layout>

    <x-section-head>{{__('messages.Edit') . ' ' . __('messages.Service Committees')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('serviceCommittee.update', $serviceCommittee->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <div class="row mx-0 px-0 g-3">
                <div class="col-md-6 pe-1">
                    <x-forms.input name="ar_name" label="{{ __('messages.Arabic Service Committee Name') }}" :value="$serviceCommittee->ar_name"/>
                </div>
                <div class="col-md-6 ps-1">
                    <x-forms.input name="en_name" label="{{ __('messages.English Service Committee Name') }}" :value="$serviceCommittee->en_name"/>
                </div>
            </div>

            
                @auth
                @can('is-super-admin')
                    <x-forms.select :$users name="email" label="{{ __('messages.Email')}}" :value="$serviceCommittee->email"/>
                @else
                    <input type="hidden" name="user_id"  value="{{ $serviceCommittee->user_id }}"/>
                @endcan
            @endauth
            <x-forms.input name="chairman_name" label="{{ __('messages.Chairman Name')}}" :value="$serviceCommittee->chairman_name"/>
            <x-forms.input name="chairman_phone" label="{{ __('messages.Chairman Phone')}}" :value="$serviceCommittee->chairman_phone"/>

            <x-forms.input id="location" name="location" label="{{ __('messages.Locations')}}" :value="$serviceCommittee->location"/>
            <x-forms.input id="ar_address" name="ar_address" label="{{ __('messages.Arabic Address')}}" :value="$serviceCommittee->ar_address"/>
            <x-forms.input id="en_address" name="en_address" label="{{ __('messages.English Address')}}" :value="$serviceCommittee->en_address"/>
            <x-forms.input id="notes" name="notes" label="{{ __('messages.Committee Meetings')}}" :value="$serviceCommittee->notes"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>