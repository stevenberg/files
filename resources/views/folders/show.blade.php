@extends('layouts.app')

@section('title', $presenter->name)

@section('main')
  <main class="stack-large">
    <ul class="cluster listing">
      @each('folders.folder', $presenter->folders, 'folder')
      @each('entries.entry', $presenter->entries, 'entry')
    </ul>
  </main>
@endsection
