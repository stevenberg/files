@auth
  @can('create', [App\Models\Entry::class, $presenter->folder])
    <a href="{{ route('folders.entries.create', $presenter->folder) }}">
      <x-icon name="file-plus"/>
      New file
    </a>
  @endcan
  @can('create', App\Models\Folder::class)
    <a href="{{ route('folders.create', ['folder_id' => $presenter->folder->id]) }}">
      <x-icon name="folder-plus"/>
      New folder
    </a>
  @endcan
  @can('update', $presenter->folder)
    <a href="{{ route('folders.edit', $presenter->folder) }}">
      <x-icon name="pen-to-square"/>
      Edit
    </a>
  @endcan
  @can('delete', $presenter->folder)
    <x-form :action="route('folders.destroy', $presenter->folder)" method="delete">
      <button>
        <x-icon name="trash"/>
        Trash
      </button>
    </x-form>
  @endcan
@endauth
