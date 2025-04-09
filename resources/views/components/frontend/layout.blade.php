<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $direction }}">

    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('assets/images/na-logo.jpg') }}" type="image/png" />

    <!-- Include common styles -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{--    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">--}}
    <script src="{{ asset('assets/js/frontend.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('css/frontend.css') }}">
    <!-- Include RTL CSS dynamically -->
    @if ($direction === 'rtl')
        {{-- <link rel="stylesheet" href="../../css/rtl.css"> --}}
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">

    @endif


    <title>NA EGYPT</title>
    
    </head>

    <body class="hanken-grotesk">

        <div class="frontend" >

            <x-frontend.nav-bar />

            <div class="container mt-2">

                <main class="mt-10 max-w-[986px] mx-auto">
                    {{$slot}}
                </main>

            </div>

        </div>
            

    </body>

</html>
