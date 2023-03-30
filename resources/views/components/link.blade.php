@props([
  'tab' => false,
])
@php
  $additionalAttributes = [];
  if ($tab) {
    $additionalAttributes['target'] = '_blank';
  }
@endphp
<a {{ $attributes->merge($additionalAttributes) }}>
  {{ $slot }}
</a>
