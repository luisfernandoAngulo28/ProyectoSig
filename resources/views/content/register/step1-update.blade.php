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
    </style>
    <div class="content__form" style="height: 1000px">
        <h3 class="title__form pb-1">Creación de tu Perfil</h3>
        <p class="m-0">¡Hola! Bienvenido al registro de conductor para formar parte de Fempile, por favor
            verifica que los campos estén correctamente llenados, toma en cuenta que revisaremos la información brindada
            para habilitarte a la aplicación, te confirmaremos por correo electrónico.
        </p>

        <br>
        <h3 class="title__form pb-1">Registro de datos personales</h3>
        <p>(*) Campos obligatorios.</p>
        <br>

        <form action="/customer-admin/register-driver/update/step1" method="POST" enctype="multipart/form-data">
            <div class="content__fields">
                <input type="hidden" name="driver_id" value={{ $driver->id }}>
                @include('includes.field', [
                    'id' => 'first_name',
                    'label' => 'Nombres (*)',
                    'name' => 'first_name',
                    'type' => 'text',
                    'hasError' => $errors->has('first_name'),
                    'error' => $errors->first('first_name'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'value' => $driver->first_name,
                ])

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
                    'value' => $driver->last_name,
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
                    'required' => true,
                    'value' => $driver->email,
                ])

                @include('includes.field', [
                    'id' => 'password',
                    'label' => 'Contraseña (*) (Crear tu Contraseña)',
                    'name' => 'password',
                    'type' => 'password',
                    'hasError' => $errors->has('password'),
                    'error' => $errors->first('password'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                ])

                @include('includes.field', [
                    'id' => 'cellphone',
                    'label' => 'Número de Teléfono (*)',
                    'name' => 'cellphone',
                    'type' => 'phone',
                    'hasError' => $errors->has('cellphone'),
                    'error' => $errors->first('cellphone'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'value' => $driver->cellphone,
                ])


                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-city_id"><b>Ciudad (*)</b></label>
                    <div class="area__select">
                        <select name="city_id" id="field__custom-city_id" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($cities as $city)
                                @if ($driver->city_id == $city->id)
                                    <option value="{{ $city->id }}" selected>{{ $city->name }}</option>
                                @else
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endif
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
                ]) --}}

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-organization_id"><b>Empresa (*)</b></label>
                    <span style="font-size: 12px; color:#4b4b4b;">Si tu empresa no se encuentra en la lista, por favor
                        comunícate con soporte.</span>

                    <div class="area__select">
                        <select name="organization_id" required id="field__custom-organization_id">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($organizations as $organization)
                                @if ($driver->organization_id == $organization->id)
                                    <option value="{{ $organization->id }}" selected>{{ $organization->name }}</option>
                                @else
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('organization_id'))
                        <p class="error__text">{{ $errors->first('organization_id') }}</p>
                    @endif
                </div>

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-rubro"><b>Rubro</b></label>
                    <span style="font-size: 12px; color:#4b4b4b;"></span>

                    <div class="area__select">
                        <select name="rubro_id" required id="field__custom-rubro">
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="taxi-moto">Taxi Moto</option>
                            <option value="taxi-auto">Taxi Auto</option>
                            <option value="servicio-encomienda">Servicio de Encomienda</option>

                            {{-- @foreach ($organizations as $organization)
                                @if ($driver->organization_id == $organization->id)
                                    <option value="{{ $organization->id }}" selected>{{ $organization->name }}</option>
                                @else
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endif
                            @endforeach --}}
                        </select>
                    </div>
                    @if ($errors->has('rubro'))
                        <p class="error__text">{{ $errors->first('rubro') }}</p>
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
                ])

                @include('includes.field', [
                    'id' => 'license_front_image',
                    'label' => 'Imagen del Anverso de la licencia (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'license_front_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('license_front_image'),
                    'error' => $errors->first('license_front_image'),
                ])

                @include('includes.field', [
                    'id' => 'license_back_image',
                    'label' => 'Imagen del reverso de la licencia (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'license_back_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('license_back_image'),
                    'error' => $errors->first('license_back_image'),
                ])

                @include('includes.field', [
                    'id' => 'image',
                    'label' => 'Imagen del Frontal de CI (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'ci_front_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_front_image'),
                    'error' => $errors->first('ci_front_image'),
                ])

                @include('includes.field', [
                    'id' => 'ci_back_image',
                    'label' => 'Imagen del Reverso del CI (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'ci_back_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('ci_back_image'),
                    'error' => $errors->first('ci_back_image'),
                ])

                @include('includes.field', [
                    'id' => 'tic_file',
                    'label' => 'Imagen del Frontal de Documento TIC',
                    'subtext' => '<span></span>',
                    'name' => 'tic_file',
                    'type' => 'file',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('tic_file'),
                    'error' => $errors->first('tic_file'),
                ])

                @include('includes.field', [
                    'id' => 'tic',
                    'label' => 'Número de TIC',
                    'subtext' => '<span></span>',
                    'name' => 'tic',
                    'type' => 'number',
                    'fill' => false,
                    'required' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('tic'),
                    'error' => $errors->first('tic'),
                    'value' => $driver->tic,
                ])

                @include('includes.field', [
                    'id' => 'license_number',
                    'label' => 'Número de Licencia (*)',
                    'name' => 'license_number',
                    'type' => 'text',
                    'hasError' => $errors->has('license_number'),
                    'error' => $errors->first('license_number'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'value' => $driver->license_number,
                ])

                @include('includes.field', [
                    'id' => 'license_expiration_date',
                    'label' => 'Fecha de expiración de la licencia (*)',
                    'name' => 'license_expiration_date',
                    'type' => 'date',
                    'hasError' => $errors->has('license_expiration_date'),
                    'error' => $errors->first('license_expiration_date'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'value' => $driver->license_expiration_date,
                ])


                @include('includes.field', [
                    'id' => 'number_of_passengers',
                    'label' => 'Número de pasajeros (*)',
                    'name' => 'number_of_passengers',
                    'type' => 'number',
                    'hasError' => $errors->has('number_of_passengers'),
                    'error' => $errors->first('number_of_passengers'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'value' => $driver->number_of_passengers,
                ])

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-gender"><b>Genero (*)</b></label>
                    <div class="area__select">
                        <select name="gender" id="field__custom-gender" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            @if ($driver->gender == 'male')
                                <option value="male" selected>Masculino</option>
                                <option value="female">Femenino</option>
                            @else
                                <option value="female" selected>Femenino</option>
                                <option value="male">Masculino</option>
                            @endif
                        </select>
                    </div>
                    @if ($errors->has('gender'))
                        <p class="error__text">{{ $errors->first('gender') }}</p>
                    @endif
                </div>

                @include('includes.field', [
                    'id' => 'active_trips',
                    'label' => '¿Viajes?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'is_active_for_career',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                    'checked' => $driver->active_trips,
                ])

                @include('includes.field', [
                    'id' => 'baby_chair',
                    'label' => '¿Silla para bebe?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'baby_chair',
                    'type' => 'switch',
                    'checked' => '',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1 d-none',
                    'checked' => $driver->baby_chair,
                ])
                @include('includes.field', [
                    'id' => 'travel_with_pets',
                    'label' => '¿Permite viaje con mascotas?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'travel_with_pets',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                    'checked' => $driver->travel_with_pets,
                ])
                @include('includes.field', [
                    'id' => 'fragile_content',
                    'label' => '¿Contenido frágil?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'fragile_content',
                    'type' => 'switch',
                    'checked' => '',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1 d-none',
                    'checked' => $driver->fragile_content,
                ])
                @include('includes.field', [
                    'id' => 'car_with_grill',
                    'label' => '¿Auto con parrilla?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'car_with_grill',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                    'checked' => $driver->car_with_grill,
                ])

                @include('includes.field', [
                    'id' => 'car_with_ac',
                    'label' => '¿Auto con aire acondicionado?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'car_with_ac',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                    'checked' => $driver->car_with_ac,
                ])
                @include('includes.field', [
                    'id' => 'car_electric',
                    'label' => '¿Auto eléctrico?',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'car_electric',
                    'type' => 'switch',
                    'checked' => 'checked',
                    'col_lg' => '',
                    'col_md' => '',
                    'col_sm' => '',
                    'col' => '',
                    'customClass' => 'content__fields_line ml-1',
                    'checked' => $driver->car_electric,
                ])

            </div>

            <br>
            <br>
            <h3 class="title__form pb-1">Registra tu Vehículo</h3>
            <p>(*) Campos obligatorios.</p>

            <br>
            <div class="content__fields">

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-city_id"><b>Ciudad (*)</b></label>
                    <div class="area__select">
                        <select name="city_id" id="field__custom-city_id" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            {{ $driver->driver_vehicles }}
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('city_id'))
                        <p class="error__text">{{ $errors->first('city_id') }}</p>
                    @endif
                </div>

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-vehicle_brand_id"><b>Marca de Vehiculo (*)</b></label>
                    <span style="font-size: 12px; color:#4b4b4b;">Si la marca de tu vehículo no se encuentra en la lista,
                        por favor comunícate con soporte.</span>
                    <div class="area__select">
                        <select name="vehicle_brand_id" onchange="getModelsByBrand(event)"
                            id="field__custom-vehicle_brand_id" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($vehiclesBrands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('vehicle_brand_id'))
                        <p class="error__text">{{ $errors->first('vehicle_brand_id') }}</p>
                    @endif
                </div>

                {{-- <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-vehicle_model_id"><b>Modelo de Vehículo (*)</b></label>
                    <span style="font-size: 12px; color:#4b4b4b;">Si el modelo de tu vehículo no se encuentra en la lista,
                        por favor comunícate con soporte.</span>

                    <div class="area__select">
                        <select name="vehicle_model_id" id="field__custom-vehicle_model_id" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($vehiclesModels as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
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
                ])

                {{-- @include('includes.field', [
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
                ])
                {{-- @include('includes.field', [
                    'id' => 'rua',
                    'label' => 'Número de RUAT (*)',
                    'name' => 'rua',
                    'type' => 'text',
                    'hasError' => $errors->has('rua'),
                    'error' => $errors->first('rua'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
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
                ]) --}}

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-type"><b>
                            Clase de Vehículo (*)</b></label>
                    <div class="area__select">
                        <select name="type" id="field__custom-type" required>
                            <option value="" selected>Seleccione una opción</option>
                            <option value="vagoneta">Vagoneta</option>
                            <option value="multiuso">Automóvil</option>
                            {{-- <option value="convertible">Convertible</option>
                            <option value="descapotable">Descapotable</option> --}}
                        </select>
                    </div>
                    @if ($errors->has('type'))
                        <p class="error__text">{{ $errors->first('type') }}</p>
                    @endif
                </div>

                @include('includes.field', [
                    'id' => 'vehicle_image',
                    'label' => 'Imagen frontal del Vehículo  (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'vehicle_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('vehicle_image'),
                    'error' => $errors->first('vehicle_image'),
                ])

                @include('includes.field', [
                    'id' => 'side_image',
                    'label' => 'Imagen de Costado del Vehículo  (*)',
                    'subtext' => '<span>(Aparecerá en tu perfil)</span>',
                    'name' => 'side_image',
                    'type' => 'file',
                    'fill' => false,
                    'required' => true,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'hasError' => $errors->has('side_image'),
                    'error' => $errors->first('side_image'),
                ])

                {{-- 
                <div class="content__field col-lg-6 col-md-6 col-sm-6 col-12">
                    <label for="color">Seleccionar color (*)</label>
                    <select data-colorselect required name="color">
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
            <br>

            <input type="checkbox" class="ml-1" id="terminos"> <label for="terminos">Aceptar Términos y
                Condiciones (*)</label>
            <br>

            <div class="col-12">
                <p class="text__terms">
                    Toca <b>“Registrarse”</b> para finalizar tu registro.
                </p>
            </div>

            <button type="submit" class="ml-1 btn btn-primary btn__submit">
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
                        `<option value="${element.id}">${element.name}</option>`; // Added closing angle bracket >
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
@endsection
