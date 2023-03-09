@extends('layouts.app')

@section('main')
  <main class="stack-large">
    <ul class="cluster listing">
      @each('folders.folder', $presenter->folders, 'folder')
    </ul>
  </main>
@endsection
