{{ header('X-UA-Compatible: IE=edge,chrome=1') }}
<!doctype html>
<html class="loading" lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    {{-- <title>{{ $site->name }} | @yield('title', $site->title)</title> --}}
    <title>Fempile | Fempile</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="@yield('description', $site->description)" />
    <meta name="keywords" content="{{ $site->keywords }}" />
    <meta name="google-site-verification" content="{{ $site->google_verification }}" />
    @if (!$pdf)
        <link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" type="image/x-icon">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/favicon-57.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/favicon-72.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/favicon-114.png') }}">

        <!--begin::Web font -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    @endif

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/master.css') }}">
    @if (!$pdf)
        <link rel="stylesheet" href="{{ url(elixir('assets/css/main.css')) }}">
    @else
        <style>

        </style>
    @endif
    <style>

    </style>
    @yield('css')
</head>
@if (!$pdf)

    <body
        class="vertical-layout vertical-menu-modern 2-columns @if (request()->segment(2) == 'my-inbox') chat-application @endif navbar-floating footer-static admin-site-2 cap__site dashboard__page "
        data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

        <!-- BEGIN: Main Menu-->
        <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
            <div class="navbar-header bg-black m-0 w-100" >
                <img class="w-100" src="{{ asset('assets/img/admin-logo.png') }}" style="width: 100% !important; object-fit: contain; height: 100%;">
                <!-- <ul class="nav navbar-nav flex-row">
                    <div class="navbar-header py-0" style="height: auto;">
                        <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="../../../html/ltr/vertical-menu-template/index.html">
                        <div class="brand-logo"></div>
                        <h2 class="brand-text mb-0">{{ $site->name }}</h2>
                    </a></li>
            </ul>
                        <br>
                        
                            
                    </div>
                </ul> -->
            </div>
            <div class="shadow-bottom"></div>
            <div class="main-menu-content">
                <ul class="navigation navigation-main" id="main-menu-navigation" style="height:100%; overflow:auto;"
                    data-menu="menu-navigation">
                    @if (auth()->user()->hasRole('admin'))
                        @include('includes.admin-options')
                    @elseif(auth()->user()->hasRole('driver'))
                        @include('includes.driver-options')
                    @elseif(auth()->user()->hasRole('passenger'))
                        @include('includes.passenger-options')
                    @elseif(auth()->user()->hasRole('alcaldia'))
                        @include('includes.alcaldia-options')
                    @elseif(auth()->user()->hasRole('sindicato'))
                        @include('includes.sindicato-options')
                    @elseif(auth()->user()->hasRole('empresa'))
                        @include('includes.empresa-options')
                    @elseif(auth()->user()->hasRole('subadmin'))
                        @include('includes.subadmin-options')
                    @endif

                </ul>
            </div>
        </div>
        <!-- END: Main Menu-->

        <!-- BEGIN: Content-->
        <div class="app-content content">

            <!-- BEGIN: Header-->
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
                <div class="navbar-wrapper">
                    <div class="navbar-container content">
                        <div class="navbar-collapse" id="navbar-mobile">
                            <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                                <ul class="nav navbar-nav">
                                    <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                            class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                                class="ficon feather icon-menu"></i></a></li>
                                </ul>

                            </div>
                            <ul class="nav navbar-nav float-right">
                                @if (auth()->check())
                                    @if (!config('solunes.admin_inbox_disabled'))
                                        <li class="dropdown dropdown-notification nav-item">
                                            <a class="nav-link" href="{{ url('customer-admin/my-inbox') }}"
                                                data-toggle="tooltip" data-placement="top" title="Mensajes">
                                                <i class="ficon feather icon-message-circle"></i><span
                                                    class="badge badge-pill badge-primary badge-up">0</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="dropdown dropdown-user nav-item">
                                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#"
                                            data-toggle="dropdown">
                                            <div class="user-nav d-sm-flex d-none"><span
                                                    class="user-name text-bold-600">{{ auth()->user()->name }}</span><span
                                                    class="user-status">Disponible</span></div>
                                            <span>
                                                @if (auth()->user()->customer->image)
                                                    <img class="round"
                                                        src="{{ \Asset::get_image_path('customer-image', 'normal', auth()->user()->customer->image) }}"
                                                        alt="avatar" height="40" width="40" />
                                                @else
                                                    <img class="round" src="{{ asset('assets/admin/img/user.jpg') }}"
                                                        alt="avatar" height="40" width="40" />
                                                @endif
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                                href="{{ url('account/my-account/1354351278') }}"><i
                                                    class="feather icon-user"></i> Editar Perfil</a>
                                            <!--<a class="dropdown-item" href="#"><i class="feather icon-mail"></i> Inbox</a>
                                  <a class="dropdown-item" href="#"><i class="feather icon-check-square"></i> Tareas</a>
                                  <a class="dropdown-item" href="#"><i class="feather icon-message-square"></i> Chats</a>-->
                                            <div class="dropdown-divider"></div><a class="dropdown-item"
                                                href="{{ url('auth/logout') }}"><i class="feather icon-power"></i>
                                                Cerrar Sesión</a>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- END: Header-->

            @if (request()->segment(2) == 'my-inbox')
                @yield('content')
            @else
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        @if (Session::has('message_error'))
                            <div class="alert alert-danger center">{{ Session::get('message_error') }}</div>
                        @elseif (Session::has('message_success'))
                            <div class="alert alert-success center">{{ Session::get('message_success') }}</div>
                        @endif
                        @yield('content')
                    </div>
                </div>
            @endif
        </div>
        <!-- END: Content-->

        <div class="sidenav-overlay"></div>
        <div class="drag-target"></div>

        <!-- BEGIN: Footer-->
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body" style="margin-left: 35px;">

                <footer class="footer footer-static footer-light">
                    <p class="clearfix blue-grey lighten-2 mb-0"><span
                            class="float-md-left d-block d-md-inline-block mt-25">&copy;
                            {{-- {{ $footer_name . ' ' . date('Y') . ' | ' . $footer_rights }}</span> --}}
                            {{ 'FEMPILE' . ' ' . date('Y') . ' | ' . $footer_rights }}</span>
                        <span class="float-md-right d-none d-md-block">{{ trans('master::layout.developed_by') }} <a
                                href="http://www.solunes.com" target="_blank"><i class="feather icon-terminal"></i>
                                {{ trans('master::layout.developer') }}</a></span>
                        <button class="btn btn-primary btn-icon scroll-top" type="button"><i
                                class="feather icon-arrow-up"></i></button>
                    </p>
                </footer>

            </div>
        </div>
        <!-- END: Footer-->

        <script src="{{ asset('assets/admin/scripts/vendor.js') }}"></script>
        <script src="{{ asset('assets/admin/scripts/admin-2.js') }}"></script>
        <script src="{{ asset('assets/admin/scripts/master.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#field_organization_users').hide();
                $('.btn-outline-danger').attr('onclick',
                    'return confirm("¿Está seguro que desea eliminar este item?")');
            });
        </script>
        @if (request()->segment(3) == 'driver-vehicle')
            <script>
                $(document).ready(function() {
                    var item = {!! $i !!};
                    const modelID = $('#vehicle_model_id')
                    $('#vehicle_brand_id').change(function() {
                        $('#vehicle_model_id').empty();
                        $('#vehicle_model_id').select2()
                        var brand = $(this).val();
                        if (brand == '') {
                            return;
                        }
                        $.ajax({
                            type: 'POST',
                            url: '/process/ajax-fill-models-by-brand',
                            data: {
                                "vehicle_brand_id": brand
                            },
                            success: function(response) {
                                var options =
                                    '<option value="" selected="selected">Seleccione una opción...</option>';
                                for (var i in response['models']) {
                                    if (modelID.val() == response['models'][i]['id']) {
                                        options +=
                                            `<option value="${response['models'][i]['id']}" selected>${response['models'][i]['name']}</option>`
                                    } else {
                                        options +=
                                            `<option value="${response['models'][i]['id']}">${response['models'][i]['name']}</option>`
                                    }

                                }
                                $('#vehicle_model_id').append(options);
                                $('#vehicle_model_id').val(item['vehicle_model_id']);
                                $("#vehicle_model_id").select2();
                            }
                        });
                    });
                    $('#vehicle_brand_id').change();
                });
            </script>
        @endif

        @include('master::scripts.date-js')
        @include('master::scripts.time-js')
        @include('master::scripts/filter-js')
        @include('master::scripts/subadmin-table-js')
        @yield('script')
    </body>
@else

    <body class="admin-site pdf-site">
        <div class="content-wrap pdf-wrap">
            @yield('content')
        </div>
        @yield('script')
    </body>
@endif

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('solunes.google_maps_key') }}&libraries=places"></script>

@if (request()->segment(3) == 'driver')
    <script>
        $('#field_driver_device_code').hide();
        $('#field_qr_image').hide();

        function marketingChange(idDriver, field) {
            window.location.href = `/customer-admin/driver/marketing/${idDriver}?field=${field}`
        }

        function sendEmailLibelula(idDriver) {
            window.location.href = `/customer-admin/driver/send-email-libelula/${idDriver}`
        }

        function assingMeDriver(idDriver) {
            window.location.href = `/customer-admin/driver/assing-me-driver/${idDriver}`
        }
    </script>
@endif

@if (request()->segment(4) == 'view')
    <script>
        $('.btn.btn-primary.mr-1.mb-1.btn-site.waves-effect.waves-light').hide();
    </script>
@endif

@if (request()->segment(3) == 'user')
    <script>
        const url = new URL(window.location.href);
        const valorPassenger = url.searchParams.get("passenger");
        if (valorPassenger === 'true') {
            $('#nav-pasajeros').addClass('active');
            $('#nav-users').removeClass('active');
            $('#field_f_role_user').hide();
            $('#field_f_city_id').hide();
            $('.filter_button.col-sm-3').hide();
            $('.content-header-title.float-left.mb-0').text('Pasajeros');
            $('.breadcrumb-item.active').text('Pasajeros');
            $('.card-header').html(`
            <h3 class="">
                Pasajeros ( 14 ) | <a href="/customer-admin/model-list/user?button=&amp;f_role_user%5B%5D=3&amp;passenger=true&amp;search=1&amp;download-excel=true"><i class="fa fa-download"></i> Descargar</a> | <a href="/customer-admin/model-list/user?button=&amp;f_role_user%5B%5D=3&amp;passenger=true&amp;search=1&amp;download-pdf=true"><i class="fa fa-download"></i> Descargar en PDF</a> | 
                <a class="admin_link" href="/customer-admin/create-passenger"><i class="fa fa-plus"></i> Crear</a> | <a href="/customer-admin/model-list/user?button=&amp;f_role_user%5B%5D=3&amp;passenger=true&amp;search=1&amp;view-trash=true"><i class="fa fa-trash"></i> Abrir Basurero</a>
                </h3>
            `);

        }
    </script>
@endif

@include('includes.filter_city')

<script>
    if (document.getElementById('field_f_city_id') !== null) {
        const filtroCity = $('#field_f_city_id');
        const btnSearch = $('.filter_button.col-sm-3');
        const filtroCustom = document.getElementById('filtro-city');
        filtroCustom.style.display = 'block'
        filtroCity.html(filtroCustom);
        btnSearch.hide();

        const filtroRoles = $('#field_f_role_user');
        filtroRoles.hide();
    }
</script>
@if (request()->segment(4) == 'create' || request()->segment(4) == 'edit' || request()->segment(4) == 'view')
    <script>
        $('#filtro-city').hide();
    </script>
@endif



<script>
    function addButtonsForRotareImage(field, nameAttribute, node, id, folder) {
        const divLicenseBackImage = document.getElementById(field);
        const image = divLicenseBackImage.querySelector('img');
        const urlImage = image.src.split('/');
        const nameImage = urlImage.pop();
        urlImage.pop();
        const newUrlImage = urlImage.join('/')
        image.src = `${newUrlImage}/normal/${nameImage}`
        image.style.maxWidth = '100%';
        image.style.height = 'auto';

        // Rendered size:	800 × 450 px
        // Intrinsic size:	800 × 450 px

        const nuevoDiv = document.createElement("div");
        nuevoDiv.innerHTML =
            `<button class="btn p-1" onclick="rotarImagen(90, event, '${field}')"><i class="fa fa-repeat" aria-hidden="true"></i></button>
        <button class="btn p-1 "  onclick="rotarImagen(-90, event, '${field}')"><i class="fa fa-undo" aria-hidden="true"></i></button>
        <button class="btn btn-primary p-1"  onclick="guardarImagen(event, '${field}', '${nameAttribute}', '${node}', ${id}, '${folder}')"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>`
        divLicenseBackImage.appendChild(nuevoDiv)

    }
</script>


<script>
    function rotarImagen(grados, e, field) {
        e.preventDefault();
        const divLicenseBackImage = document.getElementById(field);

        var imagen = divLicenseBackImage.getElementsByTagName(`img`)[0];

        // Obtén el ángulo actual de rotación
        var estilo = window.getComputedStyle(imagen);
        var transform = estilo.getPropertyValue("transform");

        var rotacionActual = 0;

        if (transform && transform !== "none") {
            var valores = transform.split("(")[1].split(")")[0].split(",");
            var a = valores[0];
            var b = valores[1];
            rotacionActual = Math.round(Math.atan2(b, a) * (180 / Math.PI));
        }
        var nuevaRotacion = rotacionActual + grados;
        imagen.style.transform = "rotate(" + nuevaRotacion + "deg)";
    }


    async function guardarImagen(e, field, nameAttribute, node, id, folder) {
        e.preventDefault();
        const divLicenseBackImage = document.getElementById(field);
        const imagen = divLicenseBackImage.getElementsByTagName(`img`)[0];

        console.log(imagen)
        // Obtén el ángulo actual de rotación
        var estilo = window.getComputedStyle(imagen);
        var transform = estilo.getPropertyValue("transform");
        var rotacionActual = 0;

        if (transform && transform !== "none") {
            var valores = transform.split("(")[1].split(")")[0].split(",");
            var a = valores[0];
            var b = valores[1];
            rotacionActual = Math.round(Math.atan2(b, a) * (180 / Math.PI));
        }

        // Crea un lienzo HTML5
        var canvas = document.createElement("canvas");
        var ctx = canvas.getContext("2d");

        canvas.width = imagen.naturalWidth;
        canvas.height = imagen.naturalHeight;

        // Aplica la rotación actual al lienzo
        ctx.translate(canvas.width / 2, canvas.height / 2);
        ctx.rotate((rotacionActual * Math.PI) / 180);
        ctx.drawImage(imagen, -canvas.width / 2, -canvas.height / 2);

        const newImage = await new Promise((resolve) => {
            canvas.toBlob((blob) => {
                // Crea un objeto File a partir del Blob
                var archivo = new File([blob], "imagen_rotada.jpg", {
                    type: "image/jpeg"
                });
                resolve(archivo);
            }, "image/jpeg");
        });


        var formData = new FormData();
        formData.append("imagen", newImage);
        formData.append("attribute", nameAttribute);
        formData.append("node", node);
        formData.append("itemId", id);
        formData.append("folder", folder);

        try {
            const response = await fetch("/customer-admin/image-save", {
                method: 'POST',
                headers: {},
                body: formData
            });
            const data = await response.json();
            console.log(data);
            window.location.reload();
        } catch (error) {
            console.error("Error en la solicitud Fetch: ", error);
        }

    }
</script>

@if (request()->segment(3) == 'driver' && request()->segment(4) == 'edit')
    <script>
        const driverID = document.querySelector('[name="id"]');

        addButtonsForRotareImage('field_license_back_image', 'license_back_image', 'driver', driverID.value,
            'driver-license_back_image');
        addButtonsForRotareImage('field_license_front_image', 'license_front_image', 'driver', driverID.value,
            'driver-license_front_image');
        addButtonsForRotareImage('field_image', 'image', 'driver', driverID.value,
            'driver-image');
        addButtonsForRotareImage('field_ci_front_image', 'ci_front_image', 'driver', driverID.value,
            'driver-ci_front_image');
        addButtonsForRotareImage('field_ci_back_image', 'ci_back_image', 'driver', driverID.value,
            'driver-ci_back_image');
    </script>
@endif
@if (request()->segment(3) == 'driver-vehicle' && request()->segment(4) == 'edit')
    <script>
        const driverVehicleID = document.querySelector('[name="id"]');

        addButtonsForRotareImage('field_vehicle_image', 'vehicle_image', 'driver-vehicle', driverVehicleID.value,
            'driver-vehicle-vehicle_image');

        addButtonsForRotareImage('field_side_image', 'side_image', 'driver-vehicle', driverVehicleID.value,
            'driver-vehicle-side_image');
    </script>
@endif


<script>
    $(document).ready(function() {
        const pagination = $('#general-list_paginate')
        pagination.css('display', 'none');
    });
</script>

</html>
