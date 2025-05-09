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
.top-nav {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  background-color: #00BAF0;
  background: linear-gradient(to left, #000487 2%, transparent 50%);
  /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
  color: #FFF;
  height: 100px;
  /* padding: 1em; */
  direction: rtl;
}

.menu {
  display: flex;
  flex-direction: row;
  list-style-type: none;
  margin: 0;
  padding: 0;
}

.menu > li {
  margin: 0 1rem;
  overflow: hidden;
}

.menu-button-container {
  display: none;
  height: 100%;
  width: 30px;
  cursor: pointer;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

#menu-toggle {
  display: none;
}

.menu-button,
.menu-button::before,
.menu-button::after {
  display: block;
  background-color: #fff;
  position: absolute;
  height: 4px;
  width: 30px;
  transition: transform 400ms cubic-bezier(0.23, 1, 0.32, 1);
  border-radius: 2px;
}

.menu-button::before {
  content: '';
  margin-top: -8px;
}

.menu-button::after {
  content: '';
  margin-top: 8px;
}

#menu-toggle:checked + .menu-button-container .menu-button::before {
  margin-top: 0px;
  transform: rotate(405deg);
}

#menu-toggle:checked + .menu-button-container .menu-button {
  background: rgba(255, 255, 255, 0);
}

#menu-toggle:checked + .menu-button-container .menu-button::after {
  margin-top: 0px;
  transform: rotate(-405deg);
}

@media (max-width: 700px) {
  .menu-button-container {
    display: flex;
  }
  .menu {
    position: absolute;
    top: 0;
    margin-top: 100px;
    left: 0;
    flex-direction: column;
    width: 100%;
    justify-content: center;
    align-items: center;
    z-index:1;
  }
  #menu-toggle ~ .menu li {
    height: 0;
    margin: 0;
    padding: 0;
    border: 0;
    transition: height 400ms cubic-bezier(0.23, 1, 0.32, 1);
  }
  #menu-toggle:checked ~ .menu li {
    border: 0px solid #000487;
    height: 3.5em;
    padding: 0.5em;
    transition: height 400ms cubic-bezier(0.23, 1, 0.32, 1);
  }
  .menu > li {
    display: flex;
    justify-content: center;
    margin: 0;
    padding: 0.5em 0;
    width: 100%;
    color: white;
    background-color: #222;
  }
  .menu > li:not(:last-child) {
    border-bottom: 1px solid #444;
  }
  .meetings-grid {
    grid-template-columns: 1fr;
  }
}

.logo {
  position: absolute;
  top: 17px;
  left: 12px;
  float: left;
  z-index: 3;

  /* font-size: 1.2rem; */
  /* color: #0e2431; */
}

.me-2 {
  width: 0.5rem; 
  height: 0.5rem; 
  background-color: crimson; 
  display: inline-block;
  margin-left: .5rem !important;
}

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
  background-color: #f7f7f7;
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

.warpper {
  display: flex;
  flex-direction: column;
  align-items: center;
  /* float: left; */
}

.tab {
  cursor: pointer;
  padding: 10px 20px;
  margin: 0px 2px;
  background: #32557f;
  display: inline-block;
  color: #fff;
  border-radius: 3px 3px 0px 0px;
  /* box-shadow: 0 0.5rem 0.8rem #00000080; */
}

.panels {
  background: #fff;
  /* box-shadow: 0 2rem 2rem #000000; */
  min-height: 200px;
  width: 98%;
  max-width: 700px;
  border-radius: 10px;
  overflow: hidden;
  padding: 20px;
}

.panel {
  display: none;
  animation: fadein 0.8s;
}

@keyframes fadein {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.panel-title {
  font-size: 1.5em;
  font-weight: bold;
}

.radio {
  display: none;
}

#one:checked ~ .panels #one-panel,
#two:checked ~ .panels #two-panel,
#three:checked ~ .panels #three-panel {
  display: block;
}

#one:checked ~ .tabs #one-tab,
#two:checked ~ .tabs #two-tab,
#three:checked ~ .tabs #three-tab {
  background: #fff;
  color: #000;
  border-top: 3px solid #32557f;
}
footer {
    position: sticky;
    bottom: 0;
    width: 100%;
    background-color: #f8f9fa;
    padding: 10px;
    text-align: center;
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
