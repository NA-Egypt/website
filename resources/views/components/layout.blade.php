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

    <!-- Include RTL CSS dynamically -->
    {{-- @if ($direction === 'rtl')
      <link rel="stylesheet" href="public/assets/css/rtl.css">
    @endif --}}

  <title>{{__('messages.NA')}}</title>

</head>
<style>
    body:not(:has(.sidebar-wrapper)) .top-header .navbar {
        left: 0 !important;
    }
</style>
<body class="hanken-grotesk">
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
        <main class="page-content hanken-grotesk ">
          {{ $slot }}
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