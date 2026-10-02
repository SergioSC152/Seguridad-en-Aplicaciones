# CowApp: elaboración unificada y validación

Fecha: 1 de octubre de 2026. Laravel 12.68, PHP 8.2, MySQL y Blade. El aplicativo móvil continúa separado. Esta fase unificada reemplaza, por autorización expresa del responsable, la elaboración de un módulo por fase. No reemplaza las evidencias obligatorias de la guía.

**Estado:** código preparado; migraciones, ejecución y resultados de pruebas pendientes en el equipo del responsable. No se declara validación en producción.

## Implementación

| Área | Implementado | Límite de esta versión |
| --- | --- | --- |
| Acceso | OTP con expiración epoch de 600 segundos desde el envío exitoso, casillas individuales, bloqueo por correo tras cinco contraseñas incorrectas, MFA opcional | Bloqueo temporal de 15 minutos; usa el último OTP solicitado |
| Clientes y leads | Ficha 360, predio ICA, crédito, VIP, documentos privados, scoring A1/B1/B2 y conversión sin duplicar | Scoring por reglas explícitas; revisión sanitaria interna, sin conexión a registros oficiales |
| Pipeline | Cinco etapas comerciales, métricas y clientes VIP sin ventas durante 45 días | Alerta en panel; no envío automático de campañas |
| Lotes | Imagen con URL en BD, razas existentes y nuevas, propósito, publicación, disponibilidad y registro de pesajes/RFID | API de mediciones disponible; conexión física requiere adaptador del equipo |
| Productos y agenda | CRUD de productos/servicios y actividades vinculadas | Formularios y datos propios por usuario |
| Cotizaciones | Merma, precio/kg, deducciones configurables y cálculo backend en centavos | No sustituye asesoría tributaria ni emite factura DIAN |
| Contratos y ventas | Enlace privado, aceptación por OTP enviado por correo, fecha/IP/hash, venta única por contrato, pagos y guía | Confirmación electrónica básica; OTP por WhatsApp pendiente de plantilla/proveedor |
| Remates | Consola administrativa, pujas manuales, adjudicación y actualización periódica | No plataforma pública de pujas ni integración con martillo físico |
| CMS y portal | Banners, testimonios, equipo, galería, contacto, noticias existentes, catálogo y captación | Publica solamente registros activos autorizados |
| WhatsApp | Envío por Cloud API y webhook con firma HMAC, bandeja persistida | Requiere cuenta Meta, credenciales, permisos y webhook HTTPS; no simula entregas |
| Seguridad | Policies, Form Requests, propiedad de datos, documentos privados, MFA y bitácora | Bitácora operativa con actualización periódica; no almacenamiento forense inmutable |

Se mantienen las vistas Bootstrap existentes y se aplica la referencia del ZIP: verde, oliva y ámbar, tarjetas, navegación lateral y adaptación móvil. Se reutilizan componentes Blade y servicios simples. No se añaden microservicios, repositorios genéricos ni un framework modular adicional.

## Migraciones

- `2026_10_01_000011_add_otp_epoch_expiration`: expiración OTP independiente de zona horaria de MySQL.
- `2026_10_01_000012_create_commercial_workspace`: productos, cotizaciones, ventas, agenda, contenidos, documentos, eventos de seguridad, remates, pujas y campos complementarios.
- `2026_10_01_000013_create_whatsapp_messages`: mensajes de WhatsApp.
- `2026_10_01_000014_add_guide_date_and_measurements`: fecha de registro de guía e historial de mediciones.

Las migraciones anteriores conservan datos. No ejecutar `migrate:fresh`.

## Comandos que ejecuta el responsable

Desde Git Bash, en la carpeta del proyecto:

```bash
php artisan migrate
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Si todavía no existe la cuenta configurada en `COWAPP_ADMIN_EMAIL`:

```bash
php artisan cowapp:admin
```

El comando pide contraseña oculta y confirmación; no crea otra cuenta si el correo ya existe. No hay una carpeta llamada `admin` que debas crear para esta configuración.

Para evidencias de rutas y pruebas esenciales:

```bash
php artisan route:list
php artisan test --filter='SecurityTimingTest|UnifiedWorkspaceTest|PasswordRecoveryOtpTest'
```

Los tests preparados cubren el límite de diez minutos, bloqueo por correo desde IP distinta, MFA, permisos, cálculo, conversión, venta sin duplicados y rechazo de webhook sin firma. **No se han ejecutado en esta elaboración.** No se ejecutan auditorías o suites completas repetidas por defecto.

Si `public/storage` ya existe, no repetir `storage:link`. Si falta:

```bash
php artisan storage:link
```

En `.env`, `APP_URL` debe coincidir con la dirección real utilizada, incluyendo puerto, por ejemplo `http://127.0.0.1:8000` en desarrollo. Un enlace localhost no sirve para un cliente en otro equipo. Las credenciales SMTP guardadas en BD tienen prioridad sobre `.env`; conservar la configuración que ya envía correos. No publicar `.env`, APP_KEY, contraseñas ni OTP.

## Rutas y permisos

| Área | Ruta principal | Permiso |
| --- | --- | --- |
| Productos | `/admin/productos` | `products.manage` |
| Actividades | `/admin/actividades` | `activities.manage` |
| Contenidos | `/admin/portal` | `content.manage` |
| Cotizaciones | `/admin/cotizaciones` | `quotes.manage` |
| Ventas | `/admin/ventas` | `sales.manage` |
| Remates | `/admin/remates` | `auctions.manage` |
| WhatsApp | `/admin/whatsapp` | `leads.manage` |
| Auditoría | `/admin/seguridad` | `audit.view` |
| Seguridad personal | `/admin/mi-seguridad` | Usuario autenticado |
| Catálogo | `/catalogo` | Público |
| Contrato | `/contratos/{token}` | Token privado vigente |

El listado exacto de rutas, incluyendo nombres y endpoints API, se obtiene con `route:list`. Cada operación privada valida autorización y propietario en backend. El administrador de plataforma usa el correo configurado, no un correo escrito en el código.

## Integraciones pendientes de datos externos

WhatsApp usa variables privadas `WHATSAPP_ACCESS_TOKEN`, `WHATSAPP_PHONE_NUMBER_ID`, `WHATSAPP_VERIFY_TOKEN`, `WHATSAPP_APP_SECRET` y `WHATSAPP_GRAPH_VERSION`. Obtenerlas del proyecto Meta correspondiente, configurar una versión Graph vigente y un callback HTTPS `/api/whatsapp/webhook`. El secreto de aplicación verifica `X-Hub-Signature-256`. No introducir credenciales en JavaScript. El estado inicial es sin conexión hasta configurar datos reales.

Los documentos ICA/FEDEGÁN se cargan y revisan internamente; no se afirma certificación oficial. Las recomendaciones relacionan propósito de lotes y productos; no hay modelo de IA externo. Los precios mostrados salen de ventas propias, no de un feed de mercado.

Las deducciones son configurables. No se aplica universalmente 0,75 % sobre ventas de ganado: [FEDEGÁN distingue la cuota sobre leche y sobre sacrificio](https://www.fedegan.org.co/preguntas-frecuentes?page=2). Verificar tarifas y supuestos de retención con el responsable tributario antes de utilizarlos.

## Pruebas manuales y evidencias

Guardar resultados reales y capturas sin datos sensibles. Estado inicial de todas: **pendiente**.

| ID | Prueba | Resultado esperado / evidencia |
| --- | --- | --- |
| U01 | Aplicar migraciones y abrir panel | Migraciones DONE y páginas visibles sin error |
| U02 | Solicitar OTP y usarlo antes de 10 minutos; repetir después del límite | Aceptado antes de 600 segundos, rechazado desde el límite; contador coherente |
| U03 | Cinco contraseñas incorrectas para el mismo correo | Quinto intento bloquea; contraseña correcta no entra durante 15 minutos; luego permite |
| U04 | Activar MFA con contraseña actual | Acceso exige OTP adicional; código incorrecto no autentica |
| U05 | Convertir lead dos veces | Un cliente y una oportunidad; scoring según campos |
| U06 | Subir documento del cliente y probar otra cuenta | Descarga privada propia; cuenta ajena recibe 403 |
| U07 | Crear lote con imagen y publicar | URL en BD; catálogo muestra únicamente lotes publicados y activos |
| U08 | Crear/editar producto y actividad | Validación, mensajes y persistencia correctos |
| U09 | Cotizar 1000 kg, merma 5 %, $10.000/kg, retención 1,5 %, comisión 3 % | Bruto $9.500.000, neto $9.072.500; totales no manipulables desde formulario |
| U10 | Enviar contrato, confirmar OTP y registrar venta dos veces | Aceptación registrada y una sola venta |
| U11 | Pago mayor al total; despacho sin guía | Ambos rechazados; pago válido y despacho con guía aceptados |
| U12 | Abrir remate, registrar pujas y cerrar | Rechaza puja inferior; mayor puja adjudicada al cliente correspondiente |
| U13 | Crear banner/testimonio/equipo/galería | Solo contenido publicado aparece en portal |
| U14 | Formulario de consignación | Crea lead del propietario correcto; exige consentimiento |
| U15 | Revisar métricas con usuarios diferentes | Datos propios; sin exponer ventas o clientes ajenos |
| U16 | Revisar auditoría tras cambios e intentos fallidos | Evento/IP/severidad, sin contraseñas ni códigos |
| U17 | API de medición con token propio/ajeno | 201 propia, 403 ajena; registra historial |
| U18 | Webhook sin firma y conexión Meta real cuando disponible | Sin firma 403; integración real queda pendiente hasta credenciales |
| U19 | Formulario sin CSRF; logout y atrás/adelante | CSRF rechazado; no permite operar una sesión cerrada |
| U20 | Portal y panel a 390 px y escritorio | Navegación, formularios y tarjetas utilizables |

## Checklist de seguridad

- [x] Contraseñas y OTP mediante Hash; OTP y tokens privados sin texto plano en BD.
- [x] Credenciales SMTP existentes cifradas; secretos fuera del frontend.
- [x] Form Requests y Policies, validación de propietario, CSRF y límites de solicitudes.
- [x] Documentos en disco privado; imágenes con validación MIME/tamaño y URL en BD.
- [x] Cotizaciones aceptadas protegidas; venta sin duplicados; bitácora sin secretos.
- [ ] Ejecutar y guardar resultados de pruebas y revisión de configuración del despliegue.
- [ ] Confirmar APP_DEBUG=false y HTTPS en producción.

## Checklist funcional y entregables

- [x] Código y vistas de los módulos descritos para una versión base.
- [x] Migraciones, rutas, permisos, servicios y pruebas esenciales preparados.
- [x] Plan de validación manual y documentación de límites de integración.
- [ ] Aplicar migraciones y validar U01–U20 con resultados reales.
- [ ] Guardar salidas de tests y evidencias de errores corregidos.
- [ ] Completar informe obligatorio: matrices de hallazgos/pruebas, evidencias, conclusiones y recomendaciones según guía.
- [ ] Adjuntar los resultados requeridos de auditoría de dependencias y Postman cuando corresponda al informe; no fabricar resultados.
- [ ] Publicar repositorio con README y sin secretos, y video explicativo de 5–8 minutos solicitado por la actividad.
- [ ] Confirmación final del responsable antes de publicar o declarar cerrada la fase.

El inventario de archivos creados/modificados está en [inventario-fase-unificada.md](inventario-fase-unificada.md). Las correcciones previas de imágenes, correo y tests sin GD se conservan. No se han enviado correos ni ejecutado migraciones automáticamente durante esta elaboración.
