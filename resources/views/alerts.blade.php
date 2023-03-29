@if (App\status_message())
  <p class="alert bg-blue color-blue">
    {{ App\status_message() }}
  </p>
@endif
@if (App\status_message('success'))
  <p class="alert bg-blue color-blue">
    {{ App\status_message('success') }}
  </p>
@endif
@if (App\status_message('failure'))
  <p class="alert bg-red color-red">
    {{ App\status_message('failure') }}
  </p>
@endif
