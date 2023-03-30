@php
  use App\Values\Thumbnails\Shape;
@endphp

@extends('layouts.app')

@section('title', $presenter->name)
@section('main-class', 'stack-large stack align-items-center')

@section('actions')
  <section class="cluster align-items-center">
    <a href="{{ route('folders.files.show', ['folder' => $presenter->folder, 'entry' => $presenter->entry]) }}" target="_blank">
      <x-icon name="file"/>
      <span>View file</span>
    </a>
    <x-form :action="route('folders.entries.destroy', ['folder' => $presenter->folder, 'entry' => $presenter->entry])" method="delete">
      <button>
        <x-icon name="trash"/>
        Trash
      </button>
    </x-form>
  </section>
@endsection

@section('main')
  <section class="entry">
    <img
      srcset="{{ $presenter->thumbnailSrcset }}"
      src="{{ $presenter->thumbnailSrc }}"
      width="{{ $presenter->thumbnailWidth }}"
      height="{{ $presenter->thumbnailHeight }}"
      sizes="(max-width: 524px) 100vw, 500px"
      alt="{{ $presenter->name }}"
    >
  </section>
@endsection
