<!--<!doctype html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">
  <style>
    html, body { margin: 0; height: 100%; font-family: 'Exo 2', sans-serif; }
    body { background-position: center center; background-size: contain; background-repeat: no-repeat; }
    .middle { padding-top: 100px; }
    .middle .img_block { width: 30%; display: inline-block; }
    .middle .img_block img { width: 80%; margin-top: 20%; margin-left: 20px; }
    /*.middle .left_block { width: 12%; display: inline-block; text-align: right; }*/
    .middle .right_block { width: 60%; display: inline-block; text-align: right; }
    .middle .right_block .name { margin-bottom: 11px; margin-top: 17px; }
    .middle .right_block .name h1 { font-size: 16px; color: #000; text-align: right; margin: 2px 0px; letter-spacing: 0px; }
    .middle .right_block p { margin: 2px 0px; font-size: 11px; }
    .middle .right_block p.sub-title { font-size: 9px; margin-top: 0px !important; margin-bottom: 3px; }
    .middle  p.space-bottom { margin-bottom: 6px; }
  </style>
</head>
<body style="background-image: url('{{ \Func::process_pdf_image(asset('assets/img/credencial.png')) }}')">
  <div class="middle">
    <div class="img_block">
      <img src="{{ \Func::process_pdf_image(asset('assets/img/nopic.png')) }}" />
    </div>
    <div class="right_block">
      <div class="name">
        <h1>Joaquín Klaus</h1>
        <h1>Barriga Berrios</h1>
      </div>
      <p class="space-bottom">C.I. {{ $item->ci_number }}</p>
      <p>2005-06-27</p>
      <p class="sub-title">Fecha de Nacimiento</p>
      <p>{{ $item->emergency_number }}<br></p>
      <p class="sub-title">Telf. Emergencia</p>
      <div class="final-space"></div>
    </div>
  </div>
</body>
</html>-->