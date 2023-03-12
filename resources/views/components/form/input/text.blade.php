@aware([
  'name',
  'model' => null,
])
@props(['type' => 'text'])
<input {{ $attributes->class(['stack-small'])->merge([
  'type' => $type,
  'name' => $name,
  'id' => $name,
  'value' => old($name, optional($model)->$name),
]) }}>
