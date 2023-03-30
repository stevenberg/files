@extends('layouts.app')

@section('title', 'Trash')

@section('actions')
  <x-form :action="route('trash.update')" method="put">
    <button>
      <x-icon name="trash-xmark"/>
      Empty trash
    </button>
  </x-form>
@endsection

@section('main')
  <table>
    @foreach ($presenter->items as $item)
      <tr>
        <td>
          <x-icon :name="$item->icon" class="color-blue"/>
          {{ $item->name }}
        </td>
        <td>
          <x-form :action="$item->restoreRoute" method="post">
            <button>
              Restore
            </button>
          </x-form>
        </td>
        <td>
          <x-form :action="$item->destroyRoute" method="delete">
            <button class="button-red">
              Delete
            </button>
          </x-form>
        </td>
      </tr>
    @endforeach
  </table>
@endsection
