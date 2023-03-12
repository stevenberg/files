@isset($presenter)
  @if ($presenter->breadcrumbs()->isNotEmpty())
    <ul class="cluster">
      @foreach ($presenter->breadcrumbs() as $breadcrumb)
        <li class="cluster">
          <a href="{{ $breadcrumb->url }}">
            {{ $breadcrumb->name }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif
@else
  <ul class="cluster">
    <li class="cluster">
      <a href="{{ route('home') }}">
        Files
      </a>
    </li>
  </ul>
@endif
