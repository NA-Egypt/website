<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
    {{-- $direction --}}

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
    <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}">
    <!-- Include RTL CSS dynamically -->
    {{-- @if ($direction === 'rtl') --}}
        <link rel="stylesheet" href="{{ asset('assets/css/rtl.css') }}">
        {{--<link rel="stylesheet" href="{{ asset('css/rtl.css') }}">--}}
    {{--@endif--}}
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.container {
  /* max-width: 1050px; */
  /* width: 100%; */
  margin: auto;
  direction:rtl;
}

.navbar {
  width: 100%;
  box-shadow: 0 1px 4px rgb(146 161 176);
  direction: rtl;
}

.nav-container {
  display: block;
  justify-content: space-between;
  align-items: center;
  height: 100px;
  position: fixed;
}

.navbar .menu-items {
  display: flex;
  background-color: whitesmoke;
}

.navbar .nav-container li {
  list-style: none;
}

.navbar .nav-container a {
  text-decoration: none;
  color: #000487;
  font-weight: 500;
  font-size: 1.2rem;
  padding: 0.7rem;
}

.navbar .nav-container a:hover{
    font-weight: bolder;
}

.nav-container .checkbox {
  position: absolute;
  display: block;
  height: 32px;
  width: 32px;
  top: 20px;
  /* left: 20px; */
  z-index: 5;
  opacity: 0;
  cursor: pointer;
}

.nav-container .hamburger-lines {
  display: block;
  height: 26px;
  width: 32px;
  position: absolute;
  top: 37px;
  /* left: 20px; */
  z-index: 2;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.nav-container .hamburger-lines .line {
  display: block;
  height: 4px;
  width: 100%;
  border-radius: 10px;
  background: #000487;
}

.nav-container .hamburger-lines .line1 {
  transform-origin: 0% 0%;
  transition: transform 0.4s ease-in-out;
}

.nav-container .hamburger-lines .line2 {
  transition: transform 0.2s ease-in-out;
}

.nav-container .hamburger-lines .line3 {
  transform-origin: 0% 100%;
  transition: transform 0.4s ease-in-out;
}

.navbar .menu-items {
  padding-top: 120px;
  box-shadow: inset 0 0 2000px rgb(255, 255, 255);
  height: 100vh;
  width: 100%;
  transform: translate(-150%);
  display: flex;
  flex-direction: column;
  margin-left: 0px;
  padding-left: 0px;
  transition: transform 0.5s ease-in-out;
  text-align: center;
}

.navbar .menu-items li {
  margin-bottom: 1.2rem;
  font-size: 1.5rem;
  font-weight: 500;
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

.nav-container input[type="checkbox"]:checked ~ .menu-items {
  transform: translateX(0);
}

.nav-container input[type="checkbox"]:checked ~ .hamburger-lines .line1 {
  transform: rotate(45deg);
}

.nav-container input[type="checkbox"]:checked ~ .hamburger-lines .line2 {
  transform: scaleY(0);
}

.nav-container input[type="checkbox"]:checked ~ .hamburger-lines .line3 {
  transform: rotate(-45deg);
}

.nav-container input[type="checkbox"]:checked ~ .logo{
  display: none;
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
  border-radius: 10px;
  padding: 20px;
  margin: 20px;
  width: 250px;
  height: 140px;
  display: inline-block;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.calc-box {
  background-color: #f7f7f7;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  margin: 20px;
  width: 250px;
  height: 100%;
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
    <script language="JavaScript">

      var myDate;
      
      function setDate(datest){
      myDate = datest.value;
      myDate = new Date(myDate);
      }
      
      function FindTime(form){
      var diff;
      today = new Date();
      diff = today.getTime() - myDate.getTime();
      
      console.log(today.getTime());
      console.log(myDate.getTime());
      console.log(diff);
      
      years = Math.floor(diff / 31536000000);
      
      days = Math.floor(diff / 86400000);
      
      H = Math.floor(days % 365);
      
      R = Math.floor(H % 30)
      
      dMy = Math.floor(days % 365);
      
      bM = Math.floor(dMy % 30);
      
      bm = Math.floor(dMy - bM);
      
      month = Math.floor(bm / 30);
      
      
      
      
      form.Fyears.value = years;
      
      form.Fmonth.value = month;
      
      form.FR.value = R;
      
      console.log(years+"/"+month+"/"+R);
      
      }
      
      </script>
      
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
