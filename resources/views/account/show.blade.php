@extends('layouts.app')

@section('title', 'Account')
@section('main-class', 'stack-large stack')

@section('main')
  @if (session('status') === 'two-factor-authentication-enabled')
    <section class="stack align-items-center">
      <div>
        {!! auth()->user()->twoFactorQrCodeSvg() !!}
      </div>
      <x-form :action="route('two-factor.confirm')" method="post" class="stack auth-form">
        <x-form.input name="code">
          <x-form.input.label/>
          <x-form.input.text/>
        </x-form.input>
        <button>
          Confirm
        </button>
      </x-form>
    </section>
  @else
    <section class="stack align-items-center">
      @if (auth()->user()->hasEnabledTwoFactorAuthentication())
        <x-form :action="route('two-factor.disable')" method="delete" class="auth-form">
          <button>
            Disable 2FA
          </button>
        </x-form>
      @else
        <x-form :action="route('two-factor.enable')" method="post" class="auth-form">
          <button>
            Enable 2FA
          </button>
        </x-form>
      @endif
      <x-form :action="route('user-profile-information.update')" method="put" :model="auth()->user()" validation="updateProfileInformation" class="stack-large stack auth-form">
        <x-form.input name="name">
          <x-form.input.label/>
          <x-form.input.text autocomplete="name"/>
        </x-form.input>
        <x-form.input name="email">
          <x-form.input.label/>
          <x-form.input.text autocomplete="email"/>
        </x-form.input>
        <button>
          <span>Update profile</span>
        </button>
      </x-form>
      <x-form :action="route('user-password.update')" method="put" validation="updatePassword" class="stack-large stack auth-form">
        <x-form.input name="current_password">
          <x-form.input.label/>
          <x-form.input.text type="password" autocomplete="current-password"/>
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
          Update password
        </button>
      </x-form>
    </section>
  @endif
@endsection
