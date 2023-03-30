<nav class="cluster margin-inline-start-auto align-self-center align-items-center">
  @guest
    {{-- <a href="{{ route('register') }}">
      <x-icon name="user-plus"/>
      <span>Register</span>
    </a> --}}
    <a href="{{ route('login') }}">
      <x-icon name="right-to-bracket"/>
      <span>Sign in</span>
    </a>
  @endguest
  @auth
    @if (isset($presenter) && isset($presenter->folder))
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
    @endif
    <a href="{{ route('account.show') }}">
      <x-icon name="user"/>
      <span>{{ auth()->user()->name }}</span>
    </a>
    <x-form :action="route('logout')" method="post">
      <button>
        <x-icon name="right-from-bracket"/>
        <span>Sign out</span>
      </button>
    </x-form>
  @endauth
</nav>
