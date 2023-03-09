@php
  use App\Values\Thumbnails\Shape;
@endphp
<picture {{ $attributes }}>
  <source srcset="{{ $srcset(Shape::Square) }}" sizes="250px" media="(min-width: 525px)" width="1" height="1">
  <source srcset="{{ $srcset(Shape::Original) }}" sizes="100vw" width="{{ $width }}" height="{{ $height }}">
  <img src="{{ $src }}" alt="{{ $name }} thumbnail" loading="lazy" width="{{ $width }}" height="{{ $height }}">
</picture>
