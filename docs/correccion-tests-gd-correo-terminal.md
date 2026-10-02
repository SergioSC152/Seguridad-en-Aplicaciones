# Corrección de tests GD y configuración Gmail por terminal

## Hallazgos y cambios

La captura del responsable muestra `LogicException: GD extension is not installed` en los tests de imagen. Fallaba la generación de imágenes ficticias, antes de validar el comportamiento del lote. Se modificó `tests/Feature/LivestockBatchImageTest.php` para usar el PNG real de `resources/branding/cowapp-logo.png` como archivo temporal de prueba. Se conservan casos de subida, reemplazo, eliminación, archivo falso/SVG/exceso de tamaño y autorización. Ya no se requiere GD para este archivo de tests; no se modificó PHP ni su configuración. El primer fallo de la captura no está completo: si persiste después de repetir los tests, registrar el mensaje completo.

Se añadió `app/Console/Commands/ConfigureGmail.php`, comando `cowapp:correo-gmail`, descubierto automáticamente por Laravel. Reutiliza `MailSettingsService` y el cast cifrado existente. No crea un administrador, no modifica `.env` ni agrega tablas, migraciones, rutas web o permisos. Quien lo ejecuta necesita acceso a la terminal del proyecto. La contraseña se solicita oculta, sin argumentos ni fallback visible. Solamente `--probar` realiza un envío real, dirigido a la misma cuenta emisora.

Se añadió `tests/Feature/ConfigureGmailTest.php`: actualización del registro, cifrado, rechazo de contraseña inválida y fallo SMTP sin imprimir secretos. El transporte de prueba de estos tests está simulado; no demuestra entrega real.

Referencia oficial: [comandos Artisan y preguntas ocultas](https://laravel.com/docs/12.x/artisan#writing-commands). El comando llama `secret` con fallback desactivado: si no puede ocultar la entrada, se detiene.

## Ejecución por el responsable en Git Bash

Desde la carpeta del proyecto:

```bash
php artisan config:clear
php artisan cowapp:correo-gmail --probar
```

Introduce el Gmail que enviará los mensajes. Después introduce su contraseña de aplicación, generada en Google con verificación en dos pasos. La contraseña no aparecerá en la terminal; no incluirla en el comando ni compartirla aquí. No es la contraseña del usuario CowApp ni la contraseña normal de Google.

El comando sustituye la configuración SMTP guardada (incluida una antigua de Mailtrap), que tiene prioridad sobre `.env`. Guarda host Gmail, 587, TLS, usuario y remitente iguales. No hace falta una cuenta administradora web para ejecutarlo. Si solo quieres guardar sin enviar, omite `--probar`.

Si Git Bash no permite entrada oculta, usar una terminal interactiva compatible; el comando no acepta entrada visible como alternativa. Si el servidor `php artisan serve` estaba activo, detenerlo con Ctrl+C y volver a iniciarlo después de limpiar configuración cuando sea necesario.

Interpretación del resultado:

- **Gmail aceptó el envío:** revisar Bandeja de entrada y Spam. Esto confirma aceptación SMTP, no garantiza entrega final.
- **Autenticación rechazada:** comprobar usuario, verificación en dos pasos y contraseña de aplicación; si se cambió la contraseña Google, generar otra contraseña de aplicación.
- **TLS/certificado:** revisar OpenSSL y certificados CA del PHP utilizado. No desactivar verificación TLS.
- **Conexión:** comprobar Internet, DNS y salida a puerto 587.
- **Error al guardar:** comprobar base de datos y migraciones existentes; no ejecutar `migrate:fresh` ni borrar datos.

El comando no imprime el texto completo de la excepción SMTP. Puedes compartir su clasificación y clase de error sin contraseña. Si la prueba SMTP funciona pero recuperación no llega, comprobar que el destinatario sea una cuenta registrada de CowApp y usar el último código recibido. Recuperación responde genéricamente para no revelar existencia de usuarios y usa envío directo, sin worker de cola.

## Repetir tests

```bash
php artisan test --filter=LivestockBatchImageTest
php artisan test --filter=ConfigureGmailTest
php artisan test --filter=PasswordRecoveryOtpTest
```

No ejecutados automáticamente. No marcar estos tests como aprobados hasta guardar sus salidas reales.

## Evidencias de cierre

| ID | Acción | Esperado | Estado |
| --- | --- | --- | --- |
| GT01 | Repetir tests de imágenes | Sin error GD; registrar resultado completo | Pendiente |
| GT02 | Configurar por terminal con `--probar` | Contraseña oculta; configuración cifrada; SMTP aceptado | Pendiente |
| GT03 | Revisar el correo de prueba | Mensaje CowApp recibido sin OTP | Pendiente |
| GT04 | Recuperar cuenta existente desde navegador | Último OTP recibido, cambio de contraseña y login válidos | Pendiente |
| GT05 | Guardar salida de tests de comando y recuperación | Casos correctos y negativos registrados | Pendiente |

Capturas sin secretos, OTP ni credenciales. Incorporar los resultados a la matriz del informe junto con la captura inicial. No iniciar otro módulo hasta confirmar.
