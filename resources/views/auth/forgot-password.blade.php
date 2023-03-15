@extends('layouts.app')

@section('title', 'Password Reset')
@section('main-class', 'stack-large stack')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('password.email')" method="post" class="stack narrow-form">
      <x-form.input name="email">
        <x-form.input.label/>
        <x-form.input.text type="email" autocomplete="email"/>
      </x-form.input>
      <button>
        Send password reset
      </button>
    </x-form>
  </section>
@endsection
