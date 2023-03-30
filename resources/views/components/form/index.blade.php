@props([
  'method' => 'get',
  'file' => false,
  'model' => null,
  'validation' => null,
])
@php
  $realMethod = $method;
  $method = in_array($realMethod, ['get', 'post']) ? $realMethod : 'post';
  $additionalAttributes = [
    'method' => $method,
  ];
  if ($file) {
    $additionalAttributes['enctype'] = 'multipart/form-data';
  }
@endphp
<form {{ $attributes->merge($additionalAttributes) }}>
  {{ $slot }}
  @if ($method === 'post')
    @csrf
  @endif
  @if ($realMethod !== $method)
    @method($realMethod)
  @endif
</form>
