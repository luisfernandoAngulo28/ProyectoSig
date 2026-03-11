@extends('layouts/master')

@section('header')
<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-wrap">
                    <nav aria-label="breadcrumb">
                        <h1>Contacto {{-- <br><small>¡Póngase en contacto si tiene alguna duda de nuestro sitio La Ganga!</small> --}}</h1>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('inicio') }}">Inicio</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Nosotros</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container pt-50 pb-50">
    <div class="deals-wrapper bg-white">
        <div class="contact-area pt-0 pb-0">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-message">
                        <h2>Ubicación</h2>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3825.350795076425!2d-68.1299167!3d-16.5083786!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f20634313cdb9%3A0xad65877e620f5775!2sInstituto%20Internacional%20de%20Integraci%C3%B3n!5e0!3m2!1ses-419!2sbo!4v1672335908694!5m2!1ses-419!2sbo" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        {{-- <form action="{{ url('process/formulario') }}" method="post" class="contact-form">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <input name="name" id="name" placeholder="Nombre *" type="text" required>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <input name="email" id="email" placeholder="Correo Electrónico *" type="text" required>
                                </div>
                                <div class="col-12">
                                    <div class="contact2-textarea text-center">
                                        <textarea placeholder="Mensaje *" name="message" id="message" class="form-control2" required=""></textarea>
                                    </div>
                                    <div class="contact-btn">
                                        <button class="btn btn__bg" type="submit">Enviar</button>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-center">
                                    <p class="form-messege"></p>
                                </div>
                            </div>
                        </form> --}}
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="contact-info">
                        {!! App\Content::find(2)->content !!}
                        <ul>
                            @foreach($nodes['contacts'] as $contact)
                                <li><i class="fa fa-home"></i> Úbicanos en {{ $contact->city }}</li>
                                <li><i class="fa fa-map-marker"></i> {{ $contact->address }}</li>
                                <li><i class="fa fa-phone"></i> (+591) {{ $contact->phone }}</li>
                                @if($contact->phone_2)
                                    <li><i class="fa fa-phone"></i> (+591) {{ $contact->phone_2 }}</li>
                                @endif
                                @if($contact->email)
                                <li><i class="fa fa-envelope-o"></i>
                                    {{-- <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a> --}}
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                </li>
                                @endif
                                @if($contact->email_2)
                                    <li><i class="fa fa-envelope-o"></i>
                                        <a href="mailto:{{ $contact->email_2 }}">{{ $contact->email_2 }}</a>
                                    </li>
                                @endif 
                                @if($contact->schedule)
                                    <li><i class="fa fa-clock-o"></i> {{ $contact->schedule }}</li>
                                @endif
                            @endforeach
                            <li>
                                <div class="share-icon mt-18">
                                    @foreach($nodes['socials'] as $social)
                                        {{-- <a href="{{ $social->url }}" title="{{ $social->code }}"><i class="fa fa-{{ $social->code }}"></i></a> --}}
                                        <a target="_blank" href="{{ $social->url }}" title="{{ $social->code }}"><i class="fa fa-{{ $social->code }}"></i></a>
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endsection

@section('script')
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCDacJcoyPCr-jdlP9HK93h3YKNyf710J0"></script>
@endsection