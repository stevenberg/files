@auth
  <section class="cluster">
    @can('create', [App\Models\Entry::class, $presenter->folder])
      <x-form :action="route('folders.entries.create', $presenter->folder)">
        <button>
          <x-icon name="file-plus"/>
          New file
        </button>
      </x-form>
    @endcan
    @can('create', App\Models\Folder::class)
      <x-form :action="route('folders.create')">
        <button>
          <x-icon name="folder-plus"/>
          New folder
        </button>
        <input type="hidden" name="folder_id" value="{{ $presenter->folder->id }}">
      </x-form>
    @endcan
    @can('delete', $presenter->folder)
      <x-form :action="route('folders.destroy', $presenter->folder)" method="delete">
        <button>
          <x-icon name="trash"/>
          Trash
        </button>
      </x-form>
    @endcan
  </section>
@endauth
