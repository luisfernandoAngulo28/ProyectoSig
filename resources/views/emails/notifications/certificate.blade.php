<!DOCTYPE html>
<html lang="es-ES">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<p>Estimado {{ $item->full_name }},</p>
		<p>Muchas gracias por haber participado de "Seminarios 360". Como se le indicó en el evento, se le adjunta el certificado de participación en PDF que también puede descargar desde la siguiente dirección:</p>
		<p><a target="_blank" href="{{ $file }}">{{ $file }}</a></p>
		<p>Saludos,</p>
		<p>360INVICTUS S.R.L.</p>
	</body>
</html>
