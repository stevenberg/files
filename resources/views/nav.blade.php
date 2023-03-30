<nav class="menu cluster margin-inline-start-auto align-self-center align-items-center">
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
    @can('admin')
      <a href="{{ route('trash.show') }}">
        <x-icon name="trash"/>
        <span>Trash</span>
      </a>
    @endcan
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
