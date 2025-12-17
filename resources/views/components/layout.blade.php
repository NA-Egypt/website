
<!doctype html>
@php
$direction = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
@endphp
<html lang="{{ app()->getLocale() }}"  dir="{{ $direction }}" class="minimal-theme">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <meta name="viewport" content="width=device-width, initial-scale=1">




  /* Custom font override */
  </style>

  <title>{{__('messages.NA')}}</title>

</head>
<body class="hanken-grotesk @can('is-super-admin') has-sidebar @endcan">
{{--     wrapper--}}
    <div class="wrapper">

        <!-- Nav Bar-->
        <x-nav-bar />
        <!-- / Nav Bar-->
         @can('is-super-admin')
        <!-- sidebar -->
        <x-side-bar />
        <!-- / sidebar -->
    @endcan
        <main class="page-content hanken-grotesk">
          <div class="container-fluid">
            {{ $slot }}
          </div>
        </main>

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
        <!--end overlay-->

        <!--Start Back To Top Button-->
        <x-back-to-top />
        <!--End Back To Top Button-->

        <!--start switcher-->
        {{-- <x-switch-themes /> --}}
        <!--end switcher-->

    </div>
    <!-- / wrapper-->
</body>

</html>
