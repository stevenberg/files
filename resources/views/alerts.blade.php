@if (App\status_message())
  <p class="alert">
    {{ App\status_message() }}
  </p>
@endif
