@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'ogImage' => null,
])
@php
    $defaultTitle = __('messages.NA');
    $pageTitle = ($title && trim($title) !== trim($defaultTitle)) ? $title . ' - ' . $defaultTitle : $defaultTitle;
    $pageDesc = $description ?? (app()->getLocale() === 'ar' 
        ? 'الموقع الرسمي لزمالة المدمنين المجهولين في مصر (NA Egypt). نبحث عن التعافي ونقدم الدعم والمساعدة والدليل الكامل لمواعيد وأماكن الاجتماعات في كافة المحافظات.'
        : 'Official website of Narcotics Anonymous Egypt (NA Egypt). We offer recovery, support, meeting directories, and resources to help any addict stop using drugs.');
    $pageKeywords = $keywords ?? 'زمالة المدمنين المجهولين, NA Egypt, تعافي, اجتماع مدمنين مجهولين, Narcotics Anonymous, مصر, علاج الإدمان';
    $pageImage = $ogImage ?? asset('assets/images/na-logo32.webp');
    $currentUrl = url()->current();
    $locale = app()->getLocale();
@endphp
<!doctype html>
<html lang="{{ $locale }}" dir="{{ $direction ?? 'rtl' }}">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $pageTitle }}</title>
  <meta name="description" content="{{ $pageDesc }}">
  <meta name="keywords" content="{{ $pageKeywords }}">
  <meta name="robots" content="index, follow">

  <!-- Preconnect resource hints -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://www.googletagmanager.com">

  <!-- Canonical & Alternate Language Links -->
  <link rel="canonical" href="{{ $currentUrl }}" />
  <link rel="alternate" hreflang="ar" href="{{ $currentUrl }}?lang=ar" />
  <link rel="alternate" hreflang="en" href="{{ $currentUrl }}?lang=en" />
  <link rel="alternate" hreflang="x-default" href="{{ $currentUrl }}" />

  <!-- Open Graph / Facebook / WhatsApp -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ $currentUrl }}">
  <meta property="og:title" content="{{ $pageTitle }}">
  <meta property="og:description" content="{{ $pageDesc }}">
  <meta property="og:image" content="{{ $pageImage }}">
  <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_EG' : 'en_US' }}">
  <meta property="og:site_name" content="NA Egypt">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $pageTitle }}">
  <meta name="twitter:description" content="{{ $pageDesc }}">
  <meta name="twitter:image" content="{{ $pageImage }}">

  <link rel="icon" href="{{ asset('assets/images/na-logo32.webp') }}" type="image/webp" />

  <!-- Include common styles -->
  @vite(['resources/js/app.js', 'resources/css/app.css'])
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://www.google.com/recaptcha/api.js"></script>
  <script src="{{ asset('assets/js/frontend.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/frontend.css') }}?v={{ filemtime(public_path('assets/css/frontend.css')) }}" />
  <script src="{{ asset('assets/js/driver.js.iife.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/driver.css') }}"/>
  <!-- Include RTL CSS dynamically -->
  @if(($direction ?? 'rtl') === 'rtl')
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
<!-- Organization JSON-LD Schema -->
<script type="application/ld+json">
{
  "{{ '@' }}context": "https://schema.org",
  "{{ '@' }}type": "NGO",
  "name": "Narcotics Anonymous Egypt",
  "alternateName": "زمالة المدمنين المجهولين بمصر",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('assets/images/na-logo32.webp') }}",
  "sameAs": [
    "https://www.facebook.com/OfficialNAEgyPage",
    "https://www.instagram.com/narcoticsanonymousegy",
    "https://www.tiktok.com/@narcoticsanonymousegypt"
  ]
}
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-TX958298Y6" onerror="this.onerror=null;"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TX958298Y6', { 'transport_type': 'beacon' });
</script>
  @livewireStyles
</head>
  <body class="hanken-grotesk {{ $direction ?? 'rtl' }}">
    <x-frontend.nav-bar />
      <div class="container-fluid px-3 px-md-5" style="max-width: 1280px; display: block !important; flex-direction: unset !important; margin: 0 auto;">
        <main class="mt-4 w-100" style="min-height: 100vh;">
          {{$slot}}
        </main>
      </div>
    <x-frontend.footer />
    @livewireScripts
    <script>
        // Patch Livewire's update URI to include locale prefix.
        // Livewire reads data-update-uri lazily (on user interaction), so patching
        // it after the script tag loads but before any click works perfectly.
        (function() {
            var el = document.querySelector('script[data-update-uri]');
            if (el) {
                el.setAttribute('data-update-uri', '/{{ app()->getLocale() }}/livewire/update');
            }
        })();
    </script>
  </body>
</html>
