<!DOCTYPE HTML>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title>{{ $site->name }} | @yield('title', $site->title)</title>
        <meta name="description" content="@yield('description', $site->description)" />
        <meta name="keywords" content="{{ $site->keywords }}" />
        <meta name="google-site-verification" content="{{ $site->google_verification }}" />
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,300i,400,400i,500,500i,700,900" rel="stylesheet">
        <link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" type="image/x-icon">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/favicon-57.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/favicon-72.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/favicon-114.png') }}">
        <link rel="stylesheet" href="{{ url(elixir("assets/css/template.css")) }}">
        <link rel="stylesheet" href="{{ url(elixir("assets/css/main.css")) }}">
        <!--[if lt IE 9]>  
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>  
        <![endif]-->  
        @yield('css')
        {!! $site->analytics !!}
    </head>
    <body class="main-site ctt-menu_mobile" id="bar_mobile">


        <main class="body-bg">
            @yield('header')

        </main>

        <div class="scroll-top not-visible">
            <i class="fa fa-angle-up"></i>
        </div>



        <!-- Scripts -->
        <script src="{{ url(elixir("assets/js/template.js")) }}"></script>

        @include('business::scripts.search-product-bridge-js')

        <script>
            var myNS = new (function() {
                $('.bar_mobile').on('click', function() {
                    $('.bar_mobile.active').not(this).removeClass('active');
                    $id = "#" + $(this).toggleClass('active').attr('data-id');
                    $('.ctt-menu_mobile:not(' + $id + ')');
                    $($id).toggleClass('open_menu');
                });
            });
        </script>

        <script>
            $('.title_box').on('click', function(){
                $(this).toggleClass('active_box');
            });
        </script>

        @yield('script')
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5fb7031da1d54c18d8eb7241/1enhekud3';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->
    </body>
</html>