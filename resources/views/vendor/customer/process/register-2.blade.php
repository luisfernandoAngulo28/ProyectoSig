@extends('master::layouts/admin-2-clean')
@include('helpers.meta')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/sales/store.css') }}">
@endsection

@section('content')
<section class="row flexbox-container">
    <div class="col-xl-8 col-11 d-flex justify-content-center">
        <div class="card bg-authentication rounded-0 mb-0">
            <div class="row m-0">
                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                  <a href="{{ url('inicio') }}">
                    <img src="{{ asset('assets/img/logo-l.jpg') }}" alt="logo" class="w-100">
                  </a>
                </div>
                <div class="col-lg-6 col-12 p-0">
                    <div class="card rounded-0 mb-0 px-2">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <h4 class="mb-0">Regístrate</h4>
                            </div>
                        </div>
                        <p class="px-2">Si ya tienes una cuenta, ingresa con tu correo electrónico / carnet de identidad y contraseña.</p>
                        <div class="card-content">
                            <div class="card-body pt-1">
                              <form method="post" action="{{ url('process/registro') }}">
                                <div class="row">
                                  <div class="col-sm-6">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                      <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Nombre(s)" required>
                                      <div class="form-control-position">
                                          <i class="feather icon-user"></i>
                                      </div>
                                      <label for="first_name">Nombre(s)</label>
                                    </fieldset>
                                  </div>
                                  <div class="col-sm-6">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                      <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Apellido(s)" required>
                                      <div class="form-control-position">
                                          <i class="feather icon-user"></i>
                                      </div>
                                      <label for="last_name">Apellido(s)</label>
                                    </fieldset>
                                  </div>
                                  <div class="col-sm-6">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                      <input type="text" class="form-control" id="ci_number" name="ci_number" placeholder="CI - Carnet de Identidad" required>
                                      <div class="form-control-position">
                                          <i class="feather icon-user"></i>
                                      </div>
                                      <label for="ci_number">CI - Carnet de Identidad</label>
                                    </fieldset>
                                  </div>
                                  <div class="col-sm-6">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                      <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                                      <div class="form-control-position">
                                          <i class="feather icon-mail"></i>
                                      </div>
                                      <label for="email">Email</label>
                                    </fieldset>
                                  </div>
                                  <div class="col-sm-6">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                      <input type="text" class="form-control" id="cellphone" name="cellphone" placeholder="Teléfono Celular" required>
                                      <div class="form-control-position">
                                          <i class="feather icon-phone"></i>
                                      </div>
                                      <label for="cellphone">Teléfono Celular</label>
                                    </fieldset>
                                  </div>
                                  <div class="col-sm-6">
                                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                                      <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña Segura" required>
                                      <div class="form-control-position">
                                          <i class="feather icon-lock"></i>
                                      </div>
                                      <label for="password">Contraseña</label>
                                    </fieldset>
                                  </div>
                                </div>
                                <div class="form-group d-flex justify-content-between align-items-center">
                                    <div class="text-right"><a href="{{ url('account/login/'.$token.'/'.$agency_token) }}" class="card-link">¿Ya tiene una cuenta? Iniciar Sesión</a></div>
                                </div>
                                @if(config('customer.different_customers_by_agency'))
                                <input type="hidden" id="agency_token" name="agency_token" value="{{ $agency_token }}" />
                                @endif
                                @if(request()->has('redirect_url'))
                                <input type="hidden" id="redirect_url" name="redirect_url" value="{{ urldecode(request()->input('redirect_url')) }}" />
                                @endif
                                <button type="submit" class="btn btn-primary float-right btn-inline">Registrarme</button>
                              </form>
                            </div>
                        </div>
                        <div class="login-footer">
                            <div class="divider">
                                <div class="divider-text">O</div>
                            </div>
                            <div class="footer-btn d-inline">
                              @if(config('solunes.socialite_google'))
                                <a href="{{ url('/auth/google/'.$agency_token) }}" class="btn btn-google" style="color: #fff;"><span class="fa fa-google"></span></a>
                              @endif
                              @if(config('solunes.socialite_facebook'))
                                <a href="{{ url('/auth/facebook/'.$agency_token) }}" class="btn btn-facebook" style="color: #fff;"><span class="fa fa-facebook"></span></a>
                              @endif
                              @if(config('solunes.socialite_twitter'))
                                <a href="{{ url('/auth/twitter/'.$agency_token) }}" class="btn btn-twitter white"><span class="fa fa-white"></span></a>
                              @endif
                              @if(config('solunes.socialite_github'))
                                <a href="{{ url('/auth/github/'.$agency_token) }}" class="btn btn-github" style="color: #fff;"><span class="fa fa-github"></span></a>
                              @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
  <!--<script>
    new CBPFWTabs(document.getElementById('tabs'));
  </script>-->
@endsection