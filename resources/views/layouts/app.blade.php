<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  {{-- Fuentes (títulos: Fraunces, texto: Inter) --}}
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  {{-- Vite: CSS + JS (si tu CSS ya se importa en app.js, puedes quitar resources/css/app.css) --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Utilidades y fix de z-index para Flatpickr --}}
  <style>
    [x-cloak]{display:none!important}
    .flatpickr-calendar{ z-index:2147483647 !important; }
  </style>

  {{-- Alpine por CDN (no lo dupliques en app.js) --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Datepicker (Flatpickr) - tema Airbnb + ES --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
  <script defer src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
</head>
<body class="font-sans antialiased bg-sand-100 text-stone-800">
  <div class="min-h-screen">
    {{-- Navbar --}}
    @include('layouts.navigation')

    {{-- Encabezado opcional --}}
    @isset($header)
      <header class="border-b border-amber-100 bg-sand-100/60">
        <div class="max-w-[1200px] mx-auto py-6 px-4 sm:px-6">
          {{ $header }}
        </div>
      </header>
    @endisset

    {{-- Contenido --}}
    <main class="relative">
      @yield('content')
    </main>
  </div>
</body>
</html>
