{{ header('X-UA-Compatible: IE=edge,chrome=1') }}
<!doctype html>
<html class="loading" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- <title>{{ $site->name }} | @yield('title', $site->title)</title> --}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    {{-- <meta name="description" content="@yield('description', $site->description)" /> --}}
    {{-- <meta name="keywords" content="{{ $site->keywords }}" /> --}}
    {{-- <meta name="google-site-verification" content="{{ $site->google_verification }}" /> --}}
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/favicon-57.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/favicon-72.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/favicon-114.png') }}">

    <!--begin::Web font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">


    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/master.css') }}">
    <link rel="stylesheet" href="{{ url(elixir('assets/css/main.css')) }}">

    <style>
        html,
        body {
            overflow: hidden;
        }
    </style>
    @yield('css')
</head>

<body class="vertical-layout 1-column blank-page admin-site-2 cap__site" data-open="click"
    data-menu="vertical-menu-modern" data-col="1-column">

    <!-- BEGIN: Content-->
    <div class="content__page">
        <div class="content__alert">
            @if (Session::has('message_error'))
                <div class="alert alert-danger center">{{ Session::get('message_error') }}</div>
            @elseif (Session::has('message_success'))
                <div class="alert alert-success center">{{ Session::get('message_success') }}</div>
            @endif
        </div>
        <div class="section__register">
            <div class="left__section">
                <div class="content__logo-information">
                    <a href="{{ url('inicio') }}">
                        {{-- <img src="{{ asset('assets/admin/img/login.png') }}" alt="logo"> --}}
                        <img src="{{ asset('assets/img/logo.png') }}" alt="logo">
                    </a>
                </div>
            </div>
            <div class="right__section">
                <div class="scroll__int-section">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- END: Footer-->
    <script src="{{ asset('assets/admin/scripts/admin-2.js') }}"></script>
    <script src="{{ asset('assets/admin/scripts/master.js') }}"></script>

    @yield('script')
</body>

</html>
