@can('view', $entry->model)
  <li>
    <x-link :href="route($entry->route, ['folder' => $entry->folder, 'entry' => $entry->model])" :tab="$entry->newTab">
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
    </x-link>
  </li>
@endcan
