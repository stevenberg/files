@aware([
  'name',
  'model' => null,
])
@props(['type' => 'file'])
<input {{ $attributes->class(['stack-small'])->merge([
  'type' => $type,
  'name' => $name,
  'id' => $name,
]) }}>
