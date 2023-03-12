<header class="cluster breadcrumbs">
  @include('breadcrumbs')
  <h1 class="cluster">
    @hasSection('heading')
      @yield('heading')
    @else
      @yield('title')
    @endif
  </h1>
</header>
