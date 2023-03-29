@extends('layouts.app')

@section('title', 'New Folder')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('folders.store')" method="post" class="stack narrow-form">
      <x-form.input name="name">
        <x-form.input.label/>
        <x-form.input.text/>
      </x-form.input>
      <button>
        Add folder
      </button>
      <input type="hidden" name="folder_id" value="{{ $presenter->folder->id }}"/>
    </x-form>
  </section>
@endsection
