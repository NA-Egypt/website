<x-layout>
    <x-backhead>{{ app()->getLocale() === 'ar' ? 'خريطة مناطق ولجان الخدمة' : 'Service Bodies & Territories Map' }}</x-backhead>

    <!-- Google Maps JS SDK with modern async loading -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJ15C_GQbFUD1oqhVSZQDsVamHRoPkmhE&loading=async&libraries=marker,places,geometry" async defer></script>

    <div class="container-fluid px-2 py-2">
        <div data-vue-app="ServiceBodyMap"
             data-initial-data='@json($mapData)'
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-layout>
