@php
  use App\Values\Thumbnails\Shape;
@endphp

@extends('layouts.app')

@section('title', $presenter->name)

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
  <section class="entry align-self-center">
    @if ($presenter->thumbnail->exists)
      <img
        srcset="{{ $presenter->thumbnailSrcset }}"
        src="{{ $presenter->thumbnailSrc }}"
        width="{{ $presenter->thumbnailWidth }}"
        height="{{ $presenter->thumbnailHeight }}"
        sizes="(max-width: 524px) 100vw, 500px"
        alt="{{ $presenter->name }}"
      >
    @else
      <img src="{{ Vite::asset('resources/images/placeholder.png') }}" width="1" height="1">
    @endif
  </section>
@endsection
