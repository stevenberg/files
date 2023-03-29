@if (App\status_message())
  <p class="alert">
    {{ App\status_message() }}
  </p>
@endif
@if (App\status_message('success'))
  <p class="alert">
    {{ App\status_message('success') }}
  </p>
@endif
