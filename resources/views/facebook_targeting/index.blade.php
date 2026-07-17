<x-layout>
    <x-backhead>{{ __('Facebook Targeting Area Mapper') }}</x-backhead>

    <!-- Google Maps JS SDK -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJ15C_GQbFUD1oqhVSZQDsVamHRoPkmhE" async defer></script>

    <div data-vue-app="FacebookTargeting"
         data-initial-groups='@json($groups)'
         data-sync-route="{{ route('facebook-targeting.sync') }}"
         data-download-route="{{ route('facebook-targeting.download') }}"
         data-static-map-route="{{ route('facebook-targeting.static-map') }}"
         data-csrf-token="{{ csrf_token() }}"
         @if(auth()->user()->hasRole('super admin')) data-is-super-admin @endif>
    </div>
</x-layout>
