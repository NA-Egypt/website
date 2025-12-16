
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
  <link rel="icon" href="{{ asset('assets/images/na-logo.jpg') }}" type="image/png" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

  <!-- Add Bootstrap RTL CSS conditionally -->
  @if ($direction === 'rtl')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif

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
