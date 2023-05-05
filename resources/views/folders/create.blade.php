@extends('layouts.app')

@section('title', 'New Folder')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('folders.store')" method="post" class="stack narrow-form">
      <x-form.input name="name">
        <x-form.input.label/>
        <x-form.input.text/>
      </x-form.input>
      <x-form.input name="restricted">
        <x-form.input.label>
          <x-form.input.checkbox/>
          Restricted
        </x-form.input.label>
      </x-form.input>
      <fieldset class="stack">
        <legend  class="stack-small">
          Viewers
        </legend>
        @foreach ($presenter->users as $index => $user)
          <input type="hidden" name="users[{{ $index }}][id]" value="{{ $user->id }}"/>
          <x-form.input name="users[{{ $index }}][selected]" class="stack-small">
            <x-form.input.label>
              <x-form.input.checkbox :checked='old("users.{$index}.selected")'/>
              {{ $user->name }}
            </x-form.input.label>
          </x-form.input>
        @endforeach
      </fieldset>
      <button>
        Add folder
      </button>
      <input type="hidden" name="folder_id" value="{{ $presenter->folder->id }}"/>
    </x-form>
  </section>
@endsection
