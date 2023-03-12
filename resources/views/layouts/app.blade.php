<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
      {{ config('app.name') }} @hasSection('title') - @yield('title') @endif
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
  </head>
  <body class="stack">
    <div class="cluster">
      @include('header')
      @include('nav')
    </div>
    <main class="@yield('main-class', 'stack-large')">
      @include('alerts')
      @yield('main')
    </main>
    <x-sprite/>
  </body>
</html>
