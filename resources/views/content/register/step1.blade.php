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

    <div class="content__form increment-text" id="content-register">

        {{-- BARRA DE PROGRESO --}}
        <div class="progress-top">
            <div class="prog-step active">
                <div class="prog-num">1</div>
                <span>Tus datos</span>
            </div>
            <div class="prog-line"></div>
            <div class="prog-step">
                <div class="prog-num">2</div>
                <span>Revisión</span>
            </div>
            <div class="prog-line"></div>
            <div class="prog-step">
                <div class="prog-num">3</div>
                <span>Activación</span>
            </div>
        </div>

        <h3 class="title__form pb-1" style="font-size:22px; font-weight:700; color:#1a3a4a;">Creación de tu Perfil</h3>
        <p class="m-0" style="color:#64748b; font-size:14px; line-height:1.6;">
            ¡Hola! Bienvenido al registro de conductor de AnDre. Completa todos los campos para que podamos verificar tu cuenta y habilitarte lo antes posible.
        </p>

        <div class="section-header">
            <div class="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h3>Datos Personales</h3>
            <span class="required-note">(*) campos obligatorios</span>
        </div>

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
                    'required' => true,
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
                    'required' => true,
                    'value' => old('last_name'),
                ])

                @include('includes.field', [
                    'id' => 'email',
                    'label' => 'Correo Electrónico (*)',
                    'name' => 'email',
                    'type' => 'email',
                    'hasError' => $errors->has('email'),
                    'error' => $errors->first('email'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'value' => old('email'),
                ])
                {{-- Contraseña eliminada del formulario: se auto-genera en el backend --}}

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
                    'required' => true,
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
                        <select name="city_id" id="field__custom-city_id" class="js-example-basic-single">
                            <option value="" selected disabled>Primero seleccione un Departamento</option>
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
                            <option value="" selected disabled>Primero seleccione un Municipio</option>
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
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('image'),
                    'error' => $errors->first('image'),
                    'value' => old('image'),
                ])

                {{-- RECORDATORIO FOTO DE PERFIL --}}
                <div class="content__field col-lg-6 col-md-6 col-12" style="display: flex; align-items: center;">
                    <div style="background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 12px 16px; width: 100%; display:flex; gap:10px; align-items:flex-start;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <p style="margin: 0; font-size: 13px; color: #92400e;">
                            <strong>Recordatorio:</strong> Debes actualizar tu <strong>Foto de Perfil</strong> el <strong>1ro de cada mes</strong> para mantener tu cuenta activa y verificada.
                        </p>
                    </div>
                </div>

                <div style="width: 100%; height: 1px; background: #e5e7eb; margin: 20px 0;"></div>

                {{-- FOTO DEL BREVETE (FRENTE Y REVERSO JUNTOS) --}}
                @include('includes.field', [
                    'id' => 'license_front_image',
                    'label' => 'Foto del Brevete / Licencia de Conducir - Frente (*)',
                    'subtext' => '<span style="font-size:13px; color:#4b4b4b;">📷 Sube una foto clara de la parte frontal de tu brevete o licencia de conducir</span>',
                    'name' => 'license_front_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('license_front_image'),
                    'error' => $errors->first('license_front_image'),
                    'value' => old('license_front_image'),
                ])

                @include('includes.field', [
                    'id' => 'license_back_image',
                    'label' => 'Foto del Brevete / Licencia de Conducir - Reverso (*)',
                    'subtext' => '<span style="font-size:13px; color:#4b4b4b;">📷 Sube una foto clara de la parte trasera de tu brevete o licencia de conducir</span>',
                    'name' => 'license_back_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('license_back_image'),
                    'error' => $errors->first('license_back_image'),
                    'value' => old('license_back_image'),
                ])

                <div style="width: 100%; height: 1px; background: #e5e7eb; margin: 20px 0;"></div>

                {{-- CI DEL CONDUCTOR (ANVERSO Y REVERSO JUNTOS) --}}
                @include('includes.field', [
                    'id' => 'ci_front_image',
                    'label' => 'Carnet de Identidad (CI) del Conductor - Anverso (*)',
                    'subtext' => '<span style="font-size:13px; color:#4b4b4b;">📷 Sube una foto clara de la parte frontal de tu CI</span>',
                    'name' => 'ci_front_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_front_image'),
                    'error' => $errors->first('ci_front_image'),
                    'value' => old('ci_front_image'),
                ])

                @include('includes.field', [
                    'id' => 'ci_back_image',
                    'label' => 'Carnet de Identidad (CI) del Conductor - Reverso (*)',
                    'subtext' => '<span style="font-size:13px; color:#4b4b4b;">📷 Sube una foto clara de la parte trasera de tu CI</span>',
                    'name' => 'ci_back_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_back_image'),
                    'error' => $errors->first('ci_back_image'),
                    'value' => old('ci_back_image'),
                ])

                <div style="width: 100%; height: 1px; background: #e5e7eb; margin: 20px 0;"></div>

                {{-- FEDERACIÓN Y NÚMERO DE INTERNO --}}
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

                {{-- @include('includes.field', [
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

            <div class="section-header">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h5l2 2-2 2h-5"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                </div>
                <h3>Datos del Vehículo</h3>
            </div>
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
                    <span style="font-size: 14px; color:#4b4b4b;">Selecciona tu marca o escríbela si no está en la lista.</span>
                    <div class="area__select">
                        <select name="vehicle_brand_id" onchange="onBrandChange(event)" class="js-example-basic-single"
                            id="field__custom-vehicle_brand_id">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($vehiclesBrands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('vehicle_brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}
                                </option>
                            @endforeach
                            <option value="otra">Otra marca (escribir abajo)</option>
                        </select>
                    </div>
                    {{-- Campo para marca personalizada --}}
                    <div id="custom_brand_container" style="display:none; margin-top: 10px;">
                        <input type="text" name="custom_brand_name" id="custom_brand_name"
                            placeholder="Escribe la marca de tu vehículo"
                            style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; font-size:14px;"
                            value="{{ old('custom_brand_name') }}">
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
                    'required' => true,
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

                @include('includes.field', [
                    'id' => 'vehicle_image',
                    'label' => 'Foto Delantera del Vehículo (*)',
                    'subtext' => '<span style="font-size:13px; color:#4b4b4b;">📸 Toma una foto de la parte delantera de tu vehículo/moto</span>',
                    'name' => 'vehicle_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('vehicle_image'),
                    'error' => $errors->first('vehicle_image'),
                    'value' => old('vehicle_image'),
                ])

                @include('includes.field', [
                    'id' => 'side_image',
                    'label' => 'Foto de Costado del Vehículo (*)',
                    'subtext' => '<span style="font-size:13px; color:#4b4b4b;">📸 Toma una foto del costado de tu vehículo/moto</span>',
                    'name' => 'side_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('side_image'),
                    'error' => $errors->first('side_image'),
                    'value' => old('side_image'),
                ])


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
        console.log('%c[INIT] Page document ready - initializing Select2', 'color: green; font-weight: bold;');
        
        $(document).ready(function() {
            console.log('[READY] Document ready - setting up Select2 plugins');
            $('.select2_popup').select2({
                dropdownParent: $('.modal__cap')
            });
            $('.select2_normal').select2();
            console.log('[SUCCESS] Select2 initialized');
        });
    </script>

    <script>
           async function getBrandsByType(e) {
            console.log('%c[DEBUG] getBrandsByType called', 'color: blue; font-weight: bold;', e);
            $('.loading__cap').addClass('show__loading');
            
            // Obtener el ID (compatible con eventos y elementos jQuery)
            const id = (e && e.target) ? e.target.value : (e && e.val ? e.val() : e);
            console.log('[DEBUG] Type ID:', id);
            
            try {
                const selectSubCategory = document.getElementById('field__custom-vehicle_brand_id');
                if (!selectSubCategory) {
                    throw new Error('Element field__custom-vehicle_brand_id not found');
                }
                
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                console.log('[INFO] Fetching brands from: /customer-admin/brands-by-type/' + id);
                
                const response = await fetch('/customer-admin/brands-by-type/' + id);
                console.log('[DEBUG] Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                
                const data = await response.json();
                console.log('%c[DEBUG] Brands response:', 'color: green;', data);
                
                if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
                    console.log('[INFO] Found ' + data.data.length + ' brands');
                    data.data.forEach(element => {
                        html += `<option value="${element.id}">${element.name}</option>`; 
                    });
                } else {
                    console.log('[WARNING] No brands data in response');
                    html = '<option value="" disabled>No hay marcas disponibles</option>';
                }
                
                selectSubCategory.innerHTML = html;
                $(`#field__custom-vehicle_brand_id`).select2('destroy').select2();
                console.log('[SUCCESS] Brands loaded successfully');
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.error('%c[ERROR] Loading brands:', 'color: red; font-weight: bold;', error);
                const selectSubCategory = document.getElementById('field__custom-vehicle_brand_id');
                if (selectSubCategory) {
                    selectSubCategory.innerHTML = '<option value="" disabled>Error cargando marcas</option>';
                }
                $('.loading__cap').removeClass('show__loading');
            }
        }


        async function getModelsByBrand(e) {
            console.log('%c[DEBUG] getModelsByBrand called', 'color: blue; font-weight: bold;', e);
            $('.loading__cap').addClass('show__loading');
            
            // Obtener el ID (compatible con eventos y elementos jQuery)
            const id = (e && e.target) ? e.target.value : (e && e.val ? e.val() : e);
            console.log('[DEBUG] Brand ID:', id);
            
            try {
                const selectSubCategory = document.getElementById('field__custom-vehicle_model_id');
                
                // Si el elemento no existe aún, simplemente retornar sin error
                if (!selectSubCategory) {
                    console.log('[WARNING] Element field__custom-vehicle_model_id not found yet - element may not be visible');
                    $('.loading__cap').removeClass('show__loading');
                    return;
                }
                
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                console.log('[INFO] Fetching models from: /customer-admin/models-by-brand/' + id);
                
                const response = await fetch('/customer-admin/models-by-brand/' + id);
                console.log('[DEBUG] Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                
                const data = await response.json();
                console.log('%c[DEBUG] Models response:', 'color: green;', data);
                
                if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
                    console.log('[INFO] Found ' + data.data.length + ' models');
                    data.data.forEach(element => {
                        html += `<option value="${element.id}">${element.name}</option>`; 
                    });
                } else {
                    console.log('[WARNING] No models data in response');
                    html = '<option value="" disabled>No hay modelos disponibles</option>';
                }
                
                selectSubCategory.innerHTML = html;
                $(`#field__custom-vehicle_model_id`).select2('destroy').select2();
                console.log('[SUCCESS] Models loaded successfully');
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.error('%c[ERROR] Loading models:', 'color: red; font-weight: bold;', error);
                const selectSubCategory = document.getElementById('field__custom-vehicle_model_id');
                if (selectSubCategory) {
                    selectSubCategory.innerHTML = '<option value="" disabled>Error cargando modelos</option>';
                }
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
        console.log('%c[SCRIPT LOADED] Registration form script initialized', 'color: purple; font-weight: bold;');
        
        async function getOrganizationByCity(cityId) {
            // Acepta tanto un evento como un ID directo
            const id = (typeof cityId === 'object' && cityId.target) ? cityId.target.value : cityId;
            try {
                const selectOrg = document.getElementById('field__custom-organization_id');
                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                const response = await fetch('/customer-admin/organization-by-city/' + id);
                const data = await response.json();
                if (data.data && data.data.length > 0) {
                    Object.values(data.data).forEach(element => {
                        html += `<option value="${element.id}">${element.name}</option>`;
                    });
                } else {
                    html = '<option value="" disabled>No hay empresas para este municipio</option>';
                }
                // Reinicializar Select2
                $('#field__custom-organization_id').select2('destroy');
                selectOrg.innerHTML = html;
                $('#field__custom-organization_id').select2();
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.log('Error cargando empresas:', error);
                $('.loading__cap').removeClass('show__loading');
            }
        }

        async function getCitiesByRegion(e) {
            console.log('%c[DEBUG] getCitiesByRegion called', 'color: blue; font-weight: bold;', e);
            
            // Obtener el valor del select (compatible con eventos, jQuery y elementos DOM)
            let id;
            if (e instanceof jQuery) {
                // Si es un elemento jQuery, obtener el valor directamente
                id = e.val();
                console.log('%c[DEBUG] Received jQuery element. Value:', 'color: blue;', id);
            } else if (e && typeof e === 'object' && e.target) {
                // Si es un evento, obtener el valor del target
                id = $(e.target).val();
                console.log('%c[DEBUG] Received event object. Value:', 'color: blue;', id);
            } else {
                // Si es un valor directo
                id = e;
                console.log('%c[DEBUG] Received value directly:', 'color: blue;', id);
            }
            
            if (!id) {
                console.log('[WARNING] No region ID found');
                return;
            }
            
            try {
                console.log('[INFO] Clearing city and organization selects...');
                
                // Limpiar municipio y empresa
                $('#field__custom-city_id').select2('destroy');
                document.getElementById('field__custom-city_id').innerHTML =
                    '<option value="" selected disabled>Cargando municipios...</option>';
                $('#field__custom-city_id').select2();

                $('#field__custom-organization_id').select2('destroy');
                document.getElementById('field__custom-organization_id').innerHTML =
                    '<option value="" selected disabled>Primero seleccione un Municipio</option>';
                $('#field__custom-organization_id').select2();

                console.log('[INFO] Fetching cities from: /customer-admin/cities-by-region/' + id);
                const response = await fetch('/customer-admin/cities-by-region/' + id);
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                
                const data = await response.json();
                console.log('%c[DEBUG] Response data:', 'color: green;', data);

                let html = '<option value="" selected disabled>Seleccione una opción</option>';
                if (data.status === true && data.data && data.data.length > 0) {
                    console.log('[INFO] Found ' + data.data.length + ' cities');
                    Object.values(data.data).forEach(element => {
                        html += `<option value="${element.id}">${element.name}</option>`;
                    });
                } else {
                    console.log('[WARNING] No cities found for region ' + id);
                    html = '<option value="" disabled>No hay municipios para este departamento</option>';
                }

                // Reinicializar Select2 con datos nuevos
                console.log('[INFO] Reinitializing Select2 for cities...');
                $('#field__custom-city_id').select2('destroy');
                document.getElementById('field__custom-city_id').innerHTML = html;
                const citySelect = $('#field__custom-city_id').select2();

                // Cuando cambie el municipio → cargar empresas
                citySelect.off('change.cascade').on('change.cascade', function() {
                    const cityId = $(this).val();
                    console.log('[DEBUG] City selected:', cityId);
                    if (cityId) getOrganizationByCity(cityId);
                });

                console.log('[SUCCESS] Cities loaded successfully');
                $('.loading__cap').removeClass('show__loading');
            } catch (error) {
                console.error('%c[ERROR] Loading cities:', 'color: red; font-weight: bold;', error);
                document.getElementById('field__custom-city_id').innerHTML = 
                    '<option value="" disabled>Error cargando municipios</option>';
                $('.loading__cap').removeClass('show__loading');
            }
        }
    </script>
    <script>
        $('#form-register').on('submit', function(e) {
            e.preventDefault();
            console.log('%c[DEBUG] Form submitted - PREVENTING DEFAULT BEHAVIOR', 'color: blue; font-weight: bold;');
            
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
            
            console.log('%c[DEBUG] Form data:', 'color: green;', {
                email: email.val(),
                first_name: firstName.val(),
                last_name: last_name.val(),
                cellphone: cellphone.val(),
                region_id: field__custom_driver_city_id.val(),
                city_id: field__custom_city_id.val(),
                organization_id: field__custom_organization_id.val()
            });
            
            console.log('%c[INFO] Form will be submitted via AJAX now', 'color: orange;');
            
            // Validación de campos obligatorios
            var camposVacios = [];
            var camposConError = [];
            
            // --- Datos Personales ---
            if (!firstName.val() || !firstName.val().trim()) { camposVacios.push('Nombres'); camposConError.push(firstName); }
            if (!last_name.val() || !last_name.val().trim()) { camposVacios.push('Apellidos'); camposConError.push(last_name); }
            if (!email.val() || !email.val().trim()) { camposVacios.push('Correo Electrónico'); camposConError.push(email); }
            if (!cellphone.val() || !cellphone.val().trim()) { camposVacios.push('Número de Celular'); camposConError.push(cellphone); }
            
            // --- Departamento y Ciudad ---
            if (!$('#field__custom-driver-region_id').val()) camposVacios.push('Departamento');
            if (!$('#field__custom-city_id').val()) camposVacios.push('Municipio');
            
            // --- Documentos del conductor ---
            if (!image[0] || !image[0].files || !image[0].files.length) camposVacios.push('Foto de Perfil');
            if (!license_front_image[0] || !license_front_image[0].files || !license_front_image[0].files.length) camposVacios.push('Brevete / Licencia - Frente');
            if (!license_back_image[0] || !license_back_image[0].files || !license_back_image[0].files.length) camposVacios.push('Brevete / Licencia - Reverso');
            if (!ci_front_image[0] || !ci_front_image[0].files || !ci_front_image[0].files.length) camposVacios.push('CI - Anverso');
            if (!ci_back_image[0] || !ci_back_image[0].files || !ci_back_image[0].files.length) camposVacios.push('CI - Reverso');
            
            // --- Datos del Vehículo ---
            if (!$('#field__custom-type_vehicle').val()) camposVacios.push('Tipo de Vehículo');
            if (!$('#field__custom-vehicle_brand_id').val()) camposVacios.push('Marca de Vehículo');
            if (!$('#field__custom-number_plate').val() || !$('#field__custom-number_plate').val().trim()) { camposVacios.push('Número de Placa'); camposConError.push($('#field__custom-number_plate')); }
            if (!vehicle_image[0] || !vehicle_image[0].files || !vehicle_image[0].files.length) camposVacios.push('Foto Delantera del Vehículo');
            if (!side_image[0] || !side_image[0].files || !side_image[0].files.length) camposVacios.push('Foto de Costado del Vehículo');
            
            if (camposVacios.length > 0) {
                alert('Por favor complete los siguientes campos obligatorios:\n\n- ' + camposVacios.join('\n- '));
                // Marcar campos de texto vacíos con borde rojo
                camposConError.forEach(function(campo) { campo.css('border', '2px solid red'); });
                // Scroll al primer campo con error
                if (camposConError.length > 0) {
                    $('html, body').animate({ scrollTop: camposConError[0].offset().top - 100 }, 500);
                }
                return false;
            }
            // Limpiar bordes rojos si todo está bien
            camposConError.forEach(function(campo) { campo.css('border', ''); });
            
            try {
                console.log('%c[DEBUG] Getting form element...', 'color: blue;');
                const formElement = document.getElementById('form-register');
                console.log('%c[DEBUG] Form element found:', 'color: blue;', formElement);
                
                if (!formElement) {
                    throw new Error('Form element not found!');
                }
                
                console.log('%c[DEBUG] Creating FormData...', 'color: blue;');
                const formData = new FormData(formElement);
                console.log('%c[DEBUG] FormData created successfully', 'color: blue;');
                
                console.log('%c[INFO] Sending FormData to /customer-admin/register-driver/step1', 'color: purple;');
                
                // Enviar por AJAX
                $.ajax({
                    url: '/customer-admin/register-driver/step1',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000,
                    success: function(response, status, xhr) {
                        console.log('%c[SUCCESS] Request completed!', 'color: green; font-weight: bold;', {
                            status: status,
                            statusCode: xhr.status
                        });
                        console.log('%c[INFO] Redirecting to step3...', 'color: purple;');
                        window.location.href = '/customer-admin/register-driver/step3/';
                    },
                    error: function(xhr, status, error) {
                        console.error('%c[ERROR] Registration failed:', 'color: red; font-weight: bold;', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error,
                            timeout: status === 'timeout'
                        });
                        
                        let errorMessage = error;
                        if (xhr.status === 422 || xhr.status === 400) {
                            try {
                                const jsonResponse = JSON.parse(xhr.responseText);
                                errorMessage = jsonResponse.message || Object.values(jsonResponse.errors || {}).flat().join(', ');
                            } catch (e) {
                                errorMessage = xhr.responseText;
                            }
                        }
                        
                        alert('Error al registrar: ' + errorMessage);
                    }
                });
                
            } catch (error) {
                console.error('%c[CRITICAL ERROR]', 'color: red; font-weight: bold;', error);
                alert('Error crítico: ' + error.message);
            }
            
            return false;

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
            console.log('[READY] Initializing main form Select2 controls');
            $('.js-example-basic-single').select2();
            console.log('[SUCCESS] Main Select2 controls initialized');
            
            // Evento para cargar municipios cuando cambia departamento (Select2)
            const regionSelect = $('#field__custom-driver-region_id');
            console.log('[DEBUG] Setting up region change listener. Element exists:', regionSelect.length > 0);
            
            regionSelect.on('change.select2', function(e) {
                console.log('[EVENT] Region changed via Select2. Value:', $(this).val());
                // Pasar el elemento jQuery directamente en lugar de un evento falso
                getCitiesByRegion($(this));
            });
            
            console.log('[SUCCESS] Region change listener attached');
        });

        // Mostrar campo de marca personalizada si selecciona "Otra"
        function onBrandChange(event) {
            const val = event.target.value;
            const container = document.getElementById('custom_brand_container');
            if (val === 'otra') {
                container.style.display = 'block';
                document.getElementById('custom_brand_name').setAttribute('required', 'required');
            } else {
                container.style.display = 'none';
                document.getElementById('custom_brand_name').removeAttribute('required');
            }
            getModelsByBrand(event);
        }
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
