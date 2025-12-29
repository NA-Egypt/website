<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $direction }}">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="{{ asset('assets/images/na-logo32.webp') }}" type="image/webp" />

  <!-- Include common styles -->
  @vite(['resources/js/app.js', 'resources/css/app.css'])
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://www.google.com/recaptcha/api.js"></script>
  <script src="{{ asset('assets/js/frontend.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}" />
  <!-- Include RTL CSS dynamically -->
  @if($direction === 'rtl')
      <link rel="stylesheet" href="{{ asset('assets/css/rtl.css') }}" />
  @endif
<style>
.helpline-box {
  background-color: #f7f7f7;
  border: 4px solid #00698f;
  color: #00698f !important;
  border-radius: 10px;
  padding: 10px;
  margin: 10px;
  width: 30%;
  min-width: 220px;
  max-width: 220px;
  height: 160px;
  display: inline-block;
  box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
  background-image: url('{{ asset('assets/images/icons/na-logo.png') }}');
  background-size: 140px;
  background-position: right 125px bottom -4px;
  background-repeat: no-repeat;
  position: relative;
  overflow: hidden;
  z-index: 1;
}
</style>
<title>{{__('messages.NA')}}</title>
</head>
  <body class="hanken-grotesk {{$direction}}">
    <x-frontend.nav-bar />
      <div class="container">
        <main class="mt-12 max-w-[986px] px-2 min-w-[100%]: min-h-[100vh]">
          {{$slot}}
        </main>
      </div>
    <x-frontend.footer />
  </body>
</html>
