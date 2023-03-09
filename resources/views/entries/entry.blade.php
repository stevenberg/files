<li>
  <a href="{{ route('folders.files.show', ['folder' => $entry->folder, 'entry' => $entry->model])}}" target="_blank">
    <figure class="center stack">
      @if ($entry->thumbnail->exists)
        <x-thumbnail :thumbnail="$entry->thumbnail"/>
      @else
        <x-icon :name="$entry->icon" size="10x" color="blue"/>
      @endif
      <figcaption class="stack-small">
        {{ $entry->name }}
      </figcaption>
    </figure>
  </a>
</li>
