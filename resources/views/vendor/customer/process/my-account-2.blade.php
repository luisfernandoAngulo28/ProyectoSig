@extends('master::layouts/admin-2')
@include('helpers.meta')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/sales/store.css') }}">
@endsection

@section('content')

<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Editar Perfil</h2>
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url(config('customer.redirect_after_login')) }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="#">Mi Cuenta</a></li>
                    <li class="breadcrumb-item active">Editar Perfil</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div id="user-profile">

        <section id="profile-info">
            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="card">
                        <br>
                        <div class="center">
                          @if($customer->image)
                            <img class="img-fluid" src="{{ asset(Asset::get_image_path('customer-image', 'normal', $customer->image)) }}" />
                          @else
                            <img class="img-fluid" src="{{ asset('assets/admin/img/user.jpg') }}" />
                          @endif
                        </div>
                        @if(config('customer.fields.image'))
                        {{Form::open(array('url' => url('account/edit-image'), 'class'=>'account-settings-form', 'method' => 'post', 'files' => true))}}
                        <div style="padding: 0px 55px 0 55px;">
                            <div class="subir">
                                <div class="input_file">
                                    <label for="file1" class="file_label file_label_1">
                                      <i class="feather icon-upload" aria-hidden="true"></i>
                                      Cambiar foto de perfil
                                    </label>
                                    <input id="file1" type="file" class="file-item" name="image" />
                                </div>
                            </div><br>
                            <div class="center">
                                <input type="submit" class="btn btn-green-pro" value="Subir Foto de Perfil" />
                            </div>
                            {{ Form::close() }}
                        </div>
                        @endif
                        
                        <!--@if($customer->image)
                        <p><a href="{{ url('account/delete-image') }}" class="btn">Eliminar Foto</a></p>
                        @endif-->
                        

                        <div class="card-header">
                            <h4 style="width: 100%; text-align: center;">{{ $customer->name }}</h4>                                            
                        </div>
                        <div class="card-body">
                            <p>Intenta mantener tus datos siempre actualizados desde esta pantalla.</p>
                            @if(config('customer.fields.city')&&$customer->city)
                                <p><span class="p_title">Ciudad: </span>{{ $customer->city->name }}</p>
                            @endif
                            @if(config('customer.fields.address')&&$customer->address)
                            <p><span class="p_title">Dirección: </span>{{ $customer->address}} @if($customer->address_extra)
                                - {{ $customer->address_extra}}
                            @endif</p>
                            @endif
                            @if(config('customer.fields.invoice_data'))
                                @if($customer->nit_number)
                                    <div class="mt-1">
                                        <h6 class="mb-0">NIT:</h6>
                                        <p>{{ $customer->nit_number }}</p>
                                    </div>
                                @endif
                                @if($customer->nit_name)
                                    <div class="mt-1">
                                        <h6 class="mb-0">Razón Social:</h6>
                                        <p>{{ $customer->nit_name }}</p>
                                    </div>
                                @endif
                            @endif
                            <!--<div class="mt-1">
                                <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-facebook"></i></button>
                                <button type="button" class="btn btn-sm btn-icon btn-primary mr-25 p-25"><i class="feather icon-twitter"></i></button>
                                <button type="button" class="btn btn-sm btn-icon btn-primary p-25"><i class="feather icon-instagram"></i></button>
                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <h4 class="card-title">Editar datos de Cuenta</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form action="{{ url('account/edit-customer') }}" method="post" class="form form-horizontal">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Nombre:</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="text" class="form-control" name="first_name" id="first-name" value="{{ $customer->first_name }}">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-user"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Apellido:</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="text" class="form-control" name="last_name" id="last-name" value="{{ $customer->last_name }}">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-user"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Email:</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="email" class="form-control" name="email" id="email" value="{{ $customer->email }}">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-mail"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Celular:</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="number" class="form-control" name="cellphone" id="cellphone" value="{{ $customer->cellphone }}">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-phone"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(config('customer.fields.country'))
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Pais</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                {!! Form::select('country_id', $countries, $customer->country_id, ['class'=>'form-control custom-select']) !!}
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-map-pin"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(config('customer.fields.city'))
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Ciudad</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                {!! Form::select('city_id', $cities, $customer->city_id, ['class'=>'form-control custom-select']) !!}
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-map-pin"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(config('customer.fields.city'))
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Dirección</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="text" class="form-control" name="address" id="address" value="{{ $customer->address }}">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-map-pin"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(config('customer.fields.invoice_data'))
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Número de NIT (Opcional)</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                {{ Form::text('nit_number', $customer->nit_number, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: 4765754017']) }}
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-user"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Razón Social (Opcional)</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                {{ Form::text('nit_name', $customer->nit_name, ['class'=>'form-control', 'required'=>false, 'placeholder'=>'EJ: Diaz']) }}
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-user"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-md-8 offset-md-4 left">
                                                    <button type="submit" class="btn btn-primary mr-1 mb-1">Guardar datos</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <h4 class="card-title">Cambiar Contraseña</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form  action="{{ url('account/change-password') }}" method="post" class="form form-horizontal">
                                        <div class="form-body">
                                            <div class="row">
                                                                                                                                                                                                                        
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Nueva contraseña</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="password" id="password" class="form-control" name="password" placeholder="*********">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-lock"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Confirmar contraseña</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="position-relative has-icon-left">
                                                                <input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="*********">
                                                                <div class="form-control-position">
                                                                    <i class="feather icon-lock"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-8 offset-md-4 left">
                                                    <button type="submit" class="btn btn-primary mr-1 mb-1">Cambiar contraseña</button>
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
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        $("#file1").on("change", function (e) {
            var files = $(this)[0].files;
            if (files.length >= 2) {
                $(".file_label_1").text(files.length + " Files Ready To Upload");
            } else {
                var fileName = e.target.value.split("\\").pop();
                $(".file_label_1").text(fileName);
            }
        });
    });
</script>
@endsection