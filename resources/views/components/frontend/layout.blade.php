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
  min-width: 200px;
  height: 140px;
  display: inline-block;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
  background-image: url('{{ asset('assets/images/icons/na-logo.png') }}');
  background-size: 140px;
  background-position: right 125px bottom -4px;
  background-repeat: no-repeat;
  position: relative;
  overflow: hidden;
  z-index: 1;
}
.helpline-box::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent overlay */
  z-index: -1;
}

.calc-box {
  background-color: #ffffff;
  border: 4px solid #00698f;
  border-radius: 10px;
  padding: 10px;
  margin: 10px;
  width: 30%;
  min-width: 200px;
  display: inline-block;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.helpline-box h4 {
  color: #00698f;
  margin-top: 0;
}

.helpline-box a {
  text-decoration: none;
  color: #337ab7;
}

.helpline-box a:hover {
  color: #23527c;
}
</style>
    <title>NA EGYPT</title>
    </head>

    <body class="hanken-grotesk">
      <x-frontend.nav-bar />
        <div class="container">
          <main class="mt-10 max-w-[986px] mx-auto">
            {{$slot}}
          </main>
        </div>
      <x-frontend.footer />
    </body>
</html>
