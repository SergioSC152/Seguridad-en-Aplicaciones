# Ajuste solicitado: opciones de lotes, imagen y recuperación por Gmail

Fecha: 1 de octubre de 2026. Estado: código preparado; migración, tests y envío real pendientes del responsable. No avanzar a otro módulo hasta validar.

## Revisión y alcance

Proyecto Laravel 12.68.0 con MVC, Form Requests, Services, Policies y Blade/Bootstrap existentes. Se reutiliza el módulo Lotes y su catálogo de categorías por cuenta. No se incorporan tablas de razas, fincas o potreros ni un nuevo módulo de correo.

Las sugerencias de raza incluyen Brahman, Angus, Hereford, Holstein, Jersey, Gyr, Nelore, Simmental, Pardo suizo, Criollo y Cruce/mestizo. Son ayudas de entrada; no implican datos registrados ni características veterinarias. El campo permite escribir otra opción. Al guardar el lote se crea o reutiliza la categoría de la cuenta en la misma transacción. Las categorías existentes permanecen disponibles. Finca y potrero sugieren valores ya usados por la misma cuenta y permiten escribir otros. Los estados conservan las opciones actuales.

La imagen es opcional: JPG/PNG/WebP, máximo 5 MB y 6000 × 6000 píxeles; validación backend de contenido, sin SVG. Se guarda con nombre generado en Storage público y con `image_path` e `image_url` en MySQL. No se acepta una URL arbitraria ni un nombre de archivo introducido por el usuario. Las fotos son públicas mediante su URL; no subir documentación confidencial. Editar sin archivo conserva la foto; reemplazar/quitar/eliminar el lote limpia el archivo anterior después de guardar correctamente la base de datos. Si falla la escritura del registro se intenta limpiar el archivo nuevo.

## Archivos y rutas

Creado:

- `database/migrations/2026_10_01_000010_add_image_to_livestock_batches_table.php`.
- `tests/Feature/LivestockBatchImageTest.php`.
- Este documento.

Modificados:

- `config/cowapp.php`: sugerencias de razas.
- `app/Models/LivestockBatch.php`: ruta y URL de imagen.
- `app/Http/Requests/StoreLivestockBatchRequest.php` y `UpdateLivestockBatchRequest.php`: imagen y elección de categoría existente/nueva.
- `app/Services/LivestockBatchService.php`: categoría, almacenamiento y limpieza.
- `app/Http/Controllers/Admin/LivestockBatchController.php`: sugerencias de ubicación limitadas a la cuenta.
- `resources/views/admin/livestock-batches/index.blade.php`: opciones, imagen y miniatura.
- `resources/views/admin/settings/mail.blade.php`: explicación de Gmail/Mailtrap y prioridad de configuración.
- `docs/fases-desarrollo-crm.md`: ajuste del punto de validación actual.

Se conservan rutas GET/POST `/admin/lotes`, GET `/admin/lotes/{batch}/edit`, PUT/PATCH/DELETE `/admin/lotes/{batch}` y el CRUD de `/admin/categorias-ganado`. Sin rutas ni permisos nuevos; Policies conservan propiedad. Se conserva `/admin/configuracion/correo`, restringida a la cuenta administradora, y las rutas existentes de recuperación.

## Comandos para Git Bash

Ejecutar desde el proyecto, uno por uno y guardar las salidas:

```bash
php artisan migrate
php artisan config:clear
php artisan view:clear
php artisan test --filter=LivestockBatchImageTest
php artisan test --filter=LivestockManagementTest
php artisan test --filter=MailSettingsTest
php artisan test --filter=PasswordRecoveryOtpTest
```

No ejecutados automáticamente. Los tests de imágenes usan Storage simulado y SQLite en memoria. Tras el error GD informado, se ajustaron para usar el PNG existente sin generar imágenes con GD; ver `correccion-tests-gd-correo-terminal.md`. No representan entrega SMTP real. No se requiere compilación por estos cambios Blade/config. Si `public/storage` ya existe y el logo carga, conservar el enlace; únicamente si falta ejecutar `php artisan storage:link`.

Referencia oficial: [Storage público](https://laravel.com/docs/12.x/filesystem#the-public-disk) y [validación de imágenes](https://laravel.com/docs/12.x/validation#rule-image).

## Por qué no llega el correo y configuración Gmail

Revisión del archivo de entorno sin exponer secretos: `MAIL_MAILER=smtp`, host `sandbox.smtp.mailtrap.io`, puerto 2525 y usuario/contraseña sin configurar. Sin credenciales no se puede autenticar ese transporte. Además, [Mailtrap Sandbox](https://docs.mailtrap.io/email-sandbox/overview) captura mensajes en su bandeja de pruebas y no los entrega a destinatarios reales.

Si existe una fila `mail_settings`, `AppServiceProvider` aplica esa configuración sobre la del entorno. Por eso cambiar solamente `.env` puede no cambiar el servidor efectivo. Usar la pantalla existente es la opción más directa. El envío de recuperación usa `send`, no cola: no hace falta un worker para este flujo. Su respuesta es genérica para no revelar si una cuenta existe; ese mensaje no confirma entrega. El servicio registra la clase de excepción en caso de fallo, sin registrar el OTP ni la contraseña SMTP.

Para Gmail con el transporte SMTP actual:

1. Activar verificación en dos pasos en la cuenta que enviará los mensajes.
2. Abrir [Contraseñas de aplicación](https://myaccount.google.com/apppasswords), crear una llamada CowApp y copiarla una vez. No usar la contraseña normal de Google.
3. Entrar a CowApp como administrador y abrir `/admin/configuracion/correo`.
4. Configurar los valores de la siguiente tabla y guardar. Introducir el secreto solamente en el campo de contraseña, nunca en el chat, GitHub o capturas.

| Campo | Valor |
| --- | --- |
| Servidor | `smtp.gmail.com` |
| Puerto | `587` |
| Cifrado | TLS (STARTTLS) |
| Usuario | Dirección Gmail completa de la cuenta emisora |
| Contraseña | Contraseña de aplicación generada; copiar sin espacios de separación |
| Remitente | La misma dirección Gmail emisora |
| Nombre | CowApp |

La contraseña se cifra con el cast `encrypted` existente. Las contraseñas de usuario y OTP continúan almacenadas como hash. No regenerar APP_KEY: se necesita para descifrar la configuración guardada.

[Google explica](https://support.google.com/accounts/answer/185833?hl=es) que la verificación en dos pasos es requisito y que cuentas de organización, Protección Avanzada o ciertas configuraciones con llaves pueden impedir esta opción. Si no aparece, comunicar cuál de esos casos aplica sin enviar secretos. [Configuración SMTP oficial](https://support.google.com/a/answer/176600?hl=es). Cambiar la contraseña de Google revoca las contraseñas de aplicación; en ese caso generar otra y actualizar CowApp.

## Matriz de evidencias

| ID | Prueba | Resultado esperado | HTTP esperado | Resultado/evidencia |
| --- | --- | --- | --- | --- |
| LI01 | Cuenta sin categorías: elegir sugerencia Brahman y guardar lote | Categoría propia creada, lote asociado | POST 302 | Pendiente |
| LI02 | Escribir raza distinta y guardar; repetir raza en otro lote | Nueva opción disponible; categoría propia reutilizada | POST 302 | Pendiente |
| LI03 | Usar categoría existente; introducir finca/potrero nuevos y crear otro lote | Opciones de ubicación sugeridas sin bloquear otros valores | GET 200 / POST 302 | Pendiente |
| LI04 | Subir JPG/PNG válido y consultar fila del lote en phpMyAdmin | Foto visible; ruta y URL registradas | POST 302 / imagen GET 200 | Pendiente |
| LI05 | Enviar texto renombrado JPG, SVG o archivo mayor a 5 MB | Rechazo backend; no nuevo lote ni categoría | POST 302 con errores | Pendiente |
| LI06 | Editar sin foto; después reemplazar foto | Primero conserva; después cambia URL y elimina archivo previo | PUT 302 | Pendiente |
| LI07 | Marcar Quitar imagen; después subir otra y eliminar lote | URL nula al quitar; archivos eliminados en ambos casos | PUT/DELETE 302 | Pendiente |
| LI08 | Otra cuenta intenta editar/eliminar lote ajeno | 403 y foto original conservada | 403 | Pendiente |
| LI09 | Formulario a 390 px y escritorio; seleccionar opciones y archivo | Controles utilizables, miniatura proporcionada | GET 200 | Pendiente |
| CO01 | Guardar SMTP Gmail como administrador | Configuración guardada; secreto no se vuelve a mostrar | PUT 302 | Pendiente |
| CO02 | En ventana privada solicitar recuperación de una cuenta registrada | Correo recibido con OTP; revisar también Spam | POST 302; entrega por comprobar | Pendiente |
| CO03 | Usar OTP válido y cambiar contraseña | Login con contraseña nueva; anterior rechazada; OTP consumido | Redirecciones 302 | Pendiente |
| CO04 | Introducir OTP incorrecto/vencido y correo desconocido | Sin cambio de contraseña; respuesta genérica sin revelar cuenta | 302 con mensajes pertinentes | Pendiente |

Guardar capturas con IDs y salidas de tests. Ocultar OTP, contraseñas, cookies, tokens y direcciones personales antes de publicar. Si CO02 falla, informar si la configuración SMTP está guardada y la clase de excepción registrada en `storage/logs/laravel.log`; no compartir el archivo completo ni credenciales. No desactivar verificación TLS para resolver fallos de certificado.

## Checklist

- [x] Reutilización del módulo y catálogos por cuenta, sin tablas adicionales para sugerencias.
- [x] Validación backend de imagen, tamaño y dimensiones.
- [x] URL en base de datos, nombres generados y limpieza preparada.
- [x] CSRF, Policies y aislamiento de cuenta conservados.
- [x] Tests y matriz preparados, credenciales fuera del código.
- [ ] Migración y tests ejecutados por el responsable.
- [ ] LI01–LI09 con resultados y evidencias.
- [ ] SMTP Gmail configurado y entrega real CO01–CO04 validada.
- [ ] Confirmación del responsable antes de continuar.
