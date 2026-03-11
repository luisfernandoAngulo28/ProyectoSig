<div id="hola"></div>
@extends('master::layouts/admin-2')

@section('content')
    <div class="">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Pasajero</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Inicio</a>
                                    </li>
                                    <li class="breadcrumb-item active">Pasajero
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section id="basic-horizontal-layouts">
                <div class="match-height">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Crear Pasajero | <a
                                            href="/customer-admin/model-list/user?passenger=true&f_role_user%5B%5D=3&button=&search=1"><i
                                                class="fa fa-arrow-circle-o-left"></i> Volver</a></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form method="POST" action="/customer-admin/model" accept-charset="UTF-8"
                                            name="create_user" id="create_user" role="form"
                                            class="form-horizontal prevent-double-submit" autocomplete="off"
                                            enctype="multipart/form-data"><input name="_token" type="hidden"
                                                value="ec94l0wwS6kraJ2kVERSqTDdGmbCb2IH1J57reNQ">
                                            <div class="row flex">
                                                {{-- INPUT PARA REDIRIR A OTRA PAGINA --}}
                                                <input class="form-control input-lg " id="redirect_custom"
                                                    autocomplete="off"
                                                    value="/customer-admin/model-list/user?passenger=true&f_role_user%5B%5D=3&button=&search=1"
                                                    name="redirect_custom" type="hidden" readonly>

                                                <div id="field_username" class="col-sm-6 flex-item "><label for="username"
                                                        class="control-label">Nombre de Usuario (*)</label><input
                                                        class="form-control input-lg " id="username" autocomplete="off"
                                                        name="username" type="text">
                                                    <div class="error">
                                                        {{ $errors->has('username') ? 'Debe llenar este campo' : '' }}</div>

                                                </div>

                                                <div id="field_name" class="col-sm-6 flex-item "><label for="name"
                                                        class="control-label">Nombre (*)</label><input
                                                        class="form-control input-lg " id="first_name" name="first_name"
                                                        type="text">
                                                    <div class="error">
                                                        {{ $errors->has('first_name') ? 'Debe llenar este campo' : '' }}</div>
                                                </div>

                                                <div style="display: none" id="field_name" class="col-sm-6 flex-item "><label for="name"
                                                        class="control-label">Nombre (*)</label><input
                                                        class="form-control input-lg " id="name" name="name"
                                                        type="text" value="test">
                                                    <div class="error">
                                                        {{ $errors->has('name') ? 'Debe llenar este campo' : '' }}</div>
                                                </div>



                                                <div id="field_last_name" class="col-sm-6 flex-item "><label for="last_name"
                                                        class="control-label">Apellidos</label><input
                                                        class="form-control input-lg " id="last_name" name="last_name"
                                                        type="text">
                                                    <div class="error">
                                                        {{ $errors->has('last_name') ? 'Debe llenar este campo' : '' }}
                                                    </div>
                                                </div>


                                                <div id="field_date_birth" class="col-sm-6 flex-item "><label
                                                        for="date_birth" class="control-label">Fecha de
                                                        nacimiento</label><input
                                                        class="form-control input-lg datepicker picker__input"
                                                        id="date_birth" name="date_birth" type="text" readonly=""
                                                        aria-haspopup="true" aria-expanded="false" aria-readonly="false"
                                                        aria-owns="date_birth_root">
                                                    <div class="picker" id="date_birth_root" aria-hidden="true">
                                                        <div class="picker__holder" tabindex="-1">
                                                            <div class="picker__frame">
                                                                <div class="picker__wrap">
                                                                    <div class="picker__box">
                                                                        <div class="picker__header"><select
                                                                                class="picker__select--year" disabled=""
                                                                                aria-controls="date_birth_table"
                                                                                title="Select a year">
                                                                                <option value="2018">2018</option>
                                                                                <option value="2019">2019</option>
                                                                                <option value="2020">2020</option>
                                                                                <option value="2021">2021</option>
                                                                                <option value="2022">2022</option>
                                                                                <option value="2023" selected="">2023
                                                                                </option>
                                                                                <option value="2024">2024</option>
                                                                                <option value="2025">2025</option>
                                                                                <option value="2026">2026</option>
                                                                                <option value="2027">2027</option>
                                                                                <option value="2028">2028</option>
                                                                            </select><select class="picker__select--month"
                                                                                disabled=""
                                                                                aria-controls="date_birth_table"
                                                                                title="Select a month">
                                                                                <option value="0">enero</option>
                                                                                <option value="1">febrero</option>
                                                                                <option value="2">marzo</option>
                                                                                <option value="3">abril</option>
                                                                                <option value="4">mayo</option>
                                                                                <option value="5">junio</option>
                                                                                <option value="6">julio</option>
                                                                                <option value="7">agosto</option>
                                                                                <option value="8" selected="">
                                                                                    septiembre</option>
                                                                                <option value="9">octubre</option>
                                                                                <option value="10">noviembre</option>
                                                                                <option value="11">diciembre</option>
                                                                            </select>
                                                                            <div class="picker__nav--prev" data-nav="-1"
                                                                                role="button"
                                                                                aria-controls="date_birth_table"
                                                                                title="Previous month"> </div>
                                                                            <div class="picker__nav--next" data-nav="1"
                                                                                role="button"
                                                                                aria-controls="date_birth_table"
                                                                                title="Next month"> </div>
                                                                        </div>
                                                                        <table class="picker__table" id="date_birth_table"
                                                                            role="grid" aria-controls="date_birth"
                                                                            aria-readonly="true">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="lunes">lun
                                                                                    </th>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="martes">mar
                                                                                    </th>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="miércoles">
                                                                                        mié</th>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="jueves">jue
                                                                                    </th>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="viernes">vie
                                                                                    </th>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="sábado">sáb
                                                                                    </th>
                                                                                    <th class="picker__weekday"
                                                                                        scope="col" title="domingo">dom
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1693195200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-08-28">28
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1693281600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-08-29">29
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1693368000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-08-30">30
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1693454400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-08-31">31
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1693540800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-01">1</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1693627200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-02">2</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1693713600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-03">3</div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1693800000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-04">4</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1693886400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-05">5</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1693972800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-06">6</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694059200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-07">7</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694145600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-08">8</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694232000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-09">9</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694318400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-10">10
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694404800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-11">11
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694491200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-12">12
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694577600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-13">13
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694664000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-14">14
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694750400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-15">15
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694836800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-16">16
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1694923200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-17">17
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695009600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-18">18
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus picker__day--today picker__day--highlighted"
                                                                                            data-pick="1695096000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-19"
                                                                                            aria-activedescendant="true">19
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695182400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-20">20
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695268800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-21">21
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695355200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-22">22
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695441600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-23">23
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695528000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-24">24
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695614400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-25">25
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695700800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-26">26
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695787200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-27">27
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695873600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-28">28
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1695960000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-29">29
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--infocus"
                                                                                            data-pick="1696046400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-09-30">30
                                                                                        </div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696132800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-01">1</div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696219200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-02">2</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696305600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-03">3</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696392000000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-04">4</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696478400000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-05">5</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696564800000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-06">6</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696651200000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-07">7</div>
                                                                                    </td>
                                                                                    <td role="presentation">
                                                                                        <div class="picker__day picker__day--outfocus"
                                                                                            data-pick="1696737600000"
                                                                                            role="gridcell"
                                                                                            aria-label="2023-10-08">8</div>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <div class="picker__footer"><button
                                                                                class="picker__button--today"
                                                                                type="button" data-pick="1695096000000"
                                                                                disabled=""
                                                                                aria-controls="date_birth">hoy</button><button
                                                                                class="picker__button--clear"
                                                                                type="button" data-clear="1"
                                                                                disabled=""
                                                                                aria-controls="date_birth">borrar</button><button
                                                                                class="picker__button--close"
                                                                                type="button" data-close="true"
                                                                                disabled=""
                                                                                aria-controls="date_birth">cerrar</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div><input type="hidden" name="date_birth_submit">
                                                    <div class="error">
                                                        {{ $errors->has('date_birth') ? 'Debe llenar este campo' : '' }}
                                                    </div>
                                                </div>

                                                <div id="field_cellphone" class="col-sm-6 flex-item "><label
                                                        for="cellphone" class="control-label">Celular (*)</label><input
                                                        class="form-control input-lg " id="cellphone" name="cellphone"
                                                        type="number">

                                                    <div class="error">
                                                        {{ $errors->has('cellphone') ? 'Debe llenar este campo' : '' }}
                                                    </div>
                                                </div>


                                                <div id="field_email" class="col-sm-6 flex-item "><label for="email"
                                                        class="control-label">Email (*)</label><input
                                                        class="form-control input-lg " id="email" name="email"
                                                        type="email">
                                                    <div class="error">
                                                        {{ $errors->has('email') ? 'Debe llenar este campo' : '' }}
                                                    </div>
                                                </div>

                                                <div id="field_password" class="col-sm-6 flex-item "><label
                                                        for="password" class="control-label">Contraseña (*)</label><input
                                                        class="form-control input-lg " id="password" autocomplete="off"
                                                        name="password" type="password" value="">
                                                    <div class="error">
                                                        {{ $errors->has('password') ? 'Debe llenar este campo' : '' }}
                                                    </div>
                                                </div>


                                                <div id="field_image" class="col-sm-6 flex-item "><label for="image"
                                                        class="control-label">Imagen</label>
                                                    <div class="file_container">
                                                        <div class="file_limitations">
                                                            <p>La imagen debe ser: JPG, JPEG, PNG o GIF.</p>
                                                        </div><input type="hidden" name="image">
                                                    </div><input class="form-control input-lg  fileupload" id="image"
                                                        data-type="image" data-folder="user-image" data-multiple="0"
                                                        data-count="0" name="uploader_image" type="file">
                                                    <div class="progress_bar">
                                                        <div class="bar" style="width: 0%;"></div><a
                                                            class="cancel_upload_button" href="#">Cancelar</a>
                                                    </div>
                                                    <div class="error_bar"></div>
                                                </div>

                                                <div id="field_gender" class="col-sm-6 flex-item "><label for="gender"
                                                        class="control-label">Género
                                                        (*)</label><select class="form-control" id="gender"
                                                        name="gender" tabindex="-1" aria-hidden="true">
                                                        <option value="" selected="selected">Seleccione una
                                                            opción...</option>
                                                        <option value="male">Masculino</option>
                                                        <option value="female">Femenino</option>
                                                    </select>
                                                </div>

                                                {{-- DATOS OCULTOS PERO NECESARIOS --}}


                                                <div id="field_status" class="col-sm-6 flex-item " style="display: none">
                                                    <label for="status" class="control-label">Estado (*) | <a
                                                            rel="status" class="unselect-radio"
                                                            href="#">X</a></label>
                                                    <div class="mt-radio-inline"><label class="mt-radio">Normal <input
                                                                checked class="field_status option_normal" id="status"
                                                                data-checkbox="true" name="status" type="radio"
                                                                value="normal"><span></span></label><label
                                                            class="mt-radio">Preguntar Contraseña <input
                                                                class="field_status option_ask_password" id="status"
                                                                data-checkbox="true" name="status" type="radio"
                                                                value="ask_password"><span></span></label><label
                                                            class="mt-radio">Confirmación Pendiente <input
                                                                class="field_status option_pending_confirmation"
                                                                id="status" data-checkbox="true" name="status"
                                                                type="radio"
                                                                value="pending_confirmation"><span></span></label><label
                                                            class="mt-radio">Bloqueado <input
                                                                class="field_status option_banned" id="status"
                                                                data-checkbox="true" name="status" type="radio"
                                                                value="banned"><span></span></label></div>
                                                </div>

                                                <div id="field_notifications_email" style="display: none"
                                                    class="col-sm-6 flex-item "><label for="notifications_email"
                                                        class="control-label">Notificaciones por
                                                        Email (*) | <a rel="notifications_email" class="unselect-radio"
                                                            href="#">X</a></label>
                                                    <div class="mt-radio-inline"><label class="mt-radio">No <input
                                                                class="field_notifications_email option_0"
                                                                id="notifications_email" data-checkbox="true"
                                                                name="notifications_email" type="radio"
                                                                value="0"><span></span></label><label
                                                            class="mt-radio">Si <input checked
                                                                class="field_notifications_email option_1"
                                                                id="notifications_email" data-checkbox="true"
                                                                name="notifications_email" type="radio"
                                                                value="1"><span></span></label></div>
                                                </div>
                                                <div id="field_role_user" class="col-sm-6 flex-item"
                                                    style="display: none"><label for="role_user"
                                                        class="control-label">Rango del Usuario (*) | <a rel="role_user"
                                                            class="unselect-radio" href="#">X</a></label>
                                                    <div class="mt-radio-inline">
                                                        <label class="mt-radio">passenger <input checked
                                                                class="field_role_user option_3" id="role_user"
                                                                data-checkbox="true" name="role_user" type="radio"
                                                                value="3"><span></span></label>
                                                    </div>
                                                </div>

                                                <div id="field_is_connect" class="col-sm-6 flex-item "
                                                    style="display: none"><label for="is_connect"
                                                        class="control-label">¿Esta Conectado?
                                                        (*)</label><select
                                                        class="form-control input-lg js-select2 select2-hidden-accessible"
                                                        id="is_connect" name="is_connect" tabindex="-1"
                                                        aria-hidden="true">
                                                        <option value="" disabled>Seleccione una
                                                            opción...</option>
                                                        <option value="0" selected>No</option>
                                                        <option value="1">Si</option>
                                                    </select>
                                                </div>



                                                <div id="field_is_verify" style="display: none"
                                                    class="col-sm-6 flex-item "><label for="is_verify"
                                                        class="control-label">¿Verificado?</label><select
                                                        class="form-control input-lg js-select2 select2-hidden-accessible"
                                                        id="is_verify" name="is_verify" tabindex="-1"
                                                        aria-hidden="true">
                                                        <option value="" selected="selected">Seleccione una
                                                            opción...</option>
                                                        <option value="0">No</option>
                                                        <option value="1" selected>Si</option>
                                                    </select>
                                                </div>


                                                <div id="field_type" class="col-sm-6 flex-item " style="display: none">
                                                    <label for="type" class="control-label">Clase</label>
                                                    <select class="form-control" id="type" name="type"
                                                        tabindex="-1" aria-hidden="true">
                                                        <option value="" selected="selected">Seleccione una
                                                            opción...</option>
                                                        <option value="representative">Representante</option>
                                                        <option value="customer" selected>Cliente</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12 left">
                                                    <input name="action_form" type="hidden" value="create">
                                                    <input name="model_node" type="hidden" value="user">
                                                    <br>
                                                    <button type="submit" name="button"
                                                        class="btn btn-primary mr-1 mb-1 btn-site waves-effect waves-light"
                                                        id="guardar">Guardar</button>
                                                </div>
                                            </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>
        <!-- // Basic Horizontal form layout section end -->

        <style>
            .flex-item {
                text-align: left
            }

            .flex-item label {
                padding: 30px 0px 5px 0px;
                font-size: 14px;
                font-weight: 600
            }

            .flex-item .mt-radio {
                padding: 20px 0px
            }
        </style>

    </div>
    </div>
@endsection
