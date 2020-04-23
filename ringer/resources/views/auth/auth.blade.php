<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="icon" href="/img/icon.svg">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Ringer</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
  <link href="{{ asset('app.css') }}" rel="stylesheet">
</head>
<body>

  <div id="mprogress"></div>

  <div id="auth-screen" class="container">
    @yield('content')
  </div>

  <script src="{{ asset('app.js') }}"></script>
</body>
</html>
