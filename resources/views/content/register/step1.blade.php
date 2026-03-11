@extends('layouts/subadmin-register')

@section('content')
    <style>
        .container {
            margin: 0px auto;
            width: 300px;
        }

        .color-select {
            border-bottom: 1px solid #3E3E3E;
            padding-left: 10px;
        }

        .color-select>span {
            /* text-transform: uppercase; */
            padding: 8px 0;
            display: block;
            cursor: pointer;
        }

        .color-select>span span {
            margin-top: -8px
        }

        .color-select ul {
            width: 100%;
            overflow: visible;
            padding: 0;
            border-top: 1px solid #3E3E3E;
            display: none;
        }

        .color-select ul:after {
            content: "";
            display: table;
            clear: both;
        }

        .color-select ul li,
        .color-select>span span {
            list-style: none;
            width: 30px;
            height: 30px;
            display: block;
            border-radius: 50%;
            background: #fff;
            float: left;
            margin: 0 12px;
            cursor: pointer;
            position: relative;
            top: -7px;

        }

        .color-select ul li {
            margin-top: 25px;
            -webkit-transition: all .3s ease-in-out;
            -moz-transition: all .3s ease-in-out;
            -ms-transition: all .3s ease-in-out;
            -o-transition: all .3s ease-in-out;
            transition: all .3s ease-in-out;
            border: 1px solid;
        }

        .color-select ul li span {
            display: none;
        }

        .color-select ul li:hover {
            -webkit-transform: scale(1.15);
            -moz-transform: scale(1.15);
            -ms-transform: scale(1.15);
            -o-transform: scale(1.15);
            transform: scale(1.15);
        }

        /* loader */

        /* Estilos para el loader */
        .loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 5px solid #f3f3f3;
            border-top: 5px solid #016d21;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            display: none;
        }

        .increment-text {
            font-size: 120%; 
        }

        .label_font_size{
            font-size: 130%;
        }

        .increment-text input,
        .increment-text textarea,
        .increment-text select,
        .increment-text b {
            font-size: inherit; 
        }

        /* Animación de rotación */
        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="loader" id="loader"></div>

    <div class="content__form increment-text" style="height: 1000px" id="content-register">
        <h3 class="title__form pb-1">Creación de tu Perfil</h3>
        <p class="m-0" style="text-align: justify;">¡Hola! Bienvenido al registro de conductor para formar parte de
            Andre, por favor
            verifica que los campos estén correctamente llenados, toma en cuenta que revisaremos la información brindada
            para habilitarte a la aplicación, te confirmaremos por correo electrónico.

        </p>

        <br>
        <h3 class="title__form pb-1">Registro de datos personales</h3>
        <p>(*) Campos obligatorios.</p>
        <br>

        <form action="/customer-admin/register-driver/step1" method="POST" enctype="multipart/form-data" id="form-register">
            <div class="content__fields">
                @include('includes.field', [
                    'id' => 'first_name',
                    'label' => 'Nombres (*) ',
                    'name' => 'first_name',
                    'type' => 'text',
                    'hasError' => $errors->has('first_name'),
                    'error' => $errors->first('first_name'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('first_name'),
                ])
                {{-- <label for="">ola</label> --}}

                {{-- @if ($errors->has('first_name'))
                    <div class="alert alert-danger">{{ $errors->first('first_name') }}</div>
                @endif --}}

                @include('includes.field', [
                    'id' => 'last_name',
                    'label' => 'Apellidos (*)',
                    'name' => 'last_name',
                    'type' => 'text',
                    'hasError' => $errors->has('last_name'),
                    'error' => $errors->first('last_name'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('last_name'),
                ])

                @include('includes.field', [
                    'id' => 'email',
                    'label' =>
                        'Email (*) (Entra a google en la parte de arriba en una esquina, con la inicial de una letra) ',
                    'name' => 'email',
                    'type' => 'email',
                    'hasError' => $errors->has('email'),
                    'error' => $errors->first('email'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('email'),
                ])
                @include('includes.field', [
                    'id' => 'password',
                    'label' => 'Contraseña (*) (Crea tu Contraseña)',
                    'name' => 'password',
                    'type' => 'password',
                    'hasError' => $errors->has('password'),
                    'error' => $errors->first('password'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('password'),
                ])

                @include('includes.field', [
                    'id' => 'cellphone',
                    'label' => 'Número de Celular (*)',
                    'name' => 'cellphone',
                    'type' => 'phone',
                    'hasError' => $errors->has('cellphone'),
                    'error' => $errors->first('cellphone'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('cellphone'),
                ])


                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-driver-region_id"><b class="label_font_size" style="font-size: 130%">Departamento (*)</b> <b
                            style="color:red; font-weight: 400" id="driver-region_id"></b></label>
                    <div class="area__select">
                        <select name="region_id" onchange="getCitiesByRegion(event)" class="js-example-basic-single"
                            id="field__custom-driver-region_id">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('city_id'))
                        <p class="error__text">{{ $errors->first('region_id') }}</p>
                    @endif
                </div>

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-city_id"><b class="label_font_size" style="font-size: 130%">Municipio (*)</b> <b style="color:red; font-weight: 400"
                            id="city_id"></b></label>
                    <div class="area__select">
                        <select name="city_id" id="field__custom-city_id" onchange="getOrganizationByCity(event)" class="js-example-basic-single">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('city_id'))
                        <p class="error__text">{{ $errors->first('city_id') }}</p>
                    @endif
                </div>

                {{-- @include('includes.field', [
                    'id' => 'ci_number',
                    'label' => 'Número de Carnet de Identidad (*)',
                    'name' => 'ci_number',
                    'type' => 'number',
                    'hasError' => $errors->has('ci_number'),
                    'error' => $errors->first('ci_number'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('ci_number'),
                ]) --}}
                {{-- 
                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-ci_exp"><b>Expedido (*)</b> <b style="color:red; font-weight: 400"
                            id="ci_exp"></b></label>
                    <div class="area__select">
                        <select name="ci_exp" id="field__custom-ci_exp">
                            <option value="" selected disabled>Seleccione una opción</option>

                            <option value="LP">LP </option>
                            <option value="CH">CH </option>
                            <option value="CB">CB </option>
                            <option value="BE">BE </option>
                            <option value="OR">OR </option>
                            <option value="PA">PA </option>
                            <option value="PO">PO </option>
                            <option value="SC">SC </option>
                            <option value="TA">TA </option>

                        </select>
                    </div>
                    @if ($errors->has('ci_exp'))
                        <p class="error__text">{{ $errors->first('ci_exp') }}</p>
                    @endif
                </div> --}}

                <div class="content__field col-lg-6 col-md-6 col-12" style="display: none;">
                    <label for="field__custom-rubro"><b>Rubro</b></label>
                    <span style="font-size: 14px; color:#4b4b4b;"></span>

                    <div class="area__select" >
                        <select name="rubro_id" required id="field__custom-rubro">
                            <option value="" >Seleccione una opción</option>
                            <option selected value="taxi-moto">Taxi Moto</option>
                            <option value="taxi-auto">Taxi Auto</option>
                            <option value="servicio-encomienda">Servicio de Encomienda</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>
                    @if ($errors->has('rubro'))
                        <p class="error__text">{{ $errors->first('rubro') }}</p>
                    @endif
                </div>
                <br>

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-organization_id"><b class="label_font_size" style="font-size: 130%">Empresa (*)</b> <b style="color:red; font-weight: 400"
                            id="organization_id"></b></label>
                    <span style="font-size: 14px; color:#4b4b4b;">Si tu empresa no se encuentra en la lista, por favor
                        comunícate con soporte.</span>

                    <div class="area__select">
                        <select name="organization_id" id="field__custom-organization_id" class="js-example-basic-single">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}"
                                    {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('organization_id'))
                        <p class="error__text">{{ $errors->first('organization_id') }}</p>
                    @endif
                </div>




                @include('includes.field', [
                    'id' => 'image',
                    'label' => 'Foto de Perfil (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('image'),
                    'error' => $errors->first('image'),
                    'value' => old('image'),
                ])

               {{--  @include('includes.field', [
                    'id' => 'license_front_image',
                    'label' => 'Imagen Frontal de la Licencia (*)',
                    'subtext' => '<span></span>',
                    'name' => 'license_front_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('license_front_image'),
                    'error' => $errors->first('license_front_image'),
                    'value' => old('license_front_image'),
                ])

                @include('includes.field', [
                    'id' => 'license_back_image',
                    'label' => 'Imagen del Reverso de la Licencia (*)',
                    'subtext' => '<span></span>',
                    'name' => 'license_back_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('license_back_image'),
                    'error' => $errors->first('license_back_image'),
                    'value' => old('license_back_image'),
                ])--}}
                
                {{-- @include('includes.field', [
                    'id' => 'ci_front_image',
                    'label' => 'Imagen Frontal del CI (*)',
                    'subtext' => '<span></span>',
                    'name' => 'ci_front_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_front_image'),
                    'error' => $errors->first('ci_front_image'),
                    'value' => old('ci_front_image'),
                ])
                @include('includes.field', [
                    'id' => 'ci_back_image',
                    'label' => 'Imagen del Reverso del CI (*)',
                    'subtext' => '<span></span>',
                    'name' => 'ci_back_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_back_image'),
                    'error' => $errors->first('ci_back_image'),
                    'value' => old('ci_back_image'),
                ]) --}}

                {{-- @include('includes.field', [
                    'id' => 'tic_file',
                    'label' => 'Imagen Frontal de Documento TIC',
                    'subtext' => '<span></span>',
                    'name' => 'tic_file',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('tic_file'),
                    'error' => $errors->first('tic_file'),
                    'value' => old('tic_file'),
                ]) --}}

                @include('includes.field', [
                    'id' => 'tic',
                    'label' => 'Nombre de Federación de Moto Taxi/ Auto Taxi',
                    'subtext' => '<span></span>',
                    'name' => 'tic',
                    'type' => 'text',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('tic'),
                    'error' => $errors->first('tic'),
                    'value' => old('tic'),
                ])

                @include('includes.field', [
                    'id' => 'license_number',
                    'label' => 'Número de Interno solo si pertenece a sindicato de transporte público',
                    'name' => 'license_number',
                    'type' => 'text',
                    'hasError' => $errors->has('license_number'),
                    'error' => $errors->first('license_number'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('license_number'),
                ])

              {{--  @include('includes.field', [
                    'id' => 'license_expiration_date',
                    'label' => 'Fecha de Expiración de la Licencia (*)',
                    'name' => 'license_expiration_date',
                    'type' => 'date',
                    'hasError' => $errors->has('license_expiration_date'),
                    'error' => $errors->first('license_expiration_date'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('license_expiration_date'),
                ])--}}


                {{-- @include('includes.field', [
                    'id' => 'number_of_passengers',
                    'label' => 'Número de pasajeros (*)',
                    'name' => 'number_of_passengers',
                    'type' => 'number',
                    'hasError' => $errors->has('number_of_passengers'),
                    'error' => $errors->first('number_of_passengers'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'min' => 0,
                    'max' => 99,
                    'required' => false,
                    'value' => old('number_of_passengers'),
                ]) --}}

                {{-- <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-gender"><b>Genero (*)</b> <b style="color:red; font-weight: 400"
                            id="gender"></b></label>
                    <div class="area__select">
                        <select name="gender" id="field__custom-gender">
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="male">Masculino</option>
                            <option value="female">Femenino</option>
                        </select>
                    </div>
                    @if ($errors->has('gender'))
                        <p class="error__text">{{ $errors->first('gender') }}</p>
                    @endif
                </div> --}}

                {{-- @include('includes.field', [
                    'id' => 'active_trips',
                    'label' => '¿Viajes?',
                    'subtext' => '<span></span>',
                    'name' => 'is_active_for_career',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                ]) --}}

                {{-- @include('includes.field', [
                    'id' => 'baby_chair',
                    'label' => '¿Silla para bebe?',
                    'subtext' => '<span></span>',
                    'name' => 'baby_chair',
                    'type' => 'switch',
                    'checked' => '',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1 d-none',
                ])
                @include('includes.field', [
                    'id' => 'travel_with_pets',
                    'label' => '¿Permite viaje con mascotas?',
                    'subtext' => '<span></span>',
                    'name' => 'travel_with_pets',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                ]) --}}
                {{-- @include('includes.field', [
                    'id' => 'fragile_content',
                    'label' => '¿Contenido frágil?',
                    'subtext' => '<span></span>',
                    'name' => 'fragile_content',
                    'type' => 'switch',
                    'checked' => '',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1 d-none',
                ]) --}}

                {{-- @include('includes.field', [
                    'id' => 'car_with_grill',
                    'label' => '¿Auto con parrilla?',
                    'subtext' => '<span></span>',
                    'name' => 'car_with_grill',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                ]) --}}

                {{-- @include('includes.field', [
                    'id' => 'car_with_ac',
                    'label' => '¿Auto con aire acondicionado?',
                    'subtext' => '<span></span>',
                    'name' => 'car_with_ac',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                ]) --}}
                {{-- @include('includes.field', [
                    'id' => 'car_electric',
                    'label' => '¿Auto eléctrico?',
                    'subtext' => '<span></span>',
                    'name' => 'car_electric',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                ]) --}}

            </div>

            <br>
            <br>
            <h3 class="title__form pb-1">Registra tu Vehículo Moto/Auto</h3>
            <p>(*) Campos obligatorios.</p>

            <br>
            <div class="content__fields">

                {{-- <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-city_id"><b>Ciudad (*)</b> <b style="color:red; font-weight: 400"
                            id="city_id"></b></label>
                    <div class="area__select">
                        <select name="city_id_vehicle" id="field__custom-city_id">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}"
                                    {{ old('city_id_vehicle') == $city->id ? 'selected' : '' }}>{{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('city_id'))
                        <p class="error__text">{{ $errors->first('city_id') }}</p>
                    @endif
                </div> --}}

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-type_vehicle"><b class="label_font_size" style="font-size: 130%">Tipo de Vehiculo (*)</b> <b
                            style="color:red; font-weight: 400" id="type_vehicle"></b></label>
                    <div class="area__select">
                          <select name="type_vehicle" required id="field__custom-type_vehicle" onchange="getBrandsByType(event)">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($typeVehicle as $typeV)
                                <option value="{{ $typeV["id"] }}"
                                    {{ old('type_vehicle') == $typeV["id"] ? 'selected' : '' }}>{{ $typeV["name"] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('type_vehicle'))
                        <p class="error__text">{{ $errors->first('type_vehicle') }}</p>
                    @endif
                </div>


                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-vehicle_brand_id"><b class="label_font_size" style="font-size: 130%">Marca de Vehiculo (*)</b> <b
                            style="color:red; font-weight: 400" id="vehicle_brand_id"></b></label>
                    <span style="font-size: 14px; color:#4b4b4b;">Si la marca de tu vehículo no se encuentra en la lista,
                        por favor comunícate con soporte.</span>
                    <div class="area__select">
                        <select name="vehicle_brand_id" onchange="getModelsByBrand(event)" class="js-example-basic-single"
                            id="field__custom-vehicle_brand_id">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($vehiclesBrands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('vehicle_brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('vehicle_brand_id'))
                        <p class="error__text">{{ $errors->first('vehicle_brand_id') }}</p>
                    @endif
                </div>

                {{-- <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-vehicle_model_id"><b>Modelo de Vehículo (*)</b> <b
                            style="color:red; font-weight: 400" id="vehicle_model_id"></b></label>
                    <span style="font-size: 12px; color:#4b4b4b;">Si el modelo de tu vehículo no se encuentra en la lista,
                        por favor comunícate con soporte.</span>

                    <div class="area__select">
                        <select name="vehicle_model_id" id="field__custom-vehicle_model_id">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($vehiclesModels as $model)
                                <option value="{{ $model->id }}"
                                    {{ old('vehicle_model_id') == $model->id ? 'selected' : '' }}>{{ $model->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('vehicle_model_id'))
                        <p class="error__text">{{ $errors->first('vehicle_model_id') }}</p>
                    @endif
                </div> --}}

                 @include('includes.field', [
                    'id' => 'number_plate',
                    'label' => 'Número de Placa (*)',
                    'name' => 'number_plate',
                    'type' => 'text',
                    'hasError' => $errors->has('number_plate'),
                    'error' => $errors->first('number_plate'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('number_plate'),
                ]) 

             
                {{-- 
                @include('includes.field', [
                    'id' => 'model_year',
                    'label' => 'Año del modelo',
                    'name' => 'model_year',
                    'type' => 'number',
                    'hasError' => $errors->has('model_year'),
                    'error' => $errors->first('model_year'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('model_year'),
                ]) --}}
                @include('includes.field', [
                    'id' => 'chassis_number',
                    'label' => 'Número de Chasis',
                    'name' => 'chassis_number',
                    'type' => 'text',
                    'hasError' => $errors->has('chassis_number'),
                    'error' => $errors->first('chassis_number'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'customClass' => 'd-none',
                    'required' => false,
                    'value' => old('chassis_number'),
                ])
                {{-- @include('includes.field', [
                    'id' => 'rua',
                    'label' => 'Número de RUAT (CRPVA)',
                    'name' => 'rua',
                    'type' => 'text',
                    'hasError' => $errors->has('rua'),
                    'error' => $errors->first('rua'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('rua'),
                ]) --}}
                {{-- @include('includes.field', [
                    'id' => 'rua_file',
                    'label' => 'Imagen Frontal RUAT',
                    'name' => 'rua_file',
                    'type' => 'file',
                    'hasError' => $errors->has('rua_file'),
                    'error' => $errors->first('rua_file'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('rua_file'),
                ]) --}}

                <div class="content__field col-lg-6 col-md-6 col-12" style="display: none;">
                    <label for="field__custom-type"><b class="label_font_size" style="font-size: 130%">
                            Clase de Vehículo (Modelo de negocio)* (*)</b> <b style="color:red; font-weight: 400" id="type"></b></label>
                    <div class="area__select">
                        <select name="type" id="field__custom-type" class="js-example-basic-single">
                            <option value="">Seleccione una opción</option>
                            <option value="moto-taxi-uso-comercial">Moto Taxi uso comercial</option>
                            <option value="torito-capacidad-maxima">Torito capacidad máxima</option>
                            <option value="auto-taxi-uso-comercial">Auto Taxi uso comercial</option>
                            <option value="delivery" selected>Delivery</option>
                            <option value="servicio-encomienda-moto">Servicio de encomienda Moto</option>
                            <option value="servicio-encomienda-auto">Servicio de encomienda Auto</option>
                            <option value="servicio-encomienda-bus">Servicio de encomienda Bus</option>
                            <option value="vehiculo-taxi-alta-gama">Vehículo taxi de alta gama</option>
                            <option value="moto-taxi-alta-gama">Moto taxi de alta gama</option>
                            <option value="otros-vehiculos">Otros vehículos</option>
                        </select>
                    </div>
                    @if ($errors->has('type'))
                        <p class="error__text">{{ $errors->first('type') }}</p>
                    @endif
                </div>

              {{--   @include('includes.field', [
                    'id' => 'vehicle_image',
                    'label' => 'Sacar foto de la placa del Vehículo o Moto (*)',
                    'subtext' => '<span></span>',
                    'name' => 'vehicle_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('vehicle_image'),
                    'error' => $errors->first('vehicle_image'),
                    'value' => old('vehicle_image'),
                ])

                @include('includes.field', [
                    'id' => 'side_image',
                    'label' => 'Imagen de Costado del Vehículo o Moto',
                    'subtext' => '<span></span>',
                    'name' => 'side_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('side_image'),
                    'error' => $errors->first('side_image'),
                    'value' => old('side_image'),
                ])--}}


                {{-- <div class="content__field col-lg-6 col-md-6 col-sm-6 col-12">
                    <label for="color">Seleccionar color (*) <b style="color:red; font-weight: 400"
                            id="color"></b></label>
                    <select data-colorselect name="color" id="select-color">
                        <option value="" disabled style="border: 0px solid">Seleccione un color</option>
                        <option value="#ffffff">Blanco</option>
                        <option value="#2ecc71">Verde</option>
                        <option value="#3498db">Azul</option>
                        <option value="#9b59b6">Violeta</option>
                        <option value="#34495e">Azul Marino</option>
                        <option value="#1abc9c">Turquesa</option>
                        <option value="#e74c3c">Rojo</option>
                        <option value="#7f8c8d">Plomo</option>
                        <option value="#f1c40f">Amarrillo</option>
                        <option value="#e67e22">Naranja</option>
                        <option value="#000000">Negro</option>
                    </select>
                    @if ($errors->has('color'))
                        <p class="error__text">{{ $errors->first('color') }}</p>
                    @endif
                </div> --}}
            </div>

            <br>
            <br>
            {{-- <h3 class="title__form pb-1">Registra Datos Bancarios</h3>
            <br>

            @include('includes.field', [
                'id' => 'number_of_bank',
                'label' => 'Número de Cuenta',
                'subtext' => '<span></span>',
                'name' => 'number_of_bank',
                'type' => 'text',
                'fill' => false,
                'required' => false,
                'col_lg' => 'col-lg-6',
                'col_md' => 'col-md-6',
                'hasError' => $errors->has('number_of_bank'),
                'error' => $errors->first('number_of_bank'),
                'value' => old('number_of_bank'),
            ])
            <br> --}}
            {{-- <div class="content__field col-lg-6 col-md-6 col-12">
                <label for="field__custom-bank_id"><b>Banco</b> <b style="color:red; font-weight: 400"
                        id="bank_id"></b></label>
                <span style="font-size: 12px; color:#4b4b4b;">Si la entidad bancaria no se encuentra en la lista,
                    por favor comunícate con soporte.</span>

                <div class="area__select">
                    <select name="bank_id" id="field__custom-bank_id">
                        <option value="" selected disabled>Seleccione una opción</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                {{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($errors->has('bank_id'))
                    <p class="error__text">{{ $errors->first('bank_id') }}</p>
                @endif
            </div> --}}


            {{-- <br>
            <div class="content__fields">
                @include('includes.field', [
                    'id' => 'name_titular',
                    'label' => 'Nombre del Titular',
                    'name' => 'name_titular',
                    'type' => 'text',
                    'hasError' => $errors->has('name_titular'),
                    'error' => $errors->first('name_titular'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('name_titular'),

                ])
                @include('includes.field', [
                    'id' => 'ci_number_titular',
                    'label' => 'Número de Carnet de Identidad del Titular',
                    'name' => 'ci_number_titular',
                    'type' => 'number',
                    'hasError' => $errors->has('ci_number_titular'),
                    'error' => $errors->first('ci_number_titular'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => false,
                    'value' => old('ci_number_titular'),

                ])
                @include('includes.field', [
                    'id' => 'ci_front_image_titular',
                    'label' => 'Imagen Frontal del CI del Titular',
                    'subtext' => '<span></span>',
                    'name' => 'ci_front_image_titular',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_front_image_titular'),
                    'error' => $errors->first('ci_front_image_titular'),
                    'value' => old('ci_front_image_titular'),
                ])
                @include('includes.field', [
                    'id' => 'ci_back_image_titular',
                    'label' => 'Imagen del Reverso del CI del Titular',
                    'subtext' => '<span></span>',
                    'name' => 'ci_back_image_titular',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_back_image_titular'),
                    'error' => $errors->first('ci_back_image_titular'),
                    'value' => old('ci_back_image_titular'),
                ])
            </div> --}}

            {{-- FOOT --}}
            <br>
            <br>
            <br>

            <input type="checkbox" class="ml-1"  id="terminos"> <label for="terminos" style="font-size: 110%">Aceptar Términos y
                Condiciones (*)</label>
            <br>

            <div class="col-12">
            </br>
                <strong>Notas: </br></strong>
                <p class="text__terms">- De un click solo una vez y espere por favor, el registro se está cargando </br>
                <p class="text__terms">- Como recomendación saque una captura de pantalla para guardar sus credenciales de registro </br> </p>
            </div>

            <button type="submit" class="ml-1 btn btn-primary btn__submit" id="btn-save">
                <span class="text__third"><b>Registrarse</b></span>
            </button>
            <br>
            <br>
    </div>
    <br>

    </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2_popup').select2({
                dropdownParent: $('.modal__cap')
            });
            $('.select2_normal').select2();
        });
    </script>

    <script>

           async function getBrandsByType(e) {
            $('.loading__cap').addClass('show__loading');
            const id = e.target.value;
            try {
                const selectSubCategory = document.getElementById('field__custom-vehicle_brand_id');
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                const response = await fetch('/customer-admin/brands-by-type/' + id);
                console.log(response)
                const data = await response.json();
                Object.values(data.data).forEach(element => {
                    html +=
                        `<option value="${element.id}">${element.name}</option>`; 
                });
                selectSubCategory.innerHTML = html;
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.log(error);
                $('.loading__cap').removeClass('show__loading');
            }
        }


        async function getModelsByBrand(e) {
            $('.loading__cap').addClass('show__loading');
            const id = e.target.value;
            try {
                const selectSubCategory = document.getElementById('field__custom-vehicle_model_id');
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                const response = await fetch('/customer-admin/models-by-brand/' + id);
                console.log(response)
                const data = await response.json();
                Object.values(data.data).forEach(element => {
                    html +=
                        `<option value="${element.id}">${element.name}</option>`; 
                });
                selectSubCategory.innerHTML = html;
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.log(error);
                $('.loading__cap').removeClass('show__loading');
            }
        }

        // COLORS
        $.fn.colorSelect = function() {
            function build($select) {
                var html = '';
                var listItems = '';

                $select.find('option').each(function() {
                    listItems += '' +
                        '<li style="background:' + this.value + '" data-colorVal="' + this.value + '">' +
                        '<span>' + this.text + '</span>' +
                        '</li>';
                });

                html = '' +
                    '<div class="color-select">' +
                    '<span>Seleccionar Color</span>' +
                    '<ul>' + listItems + '</ul>' +
                    '</div>';

                return html;
            }

            this.each(function() {
                var $this = $(this);

                $this.hide();

                $this.after(build($this));
            });
        };

        $(document)
            .on('click', '.color-select > span', function() {
                $(this).siblings('ul').toggle();
            })
            .on('click', '.color-select li', function() {
                var $this = $(this);
                var color = $this.attr('data-colorVal');
                var colorText = $this.find('span').text();
                var $value = $this.parents('.color-select').find('span:first');
                var $select = $this.parents('.color-select').prev('select');

                $value.text(colorText);
                $value.append('<span style="background:' + color + '"></span>');
                $this.parents('ul').hide();
                $select.val(color);
            });

        $(function() {
            $('[data-colorselect]').colorSelect();
        })
    </script>

    <script>
        async function getOrganizationByCity(e) {
            const id = e.target.value;
            try {
                const selectSubCategory = document.getElementById('field__custom-organization_id');
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                const response = await fetch('/customer-admin/organization-by-city/' + id);

                const data = await response.json();
                Object.values(data.data).forEach(element => {
                    html +=
                        `<option value="${element.id}">${element.name}</option>`; // Added closing angle bracket >
                });

                selectSubCategory.innerHTML = html;
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.log(error);
                $('.loading__cap').removeClass('show__loading');
            }
        }

        async function getCitiesByRegion(e) {
            const id = e.target.value;
            try {
                const selectCity = document.getElementById('field__custom-city_id');
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                const response = await fetch('/customer-admin/cities-by-region/' + id);

                const data = await response.json();
                Object.values(data.data).forEach(element => {
                    html +=
                        `<option value="${element.id}">${element.name}</option>`; // Added closing angle bracket >
                });

                selectCity.innerHTML = html;
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.log(error);
                $('.loading__cap').removeClass('show__loading');
            }
        }
    </script>
    <script>
        $('#form-register').on('submit', function(e) {
            const firstName = $('#field__custom-first_name')
            const last_name = $('#field__custom-last_name')
            const email = $('#field__custom-email')
            const password = $('#field__custom-password')
            const cellphone = $('#field__custom-cellphone')

            const image = $('#field__custom-image')
            const license_front_image = $('#field__custom-license_front_image')
            const license_back_image = $('#field__custom-license_back_image')
            const ci_front_image = $('#field__custom-ci_front_image')
            const ci_back_image = $('#field__custom-ci_back_image')

            const license_number = $('#field__custom-license_number')
            const license_expiration_date = $('#field__custom-license_expiration_date')
            // const number_of_passengers = $('#field__custom-number_of_passengers')

            const number_plate = $('#field__custom-number_plate')
            const model_year = $('#field__custom-model_year')
            const chassis_number = $('#field__custom-chassis_number')
            const rua = $('#field__custom-rua')
            const vehicle_image = $('#field__custom-vehicle_image')
            const side_image = $('#field__custom-side_image')
            const number_of_bank = $('#field__custom-number_of_bank')
            const name_titular = $('#field__custom-name_titular')
            const ci_number_titular = $('#field__custom-ci_number_titular')
            const ci_front_image_titular = $('#field__custom-ci_front_image_titular')
            const ci_back_image_titular = $('#field__custom-ci_back_image_titular')

            const field__custom_driver_city_id = $('#field__custom-driver-city_id')
            const field__custom_organization_id = $('#field__custom-organization_id')
            const field__custom_city_id = $('#field__custom-city_id')
            const field__custom_vehicle_brand_id = $('#field__custom-vehicle_brand_id')
            const field__custom_vehicle_model_id = $('#field__custom-vehicle_model_id')
            const field__custom_gender = $('#field__custom-gender')
            const field__custom_type = $('#field__custom-type')
            const field__custom_bank_id = $('#field__custom-bank_id')

            const ci_number = $('#field__custom-ci_number')
            const field__custom_ci_exp = $('#field__custom-ci_exp')

            const color = $('#select-color')

            // try {
            //     validEmpty(firstName, 'first_name', e)
            //     validEmpty(last_name, 'last_name', e)
            //     validEmpty(email, 'email', e)
            //     validEmpty(password, 'password', e)
            //     validEmpty(cellphone, 'cellphone', e)

            //     // validEmpty(ci_number, 'ci_number', e)

            //     validEmpty(image, 'image', e)
            //     validEmpty(license_front_image, 'license_front_image', e)
            //     validEmpty(license_back_image, 'license_back_image', e)
            //     // validEmpty(ci_front_image, 'ci_front_image', e)
            //     // validEmpty(ci_back_image, 'ci_back_image', e)

            //     validEmpty(license_number, 'license_number', e)
            //     validEmpty(license_expiration_date, 'license_expiration_date', e)
            //     // validEmpty(number_of_passengers, 'number_of_passengers', e)
            //     validEmpty(ci_back_image, 'ci_back_image', e)
            //     validEmpty(number_plate, 'number_plate', e)
            //     // validEmpty(model_year, 'model_year', e)
            //     // validEmpty(rua, 'rua', e)
            //     validEmpty(vehicle_image, 'vehicle_image', e)
            //     validEmpty(side_image, 'side_image', e)

            //     // validEmpty(number_of_bank, 'number_of_bank', e)

            //     // validEmpty(name_titular, 'name_titular', e)
            //     // validEmpty(ci_number_titular, 'ci_number_titular', e)
            //     // validEmpty(ci_front_image_titular, 'ci_front_image_titular', e)
            //     // validEmpty(ci_back_image_titular, 'ci_back_image_titular', e)

            //     validEmptySelect(field__custom_driver_city_id, 'driver-city_id')
            //     // validEmptySelect(field__custom_organization_id, 'organization_id')
            //     validEmptySelect(field__custom_city_id, 'city_id')


            //     validEmptySelect(field__custom_vehicle_brand_id, 'vehicle_brand_id')
            //     // validEmptySelect(field__custom_vehicle_model_id, 'vehicle_model_id')
            //     validEmptySelect(field__custom_gender, 'gender')

            //     // validEmptySelect(field__custom_bank_id, 'bank_id')

            //     validEmptySelect(field__custom_type, 'type')
            //     // validEmptySelect(color, 'color')
            //     $('#loader').css('display', 'block')
            //     $('#content-register').css('display', 'none');

            // } catch (error) {
            //     e.preventDefault()
            //     alert("Debe introducir todos los campos")

            //     $('#loader').css('display', 'none')
            //     $('#content-register').css('display', 'block')
            // }

        })

        function validEmpty(element, id, e) {
            if (element.val() == '') {
                // console.log(id)
                $(`#${id}`).text('Debe completar el campo')
                throw new Error('Debe completar el campo')
            }
        }

        function validEmptySelect(element, id) {
            if (element.val() == null || element.val() == '') {
                $(`#${id}`).text('Debe completar el campo')
                throw new Error('Debe completar el campo')
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
        // document.getElementById('field__custom-number_of_passengers').addEventListener('keyup', function(e) {
        //     if (e.target.value > 99) {
        //         $(`#number_of_passengers`).text('Rango de valores 0 - 99');
        //         e.target.value = 0; // Desactivar el campo de entrada
        //     } else {
        //         $(`#number_of_passengers`).text('');
        //     }
        // })
        // document.getElementById('field__custom-number_of_passengers').addEventListener('click', function(e) {
        //     if (e.target.value > 99) {
        //         $(`#number_of_passengers`).text('Rango de valores 0 - 99');
        //         e.target.value = 0; // Desactivar el campo de entrada
        //     } else {
        //         $(`#number_of_passengers`).text('');
        //     }
        // })
    </script>
@endsection
