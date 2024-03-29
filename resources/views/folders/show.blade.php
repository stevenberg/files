@extends('layouts.app')

@section('title', $presenter->name)

@section('actions')
  @include('folders.actions')
@endsection

@section('main')
  <ul class="cluster listing">
    @each('folders.folder', $presenter->folders, 'folder')
    @each('entries.entry', $presenter->entries, 'entry')
  </ul>
@endsection
