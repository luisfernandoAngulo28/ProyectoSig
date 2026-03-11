<!doctype html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    @font-face {
      font-family: 'pantonbold_italic';
      src: url('{{ asset("assets/fonts/panton/panton_bold_italic-webfont.woff2") }}') format('woff2'),
           url('{{ asset("assets/fonts/panton/panton_bold_italic-webfont.woff") }}') format('woff');
      font-weight: normal;
      font-style: normal;

    }
    @font-face {
      font-family: 'pantonsemibold_italic';
      src: url('{{ asset("assets/fonts/panton/panton_semibold_italic-webfont.woff2") }}') format('woff2'),
           url('{{ asset("assets/fonts/panton/panton_semibold_italic-webfont.woff") }}') format('woff');
      font-weight: normal;
      font-style: normal;

    }
    @font-face {
      font-family: 'pantonregular';
      src: url('{{ asset("assets/fonts/panton/panton-webfont.woff2") }}') format('woff2'),
           url('{{ asset("assets/fonts/panton/panton-webfont.woff") }}') format('woff');
      font-weight: normal;
      font-style: normal;

    }

    html, body { margin: 0; height: 100%; font-family: 'pantonregular'; }
    .top { text-align: right; margin-bottom: 50px; }
    .top img { width: 90%; height: auto; }
    .middle { position: relative; max-width: 1150px; margin: auto; text-align: center; background: url("{{ asset('assets/img/pdf-watermark.png') }}") center center no-repeat; background-size: 70%; }
    .middle h1 { color: #004da2; font-family: 'pantonbold_italic'; font-weight: normal; font-size: 50px; }
    .middle h3 { color: #000; font-family: 'pantonsemibold_italic'; font-weight: normal; margin-top: 40px; font-size: 26px; }
    .middle .name-block { width: 75%; margin: auto; padding-top: 150px; color: #000; font-family: 'pantonbold_italic'; font-weight: bold; border-bottom: 3px solid #004da2; font-size: 45px; padding-bottom: 5px; }
    .middle .name-block.small { font-size: 32px; }
    .middle .name-block.super-small { font-size: 30px; }
    .middle p { color: #000; font-size: 22px; margin-top: 30px; font-family: 'pantonsemibold_italic'; font-weight: normal; }
    .middle p strong {  font-family: 'pantonbold_italic'; }
    .middle .signature { margin-top: 10px; }
    .bottom { position: absolute; width: 100%; left: 0; right: 0; bottom: 0; }
    .bottom .footer-text { position: absolute; top: 3%; left: 3%; color: #fff;  }
    .bottom .footer-text p { font-size: 22px; margin-bottom: 0; }
    .bottom .footer { float: left; width: 80%; }
    .bottom .logo { float: right; width: 21%; margin-left: -3%; margin-right: 0; margin-top: 2px; }
    .bottom .logo img { width: 44%; height: auto; }
    .bottom .logo img.first-logo { margin-right: 3%; }
  </style>
</head>
<body>
  <div class="top"><img src="{{ asset('assets/img/pdf-top.png') }}" /></div>
  <div class="middle">
    <h1>Certificado de Participación</h1>
    <h3>Por medio de la presente, la agencia 360Invictus, certifica que:</h3>
    <div class="name-block @if(strlen($item->full_name)>32) super-small @elseif(strlen($item->full_name)>20) small @endif ">{{ mb_strtoupper($item->full_name, 'UTF-8') }}</div>
    <p>Ha sido partícipe del evento <strong>Charla Magistral con César Farias</strong> en fecha 28 de septiembre de 2017,<br>
    llevado a cabo en el Centro de Eventos y Convenciones del Colegio Médico.</p>
    <div class="signature"><img src="{{ asset('assets/img/pdf-signature.png') }}" /></div>
  </div>
  <div class="bottom">
    <div class="footer-text">
      <p>info@360invictus.com<br>76561628<br>70121666<br>www.360invictus.com</p>
    </div>
    <img class="footer" src="{{ asset('assets/img/pdf-footer.png') }}" />
    <div class="logo">
      <img class="first-logo" src="{{ asset('assets/img/pdf-logo.png') }}" />
      <img src="{{ asset('assets/img/logo-udf.png') }}" />
    </div>
  </div>
</body>
</html>