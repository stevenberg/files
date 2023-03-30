@extends('layouts.app')

@section('heading', $presenter->name)

@section('actions')
  @include('folders.actions')
@endsection

@section('main')
  <ul class="cluster listing">
    @each('folders.folder', $presenter->folders, 'folder')
  </ul>
@endsection
