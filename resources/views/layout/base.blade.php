<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} Booking System</title>

        <link rel="stylesheet" type="text/css" href="/css/app.css">

        <script defer src="https://use.fontawesome.com/releases/v5.0.0/js/all.js"></script>
    </head>
    <body>
        @section('nav')
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="/">{{ config('app.name') }}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarColor01">
                    @if (!Auth::guest())
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item @if (strstr('/', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('welcome') }}">Home</a>
                        </li>
                        <li class="nav-item @if (strstr('planning', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('planning') }}">Planning</a>
                        </li>
                        <li class="nav-item @if (strstr('kamers', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('rooms') }}">Kamers</a>
                        </li>
                        <li class="nav-item @if (strstr('extras', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('extra') }}">Extra's</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/logout') }}"
                                onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                    <form class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" placeholder="Zoek boeking..." type="text">
                        <button class="btn btn-secondary my-2 my-sm-0" type="submit">Zoek</button>
                    </form>
                    @endif
                </div>
            </nav>
        @show

        <div class="container">
            @yield('content')
        </div>

        <footer class="text-center mb-3"><hr />by henri</footer>

        <script type="text/javascript" src="/js/app.js" ></script>
    </body>
</html>
