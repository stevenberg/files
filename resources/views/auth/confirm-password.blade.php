@extends('layouts.app')

@section('title', 'Confirm Password')
@section('main-class', 'stack-large stack align-items-center')

@section('main')
  <x-form :action="route('password.confirm')" method="post" class="stack narrow-form">
    <x-form.input name="password">
      <x-form.input.label/>
      <x-form.input.text type="password" autocomplete="current-password"/>
    </x-form.input>
    <button>
      Confirm
    </button>
    @csrf
  </x-form>
@endsection
