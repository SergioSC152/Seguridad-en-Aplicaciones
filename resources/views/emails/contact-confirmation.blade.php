<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de recepción de mensaje</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0"
    style="max-width:600px;background:#fff;margin:40px auto;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05);">
<tr><td style="background:#022766;padding:30px;text-align:center;color:white;">
    <h1 style="margin:0;font-size:24px;">{{ config('app.name') }}</h1>
</td></tr>
<tr><td style="padding:35px;">
    <h2 style="color:#022766;margin-top:0;">¡Hola {{ $name }}!</h2>
    <p>Hemos recibido tu mensaje correctamente a través de nuestro formulario de contacto.</p>
    <p>Nuestro equipo revisará tu solicitud y se pondrá en contacto contigo lo más pronto posible.</p>
    <hr style="border:0;border-top:1px solid #e2e8f0;margin:25px 0;">
    <p style="color:#64748b;font-size:14px;margin-bottom:0;">Gracias por comunicarte con nosotros.</p>
</td></tr>
<tr><td style="background:#f4f6f8;padding:20px;text-align:center;font-size:12px;color:#666;">
    Este mensaje fue generado automáticamente por el sistema de {{ config('app.name') }}.
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
