@extends('master::layouts/admin')
@section('title', 'Log In')

@section('content')
  <div class="main-content main-content-2">
    <div class="login">
      <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
          @if($blocked_time==0)
            
          @if(session()->has('confirmation_url')&&session()->get('confirmation_url'))
            <h2>Verificar Email</h2>
            <p>Si desea que volvamos a enviar el email de confirmación a su cuenta de correo, haga click aquí:</p>
            <p><a class="btn btn-site" href="{{ session()->get('confirmation_url') }}">Enviar Email de Confirmación</a><br><br></p>
            <div class="page-bar"></div>
          @endif

          {!! Form::open(array('name'=>'login_form', 'role'=>'form', 'url'=>'auth/login', 'class'=>'form-horizontal prevent-double-submit')) !!}
            <h2 class="col-sm-offset-3 col-sm-9">INICIAR SESIÓN</h2>
            <p class="col-sm-offset-3 col-sm-9">¿Primera vez aquí? Ingresa con tu carnet de identidad y la contraseña temporal "12345678".</p>
        	<p class="col-sm-offset-3 col-sm-9">Una vez ingreses se te solicitará que cambies tu contraseña.</p>
            @if($failed_attempts>0)
              <h3 class="col-sm-offset-3 col-sm-9">Intentos Fallidos de Ingreso: {{ $failed_attempts }}</h3>
            @endif

            <div class="form-group">
              {!! Form::label('user', 'Carnet de Identidad / Email', ['class'=>'col-sm-3 control-label']) !!} 
              <div class="col-sm-6">
                {!! Form::text('user', NULL, ['class'=> 'form-control']) !!}
              </div>
              <div class="col-sm-offset-3 col-sm-6 error">{{ $errors->first('user') }}</div>
            </div>
            <div class="form-group">
              {!! Form::label('password', 'Contraseña', ['class'=>'col-sm-3 control-label']) !!} 
              <div class="col-sm-6">
                {!! Form::password('password', ['class'=> 'form-control']) !!}
              </div>
              <div class="col-sm-offset-3 col-sm-6 error">{{ $errors->first('password') }}</div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-6">
                {!! HTML::link('password/recover', 'Olvidaste tu contraseña?') !!}
              </div>
            </div>
            @if(config('solunes.nocaptcha_login'))
              <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                  {!! NoCaptcha::display() !!}
                </div>
              </div>
            @endif
            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-6">
                {!! Form::submit('Iniciar Sesión',['class' => 'btn btn-site']) !!}
              </div>
            </div>
          {!! Form::close() !!}
          @else
            <h2 class="col-sm-offset-1 col-sm-9">Iniciar Sesión</h2>
            <h3 class="col-sm-offset-1 col-sm-9">Cometió muchos intentos fallidos para iniciar sesión, por lo que debe esperar {{ $blocked_time }} minutos para volverlo a intentar.</h3>
            <h3 class="col-sm-offset-1 col-sm-9"><a href="{{ url('auth/login') }}">Recargar página</a> | 
            {!! HTML::link('password/recover', 'Olvidaste tu contraseña?') !!}</h3>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
  @if(config('solunes.nocaptcha_login'))
    {!! NoCaptcha::renderJs(config('solunes.main_lang')) !!}
  @endif
@endsection