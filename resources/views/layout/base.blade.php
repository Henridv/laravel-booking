<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@hasSection('title')@yield('title') - @endif{{ config('app.name') }} Booking System</title>

        <link rel="stylesheet" type="text/css" href="/css/app.css">
        <link rel="stylesheet" type="text/css" href="/css/print.css">

        <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/regular.js" integrity="sha384-t7yHmUlwFrLxHXNLstawVRBMeSLcXTbQ5hsd0ifzwGtN7ZF7RZ8ppM7Ldinuoiif" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>
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
                        @can('edit.all')
                        <li class="nav-item @if (strstr('kamers', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('rooms') }}">Kamers</a>
                        </li>
                        <li class="nav-item @if (strstr('extras', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('extra') }}">Extra's</a>
                        </li>
                        <li class="nav-item @if (strstr('stats', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('stats') }}">Statistieken</a>
                        </li>
                        @endcan
                        @can('access.admin')
                        <li class="nav-item @if (strstr('admin', Request::path())) active @endif">
                            <a class="nav-link" href="{{ route('admin') }}">Admin</a>
                        </li>
                        @endcan
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
                    <form action="{{ route('booking.search') }}" method="GET" class="form-inline my-2 my-lg-0">
                        {{-- {{ csrf_field() }} --}}
                        <input class="form-control mr-sm-2 search__input" placeholder="Zoek boeking... (min. 3 karakters)" type="text" name="search" value="{{ app('request')->input('search') }}" pattern=".{3,}" title="min. 3 karakters" required>
                        <button class="btn btn-secondary my-2 my-sm-0" type="submit">Zoek</button>
                    </form>
                    @endif
                </div>
            </nav>
        @show

        <div class="@isset($planning_table) container-fluid @else container @endisset">
            @yield('content')
        </div>

        <footer class="text-center mb-3"><hr />by henri</footer>

        <script type="text/javascript" src="/js/app.js" ></script>
    </body>
</html>
