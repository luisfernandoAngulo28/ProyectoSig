@extends('layouts/subadmin-register')

@section('content')
    <div class="p-4">

        <div class="modal__header">
            <div class="content__party">
                  <img style="width: 150px" src="{{ asset('assets/img/admin-logo.png') }}" alt="PARTY">
                <h3>¡ TE DAMOS LA BIENVENIDA ESTIMADO DRIVER¡</h3>
            </div>
            <p style="text-align: justify;">
                Estamos muy emocionados de tenerte en nuestro equipo. Trabajaremos arduamente para brindarte una agradable
                experiencia.
            </p>
            <p style="text-align: justify;">
                Posteriormente revisaremos tu solicitud pendiente, y te notificaremos a la brevedad, mediante tu correo
                electrónico que nos proporcionaste.
            </p>
            <p style="text-align: justify;">
                Tienes que saber que nuestra tecnología, esta manos de profesionales competentes, es por esta razón que
                garantizamos una excelente gestión de calidad, y mejora continua en cada proceso.
            </p>
            <p> <strong> 
                La aprobación de tu solicitud se aceptará en un lapso máximo de 24 horas o antes.</strong>
            </p> 
        </div>

        {{-- <div class="content__fields">
            <div class="content__items-hj">
                <div class="img___hj">
                    <img src="{{ asset('assets/img/yaservis/img_1.png') }}" alt="Image">
                </div>
                <div class="desc__hj">
                    <h5>Nuestras Redes Sociales</h5>
                    <p>¡Hazte visible en todo momento! Agrega tus redes sociales para que otros usuarios puedan
                        conocer más sobre ti dentro y fuera de la app.</p>
                </div>
            </div>
            <div class="content__items-hj">
                <div class="img___hj">
                    <img src="{{ asset('assets/img/yaservis/img_2.png') }}" alt="Image">
                </div>
                <div class="desc__hj">
                    <h5>Aparición exclusiva en búsquedas</h5>
                    <p>Aumenta la seguridad y confianza en tus servicios para nuestros usuarios.</p>
                </div>
            </div>
            <div class="content__items-hj">
                <div class="img___hj">
                    <img src="{{ asset('assets/img/yaservis/img_3.png') }}" alt="Image">
                </div>
                <div class="desc__hj">
                    <h5>Verificación por YaServis</h5>
                    <p>Aumenta la seguridad y confianza en tus servicios para nuestros usuarios.</p>
                </div>
            </div>
            <div class="content__items-hj">
                <div class="img___hj">
                    <img src="{{ asset('assets/img/yaservis/img_4.png') }}" alt="Image">
                </div>
                <div class="desc__hj">
                    <h5>Videos e imágenes ilimitadas</h5>
                    <p>Complementa tu perfil con videos auténticos e imágenes ilimitadas para llamar la atención y
                        mostrar lo que puedes hacer.</p>
                </div>
            </div>
            <div class="content__items-hj">
                <div class="img___hj">
                    <img src="{{ asset('assets/img/yaservis/img_5.png') }}" alt="Image">
                </div>
                <div class="desc__hj">
                    <h5>¡Y mucho más!</h5>
                </div>
            </div>
        </div> --}}

        <div class="modal__buttons">
            {{-- <p class="w-full">Si tienes alguna pregunta, no dudes en <a
                    href="https://api.whatsapp.com/send?phone=+59162567735&text=Hola..." target="_blank">Contactarnos</a>.</p> --}}
            {{-- <br> --}}
            {{-- <a class="btn btn-secondary w-full" href="/account/login/mMLZggHrrFgfF">Volver a la plataforma</a> --}}
        </div>
    </div>
@endsection
