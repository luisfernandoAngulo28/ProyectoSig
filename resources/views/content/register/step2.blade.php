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
    <div class="content__form">
        <h3 class="title__form">Registro de Vehículo</h3>
        <form action="/customer-admin/register-driver/step2" method="POST" enctype="multipart/form-data">
            <div class="content__fields">

                @include('includes.field', [
                    'id' => 'name',
                    'label' => 'Nombre de Perfil',
                    'name' => 'name',
                    'type' => 'text',
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'required' => true,
                    'disabled' => true,
                    'value' => $driver->first_name . ' ' . $driver->last_name,
                ])

                <input type="text" name="driverId" id="driverId" value="{{ $driver->id }}" style="display: none">

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-city_id"><b>Ciudad (*)</b></label>
                    <div class="area__select">
                        <select name="city_id" id="field__custom-city_id" required>
                            <option value="" selected disabled>Seleccione una opción</option>
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
                        <p class="error__text">{{ $errors->first('city_id') }}</p>
                    @endif
                </div>

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-vehicle_model_id"><b>Modelo de Vehículo (*)</b></label>
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
                </div>

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
                ])
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
                @include('includes.field', [
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
                ])
                @include('includes.field', [
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
                ])
                {{-- @include('includes.field', [
                    'id' => 'tmov',
                    'label' => 'Número de TMOV',
                    'name' => 'tmov',
                    'type' => 'text',
                    'hasError' => $errors->has('tmov'),
                    'error' => $errors->first('tmov'),
                    'fill' => false,
                    'col_lg' => 'col-lg-6',
                    'col_md' => 'col-md-6',
                    'customClass' => 'd-none',
                    'required' => true,
                ]) --}}

                <div class="content__field col-lg-6 col-md-6 col-12">
                    <label for="field__custom-type"><b>
                            Clase de Vehículo (*)</b></label>
                    <div class="area__select">
                        <select name="type" id="field__custom-type" required>
                            <option value="" selected>Seleccione una opción</option>
                            <option value="vagoneta">Vagoneta</option>
                            <option value="multiuso">Automóvil</option>
                            <option value="convertible">Convertible</option>
                            <option value="descapotable">Descapotable</option>
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


                <div class="content__field col-lg-6 col-md-6 col-sm-6 col-12">
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
                </div>



            </div>

            <br>
            <br>
            <div class="col-12">
                <p class="text__terms">
                    Toca <b>“Registrarse y aceptar”</b> para finalizar el registro.
                    Recuerda que si no existe alguna marca de vehiculo o modelo puedes contactarte con nosotros
                </p>
            </div>


            <button type="submit" class="ml-1 btn btn-primary btn__submit">
                <span class="text__third"><b>Registrarse y continuar</b></span>
            </button>

    </div>

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
