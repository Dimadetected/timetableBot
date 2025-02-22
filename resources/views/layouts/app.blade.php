<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <div class="row">
        <div class="col-12">
            <nav class="navbar navbar-expand-lg navbar-light bg-light ">
                <a class="navbar-brand" href="{{route('admin.timetable.index')}}">{{env('APP_NAME')}}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
                        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                    <div class="navbar-nav ">
                        @if(auth()->check())
                            @if(auth()->user()->stCheck())
                                <a class="nav-link" href="{{route('admin.users.index')}}">Пользователи</a>
                                <a class="nav-link" href="{{route('admin.lectures.index')}}">Предметы</a>
                                <a class="nav-link" href="{{route('admin.templates.index')}}">Шаблоны</a>
                            @endif
                            @if(auth()->user()->id == 23)
                                <a class="nav-link" href="{{route('admin.courses.index')}}">Курсы</a>
                                <a class="nav-link" href="{{route('admin.faculties.index')}}">Факультеты</a>
                                <a class="nav-link" href="{{route('admin.groups.index')}}">Группы</a>
                                <a class="nav-link" href="{{route('admin.usersType.index')}}">Типы юзеров</a>
                            @endif
                        @endif
                        @guest
                            <a class="nav-link " href="{{ route('login') }}">{{ __('Login') }}</a>
                            @if (Route::has('register'))
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        @else
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        @endguest
                    </div>
                </div>
            </nav>
        </div>
    </div>


    <div class="col-12 p-3 h3">
        <div class="row">
            <div class="col-8">
                @yield('h1')
            </div>
            <div class="col-4 text-right">
                @yield('actions')
            </div>
        </div>
    </div>
    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>
@yield('script')
</html>
