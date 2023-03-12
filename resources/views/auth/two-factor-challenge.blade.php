
@extends('layouts.app')

@section('title', 'Two Factor')
@section('main-class', 'stack-large stack align-items-center')

@section('main')
  <x-form :action="route('two-factor.login')" method="post" class="stack auth-form">
    <x-form.input name="code">
      <x-form.input.label>
        2FA code
      </x-form.input.label>
      <x-form.input.text/>
    </x-form.input>
    <button>
      Sign in
    </button>
  </x-form>
@endsection
