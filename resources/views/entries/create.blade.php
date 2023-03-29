@extends('layouts.app')

@section('title', 'New File')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('folders.entries.store', $presenter->folder)" method="post" :file="true" class="stack narrow-form">
      <x-form.input name="name">
        <x-form.input.label/>
        <x-form.input.text/>
      </x-form.input>
      <x-form.input name="file">
        <x-form.input.file/>
      </x-form.input>
      <button>
        Upload file
      </button>
    </x-form>
  </section>
@endsection
