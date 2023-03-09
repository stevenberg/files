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
    <header class="cluster">
      @if ($presenter->breadcrumbs()->isNotEmpty())
        <ul class="cluster breadcrumbs">
          @foreach ($presenter->breadcrumbs() as $breadcrumb)
            <li class="cluster">
              <a href="{{ $breadcrumb->url }}">
                {{ $breadcrumb->name }}
              </a>
            </li>
          @endforeach
        </ul>
      @endif
      <h1 class="cluster">
        {{ $presenter->name }}
      </h1>
    </header>
    @yield('main')
    <x-sprite/>
  </body>
</html>
