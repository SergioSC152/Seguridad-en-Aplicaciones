# Límites de texto: validación

Se conservan los límites existentes por módulo y se completan los faltantes. Los formularios usan maxlength para impedir exceso al escribir o pegar; el backend rechaza valores superiores aunque se quite el atributo o se envíen mediante API. No se truncan contraseñas ni datos silenciosamente.

| Campo | Máximo de caracteres |
| --- | --- |
| Nombre de cuenta y correo | 100 |
| Nombre de cliente, prospecto o producto | 160 |
| Teléfono y documento comercial | 40 |
| Municipio/departamento | 100 |
| Dirección | 255 |
| Categoría/raza nueva | 100 |
| Código de lote / identificación animal | 50 / 80 |
| RFID / registro ICA / guía | 100 |
| Finca en lotes / finca en prospectos | 150 / 160 |
| Rol y descripción del rol | 80 / 255 |
| Cotización / oportunidad: título | 160 / 180 |
| Notas comerciales y consulta pública | 3000 |
| Descripción de producto, contenido CMS, notas de lote/actividad | 5000 |
| Condiciones de cotización | 10000 |
| Cuerpo de noticia | 20000 |
| URL del contenido | 2048 |
| Contraseña: login, registro, recuperación, seguridad personal | 25 |

Por solicitud del responsable, el máximo de contraseña de acceso es 25 caracteres y el de nombre de cuenta/correo es 100. Si una cuenta existente tenía una contraseña de más de 25 caracteres, debe restablecerla mediante recuperación; no se modifica ni trunca su hash. SMTP conserva su límite previo de 1024 para no alterar credenciales de proveedores. Los mensajes WhatsApp conservan 4096. Los códigos OTP tienen exactamente seis dígitos. Los campos numéricos y archivos conservan sus validaciones propias; maxlength no sustituye esas reglas.

Se configura APP_LOCALE=es y APP_FALLBACK_LOCALE=es, y se incorporan traducciones de validación, atributos, autenticación, recuperación y paginación en lang/es. Configuración en caché requiere `php artisan config:clear`. No se traducen logs técnicos ni stack traces.

Cambios: LoginController, API AuthController, RegisterUserRequest, ResetPasswordWithOtpRequest, SecurityController, NewsController y vistas de acceso, registro, recuperación, catálogo, formularios compartidos, cotizaciones, noticias, multimedia, contacto y lotes.

## Comandos del responsable

```bash
php artisan view:clear
php artisan config:clear
php artisan test --filter=TextLimitsTest
```

No hay migraciones ni compilación frontend. Se prepararon tres pruebas de límites backend; no se ejecutaron por el agente.

## Evidencias manuales

1. Pegar un nombre superior al límite: el formulario impide excederlo.
2. Crear un registro con el tamaño máximo permitido: guardar correctamente.
3. Enviar desde Postman un nombre o contraseña superior al máximo: recibir error de validación y no crear/autenticar al usuario.
4. Crear producto o contenido CMS: comprobar límite de nombre/título, descripción y enlace.
5. En noticias, enviar 20001 caracteres: rechazo backend; con 20000 y demás datos válidos, aceptar.

Revisión estática realizada. Resultados de ejecución y capturas pendientes del responsable.
