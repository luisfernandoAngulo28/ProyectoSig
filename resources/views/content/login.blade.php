@extends('layouts/master')

@section('header')

@endsection

@section('content')
<div id="page-content" class="page-wrapper" style="padding-top: 40px;">
    <!-- LOGIN SECTION START -->
    <div class="login-section mb-80">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="registered-customers">
                        <h6 class="widget-title border-left mb-50">Iniciar Sesión</h6>
                        <form action="{{ url('auth/login') }}" method="post">
                            <div class="login-account p-30 box-shadow">
                                <p>Si tiene una cuenta con nosotros, por favor inicie sesión.</p>
                                <input type="text" name="user" placeholder="Email">
                                <input type="password" name="password" placeholder="Contraseña">
                                <p><small><a href="#">Olvidó su Contraseña</a></small></p>
                                <button class="submit-btn-1 btn-hover-1" type="submit">Iniciar Sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- new-customers -->
                <div class="col-md-6">
                    <div class="new-customers">
                        <form action="{{ url('process/finish-sale') }}" method="post">
                            <h6 class="widget-title border-left mb-50">Aún no Tiene una cuenta activa? ingrese sus datos</h6>
                            <div class="login-account p-30 box-shadow">
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! Form::text('first_name',NULL,['placeholder'=>'Nombre','required'=>true]) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::text('last_name',NULL,['placeholder'=>'Apellido','required'=>true]) !!}
                                    </div>
                                    <div class="col-sm-12">
                                        {!! Form::email('email',NULL,['placeholder'=>'Dirección de Correo Electronico','required'=>true]) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        {!! Form::text('cellphone',NULL,['placeholder'=>'Telefono / Celular','required'=>true]) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="custom-select" id="payment_id" name="payment_id">
                                            <option value="defalt">Método de Pago</option>
                                            <option value="2">PagosTT</option>
                                        </select>
                                    </div>
                                </div>
                                {!! Form::password('password', [ 'placeholder'=>'Contraseña','required'=>true]) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="submit-btn-1 mt-20 btn-hover-1" type="submit" value="register">Registrarse</button>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="submit-btn-1 mt-20 btn-hover-1 f-right" type="reset">Limpiar</button>
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
@endsection

@section('script')
@endsection