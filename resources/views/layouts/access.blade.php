<!DOCTYPE html>
<html lang="en" >
    <!-- begin::Head -->
    <head>
        <meta charset="utf-8" />
        <title>
            Login
        </title>
        <meta name="description" content="Latest updates and statistic charts">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--begin::Web font -->
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
        <script>
          WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
        </script>
        <!--end::Web font -->
        <link rel="stylesheet" href="{{ url(elixir("assets/css/template.css")) }}">
        <link rel="stylesheet" href="{{ url(elixir("assets/css/main.css")) }}">
    </head>
    <!-- end::Head -->
    <!-- end::Body -->
    <body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default main-site">
        <!-- begin:: Page -->
        <div class="fondo-body">
            <div class="m-grid m-grid--hor m-grid--root m-page page-login">
                <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--singin m-login--2 m-login-2--skin-2" id="m_login">
                    <div class="m-grid__item m-grid__item--fluid    m-login__wrapper">
                        <div class="m-login__container @yield('size-class', 'default-width') ">
                            @include('helpers.alert', ['container'=>false])
                            <div class="m-login__logo">
                                <a href="#">
                                    <img src="{{ asset('assets/app/media/img/logos/todotix-logo.png') }}">
                                </a>
                            </div>
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:: Page -->
        <script type="text/javascript" src="{{ url(elixir("assets/js/template.js")) }}"></script>
        <!-- <script type="text/javascript" src="{{ url(elixir("assets/js/template-login.js")) }}"></script>-->
    </body>
    <!-- end::Body -->
</html>
