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
        <h1>Register</h1>
        <p>Register your Ringer account by clicking here</p>
        <a href="{{ route('register') }}" class="mybtn2">Register</a>
      </div>
    </div>

    <!-- Right -->
    <div class="right">
      <img src="/img/girl.svg" class="svg">
      <h1>Login</h1>
      <p>
        You can use a test account to test the app:<br><br>
        Email: <span>sample@gmail.com</span><br>
        Password: <span>sample123</span>
      </p>

      <!-- Login form -->
      <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

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
            value="{{ old('email') ? old('email') : 'sample@gmail.com' }}"
            placeholder="Email"
            autocomplete="off"
            spellcheck="false"
            required
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
            value="{{ old('email') ? '' : 'sample123' }}"
            placeholder="Password"
            required
          >
          @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
              <strong>{{ $errors->first('password') }}</strong>
            </span>
          @endif
        </div>

        <!-- Remember Me -->
        <div class="form-group remember-me">
          <input
            class="check-input"
            type="checkbox"
            name="remember"
            id="remember" {{ old('remember') ? 'checked' : '' }}
          >
          <label class="check-label" for="remember">Remember Me</label>
        </div>

        <!-- Submit -->
        <div class="form-group submit-group">
          <button type="submit" class="mybtn1">Login</button>
        </div>

      </form>

    </div>

  </div>
@endsection
