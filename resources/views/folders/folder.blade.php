@can('view', $folder)
  <li>
    <a href="{{ route('folders.show', $folder) }}">
      <figure class="center stack">
        <x-icon name="folder" size="10x" color="blue"/>
        <figcaption class="stack-small">
          {{ $folder->name }}
        </figcaption>
      </figure>
    </a>
  </li>
@endcan
