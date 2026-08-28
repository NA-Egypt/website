<x-frontend.layout 
    title="{{ app()->getLocale() === 'ar' ? 'خريطة مناطق ولجان خدمة الزمالة | NA Egypt' : 'Service Bodies & Territories Map | NA Egypt' }}" 
    description="{{ app()->getLocale() === 'ar' ? 'خريطة تفاعلية توضح النطاقات الجغرافية والمجموعات التابعة لكل منطقة ولجنة ومنتدى خدمة في زمالة المدمنين المجهولين بمصر.' : 'Interactive map visualizing geographic boundary territories and groups of NA Egypt Service Bodies.' }}"
>
    <!-- Google Maps JS SDK with modern async loading -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJ15C_GQbFUD1oqhVSZQDsVamHRoPkmhE&loading=async&libraries=marker,places,geometry" async defer></script>

    <div class="container-fluid px-3 px-lg-4 py-3">
        <div data-vue-app="ServiceBodyMap"
             data-initial-data='@json($mapData)'
             data-csrf-token="{{ csrf_token() }}">
        </div>
    </div>
</x-frontend.layout>
