@extends('layouts.app')

@section('title', $presenter->title)
@section('heading', 'Edit')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('folders.update', $presenter->folder)" method="put" :model="$presenter->folder" class="stack narrow-form">
      <x-form.input name="name">
        <x-form.input.label/>
        <x-form.input.text/>
      </x-form.input>
      <x-form.input name="restricted">
        <x-form.input.label>
          <x-form.input.checkbox :checked="$presenter->folder->restricted"/>
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
              <x-form.input.checkbox :checked='old("users.{$index}.selected", $presenter->viewers->contains($user->id))'/>
              {{ $user->name }}
            </x-form.input.label>
          </x-form.input>
        @endforeach
      </fieldset>
      <button>
        Update
      </button>
    </x-form>
  </section>
@endsection
