@aware(['name'])
<label {{ $attributes->merge(['for' => $name]) }}>
  @if ($slot->isNotEmpty())
    {{ $slot }}
  @else
    {{ Illuminate\Support\Str::of($name)->headline()->lower()->ucfirst()->toString() }}
  @endif
</label>
