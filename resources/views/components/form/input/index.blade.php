@aware([
  'model' => null,
  'validation' => 'default',
])
@props(['name'])
<div class="stack">
  {{ $slot }}
  @error($name, $validation)
    <p class="stack-small error">
      <x-icon name="triangle-exclamation"/>
      {{ $message }}
    </p>
  @enderror
</div>
