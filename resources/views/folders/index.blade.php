@extends('layouts.app')

@section('heading', $presenter->name)

@section('main')
  <ul class="cluster listing">
    @each('folders.folder', $presenter->folders, 'folder')
  </ul>
@endsection
