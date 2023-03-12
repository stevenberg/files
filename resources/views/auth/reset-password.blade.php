@extends('layouts.app')

@section('title', 'Password Reset')
@section('main-class', 'stack-large stack')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('password.update')" method="post" class="stack auth-form">
      <x-form.input name="email">
        <x-form.input.label/>
        <x-form.input.text type="email" autocomplete="email"/>
      </x-form.input>
      <x-form.input name="password">
        <x-form.input.label/>
        <x-form.input.text type="password" autocomplete="new-password"/>
      </x-form.input>
      <x-form.input name="password_confirmation">
        <x-form.input.label>
          Confirm password
        </x-form.input.label>
        <x-form.input.text type="password" autocomplete="new-password"/>
      </x-form.input>
      <button>
        <span>Update password</span>
      </button>
      <input type="hidden" name="token" value="{{ request()->route('token') }}">
    </x-form>
  </section>
@endsection
