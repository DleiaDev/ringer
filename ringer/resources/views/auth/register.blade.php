@extends('auth.auth')

@section('content')
  <!-- Card -->
  <div class="card">

    <!-- Left -->
    <div class="left">
      <img src="/img/inbox.svg" class="svg">
      <div class="logo">
        <i class="fas fa-phone-volume icon"></i>
        <span>Ringer</span>
      </div>
      <div class="main">
        <h1>Login</h1>
        <p>You can login with your Envelope account here.</p>
        <a href="{{ route('login') }}" class="mybtn2">Login</a>
      </div>
    </div>

    <!-- Right -->
    <div class="right">
      <img src="/img/girl.svg" class="svg">
      <h1>Register</h1>

      <form method="POST" action="/register" id="register-form">
        @csrf

        <!-- Name -->
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user icon"></i></span>
          </div>
          <input
            id="name"
            type="text"
            class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
            name="name"
            value="{{ old('name') }}"
            placeholder="Name"
            autocomplete="off"
            spellcheck="false"
            required autofocus
          >
          @if ($errors->has('name'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('name') }}</strong>
            </span>
          @endif
        </div>

        <!-- Email -->
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-envelope icon"></i></span>
          </div>
          <input
            id="email"
            type="email"
            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
            name="email"
            value="{{ old('email') }}"
            placeholder="Email"
            autocomplete="off"
            spellcheck="false"
            required autofocus
          >
          @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('email') }}</strong>
            </span>
          @endif
        </div>

        <!-- Password -->
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-key icon"></i></span>
          </div>
          <input
            id="password"
            type="password"
            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
            name="password"
            value="{{ old('password') }}"
            placeholder="Password"
            required autofocus
          >
          @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('password') }}</strong>
            </span>
          @endif
        </div>

        <!-- Repeat password -->
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-key icon"></i></span>
          </div>
          <input
            id="password-confirm"
            type="password"
            class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
            name="password_confirmation"
            value="{{ old('password_confirmation') }}"
            placeholder="Repeat password"
            required autofocus
          >
          @if ($errors->has('password_confirmation'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('password_confirmation') }}</strong>
            </span>
          @endif
        </div>

        <!-- Submit -->
        <div class="form-group submit-group">
          <button type="submit" class="mybtn1">Register</button>
        </div>

      </form>
    </div>

  </div>
@endsection
