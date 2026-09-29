<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0"
    style="max-width:600px;background:#fff;margin:40px auto;border-radius:12px;overflow:hidden;">
<tr><td style="background:#022766;padding:30px;text-align:center;color:white;">
    <h1 style="margin:0;">{{ config('app.name') }}</h1>
</td></tr>
<tr><td style="padding:35px;">
    <h2>Nuevo mensaje de contacto</h2>
    <p>Se ha recibido un nuevo mensaje desde el sitio web.</p>
    <hr>
    <p><strong>Nombre:</strong> {{ $name }}</p>
    <p><strong>Correo:</strong> {{ $email }}</p>
    <p><strong>Mensaje:</strong></p>
    <div style="background:#f4f6f8;padding:20px;border-radius:8px;">
        {{ $message }}
    </div>
</td></tr>
<tr><td style="background:#f4f6f8;padding:20px;text-align:center;font-size:12px;color:#666;">
    Este mensaje fue generado automáticamente desde el sitio web.
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
