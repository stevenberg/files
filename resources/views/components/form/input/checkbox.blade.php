@aware([
  'name',
  'model' => null,
])
@props([
  'checked' => false,
])
@php
  if (!is_bool($checked)) {
    $checked = $checked === '1' ? true : false;
  }

  $modelValue = null;
  if (is_bool(optional($model)->$name)) {
    $modeValue = optional($model)->$name ? '1' : '0';
  }
  $old = old($name, $modelValue) ?? ($checked ? '1' : '0');
@endphp
<input type="hidden" name="{{ $name }}" value="0">
<input {{ $attributes->merge([
  'name' => $name,
  'id' => $name,
  'type' => 'checkbox',
  'value' => '1',
  'checked' => $old === '1',
]) }}>
