@extends('layouts.app')

@section('title', 'Sign In')
@section('main-class', 'stack-large stack')

@section('main')
  <section class="stack align-items-center">
    <x-form :action="route('login')" method="post" class="stack narrow-form">
      <x-form.input name="email">
        <x-form.input.label/>
        <x-form.input.text type="email" autocomplete="email"/>
      </x-form.input>
      <x-form.input name="password">
        <x-form.input.label/>
        <x-form.input.text type="password" autocomplete="current-password"/>
      </x-form.input>
      <p class="stack-small">
        <a href="{{ route('password.request') }}">
          Forgot your password?
        </a>
      </p>
      <x-form.input name="remember">
        <x-form.input.label>
          <x-form.input.checkbox :checked="true"/>
          Remember me
        </x-form.input.label>
      </x-form.input>
      <button>
        Sign in
      </button>
    </x-form>
  </section>
@endsection
