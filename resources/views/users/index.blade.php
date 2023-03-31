@extends('layouts.app')

@section('title', 'Users')

@section('main')
  <table>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Actions</th>
    </tr>
    @foreach ($presenter->users as $user)
      <tr>
        <td>
          {{ $user->name }}
        </td>
        <td>
          <a href="mailto:{{ $user->email }}">
            {{ $user->email }}
          </a>
        </td>
        <td>
          {{ $user->role }}
        </td>
        <td>
          <div class="cluster inline-size-max-content">
            @can('delete', $user->model)
              <x-form :action="route('users.destroy', $user->model)" method="delete">
                <button>
                  <x-icon name="trash"/>
                  Trash
                </button>
              </x-form>
            @endcan
            @if ($user->isPending)
              <x-form :action="route('users.update', $user->model)" method="put">
                <button>
                  <x-icon name="user-check"/>
                  Approve
                </button>
              </x-form>
            @endif
          </div>
        </td>
      </tr>
    @endforeach
  </table>
@endsection
