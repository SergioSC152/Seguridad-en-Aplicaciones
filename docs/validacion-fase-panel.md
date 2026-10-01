# CowApp CRM: fase del panel y evidencias de auditoría

Fecha de implementación: 1 de octubre de 2026.

Estado: implementado; pendiente de validación manual y confirmación del responsable. No iniciar otra fase hasta que el responsable confirme que funciona.

## Alcance de esta fase

Restaurar la vista de `/dashboard`, reutilizar el tema visual existente, mostrar datos reales de la cuenta y respetar los permisos de los módulos comerciales. La ruta conserva `auth`; cerrar sesión conserva POST y CSRF. El panel está disponible para usuarios autenticados y las funciones administrativas restringidas mantienen su autorización en servidor.

Archivos creados:

- `resources/views/dashboard.blade.php`.
- `docs/validacion-fase-panel.md`.
- `docs/evidencias/panel/composer-audit-inicial.txt`.
- `docs/evidencias/panel/npm-audit-inicial.txt`.
- `docs/evidencias/panel/rutas-iniciales.txt`.
- `docs/evidencias/panel/tests-panel.txt`.

Archivos modificados:

- `app/Services/DashboardAnalyticsService.php`: las métricas de clientes, prospectos y oportunidades se consultan únicamente con el permiso correspondiente.
- `tests/Feature/DashboardAnalyticsTest.php`: casos adicionales de permisos y métricas comerciales de la cuenta.

No se incorporan rutas, migraciones, modelos, CRUD ni permisos nuevos. Se conserva Bootstrap y el tema compartido para mantener compatibilidad con las pantallas actuales. La configuración `.env` se mantiene fuera del repositorio.

## Entregables obligatorios de la actividad

La fuente principal es `Actividad_Auditoria_Pruebas_Seguridad_Laravel_Postman.pdf`. Sus requisitos tienen prioridad para la entrega; las guías anteriores sirven para construcción y ejecución de módulos.

La entrega final requiere:

1. Informe técnico PDF con portada, introducción, objetivos, descripción de la aplicación, metodología, herramientas y comandos, auditoría de dependencias, rutas/middleware, configuración, autenticación/autorización, validaciones/CSRF, imágenes, API/Postman, matriz de pruebas, matriz de hallazgos, resultados, recomendaciones, conclusiones y enlaces de GitHub/YouTube.
2. Evidencias de `composer audit`, `npm audit`, `php artisan route:list`, login/registro, autorización, validaciones, imágenes y Postman.
3. Repositorio GitHub actualizado sin secretos y README con instalación y pruebas.
4. Video de YouTube de 5 a 8 minutos que presente la aplicación, auditorías, rutas, autenticación, una prueba negativa, autorización, Postman, un control o hallazgo y una recomendación.

Este registro aporta únicamente la fase del panel. No sustituye las evidencias de las demás fases ni constituye un informe final aprobado. Los resultados no ejecutados permanecen pendientes. No publicar capturas de `.env`, APP_KEY, contraseñas, tokens, cookies ni credenciales SMTP. Ocultar esos datos también en capturas de Postman y del navegador.

## Preparación local

En esta fase se detectó que la base indicada en `.env` no existía. Se creó `dv_api` mediante `CREATE DATABASE IF NOT EXISTS` y se ejecutaron las 14 migraciones ya presentes con `php artisan migrate --no-interaction`. No se crearon migraciones nuevas ni se utilizaron comandos de borrado. `php artisan migrate:status` confirmó todas como ejecutadas. No se crearon usuarios ni se asignaron contraseñas.

Para la primera validación, crear una cuenta de prueba normal en `/register` con una contraseña elegida por el responsable. El correo reservado de administrador no se puede registrar por ese formulario. P06/P08 quedan pendientes hasta disponer de la cuenta administrativa y de cuentas con los permisos correspondientes; no usar el registro API para intentar obtener acceso raíz.

Si todavía no existe la cuenta administrativa, el responsable puede crearla localmente desde `php artisan tinker`, usando el correo configurado y una contraseña introducida en un prompt oculto. No mostrar esa entrada en el video ni en las capturas. Ejecutar dentro de Tinker:

```php
App\Models\User::firstOrCreate(['email' => config('cowapp.mail_settings_admin_email')], ['name' => 'Administrador CowApp', 'password' => Illuminate\Support\Facades\Hash::make(Laravel\Prompts\password('Contraseña del administrador (mínimo 8 caracteres)', required: true, validate: fn ($value) => strlen($value) >= 8 ? null : 'Usa al menos 8 caracteres.'))]);
```

`firstOrCreate` conserva una cuenta existente y su contraseña. No crea permisos nuevos. Luego salir con `exit`, iniciar sesión y usar el módulo existente de roles para preparar P06/P08. Esta creación queda a cargo del responsable; no se ha ejecutado automáticamente.

Usar una terminal en la carpeta del proyecto. No actualizar dependencias antes de registrar el estado inicial. `composer install` conserva las versiones de `composer.lock`; no equivale a `composer update`.

```powershell
php -v
composer --version
git status --short
git ls-files .env
composer audit --locked
composer outdated --direct --locked
npm.cmd audit
npm.cmd outdated
```

Capturar los resultados completos de las auditorías y registrar paquete, versión, problema, severidad y recomendación. Un comando fallido o sin acceso a red se registra como no ejecutado correctamente; no demuestra ausencia de vulnerabilidades.

Si Composer informa que no puede extraer ZIP, el entorno XAMPP permite habilitar la extensión solamente durante la instalación, sin editar `php.ini`:

```powershell
php -d extension=zip C:/composer/composer.phar install --no-interaction --prefer-dist --no-scripts
```

Para registrar las rutas y el estado de la base de datos:

```powershell
php artisan route:list -v
php artisan migrate:status
```

Si faltan tablas, revisar las migraciones pendientes antes de ejecutarlas. Esta fase no crea migraciones nuevas. No usar `migrate:fresh`, `db:wipe` ni `migrate:refresh` sobre los datos actuales.

El panel reutiliza CSS de Bootstrap y el tema existente. Su renderizado no requiere compilar Vite; el login existente sí referencia Vite. Si todavía no hay compilación ni servidor Vite, preparar sus archivos con:

```powershell
npm.cmd ci
npm.cmd run build
```

Preparación ya ejecutada en esta fase: instalación de PHP con el archivo lock, `npm.cmd ci --no-audit --no-fund` y `npm.cmd run build`. Vite compiló correctamente 57 módulos y generó el manifiesto del login. Los archivos compilados y las dependencias permanecen excluidos de Git. No se modificó `.env`, `php.ini` ni los archivos lock.

Para ejecutar localmente:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Usar `http://127.0.0.1:8000` para estas pruebas. Si se ejecuta mediante Apache, usar su URL real y conservar el mismo host durante login y logout. Para Postman, configurar `base_url` con esa URL; la respuesta de una API no usa la sesión web automáticamente.

## Matriz de validación manual del panel

Completar Resultado obtenido, HTTP observado y Estado después de ejecutar cada caso. Guardar captura de la pantalla y de la pestaña Network cuando corresponda. En DevTools activar Preserve log para observar redirecciones. No copiar los valores de cookies o tokens.

| ID | Procedimiento | Resultado esperado | HTTP esperado | Evidencia sugerida | Obtenido / HTTP / Estado |
| --- | --- | --- | --- | --- | --- |
| P01 | Abrir `/dashboard` en una ventana privada sin sesión | Redirección a login, sin métricas privadas | 302 hacia `/login`; después 200 | `P01-dashboard-sin-sesion.png` | Pendiente |
| P02 | Iniciar sesión con una cuenta existente y abrir `/dashboard` | Nombre de la cuenta, panel visible y métricas de sus datos | POST login 302; GET dashboard 200 | `P02-panel-autenticado.png` | Pendiente |
| P03 | Entrar con una cuenta sin clientes, lotes ni prospectos | Estado vacío; ningún valor ficticio; botón para registrar primer lote | 200 | `P03-panel-vacio.png` | Pendiente |
| P04 | Entrar con una cuenta normal sin permisos comerciales | No se muestran métricas/enlaces de clientes, prospectos u oportunidades | 200 | `P04-panel-sin-permisos.png` | Pendiente |
| P05 | Con esa cuenta escribir `/admin/clientes` y `/admin/roles` directamente | Acceso rechazado por servidor | 403 en ambas rutas | `P05-autorizacion-403.png` | Pendiente |
| P06 | Entrar con una cuenta que tenga solamente `clients.manage` | Clientes visibles; prospectos/oportunidades ocultos | 200 | `P06-permiso-clientes.png` | Pendiente |
| P07 | Comparar el panel de dos cuentas con inventario diferente | Cada cuenta muestra sus propios lotes/cantidades, incluso la raíz | 200 | `P07-cuenta-a.png`, `P07-cuenta-b.png` | Pendiente |
| P08 | Con permiso de oportunidades, comparar abiertas y ganadas contra sus registros | La suma abierta excluye ganadas/perdidas; no equivale a dinero cobrado | 200 | `P08-metricas-comerciales.png` | Pendiente |
| P09 | Pulsar Cerrar sesión y volver a solicitar `/dashboard` | Sesión cerrada y acceso al panel rechazado | POST logout 302; GET dashboard 302 | `P09-logout.png` | Pendiente |
| P10 | Con sesión activa, retirar el campo `_token` del formulario de logout en DevTools y enviarlo | Rechazo CSRF; la sesión sigue activa. Recargar para restaurar el formulario | POST logout 419 | `P10-csrf-419.png` | Pendiente |
| P11 | Solicitar `/logout` mediante GET | No permite cerrar sesión por GET | 405 | `P11-metodo-logout.png` | Pendiente |
| P12 | Probar el panel a 390 px y 1280 px de ancho; navegar con Tab | Menú, tarjetas y acciones utilizables; tabla con desplazamiento propio | 200 | `P12-movil.png`, `P12-escritorio.png` | Pendiente |

P06 y P08 requieren cuentas con permisos asignados mediante el módulo existente de roles. No se requiere implementar nuevos roles ni nuevos CRUD. Si no hay cuentas/datos para un caso, registrarlo como pendiente y explicar la precondición. Las pruebas de registro, API e imágenes se ejecutarán en sus respectivas fases, conservando los endpoints exigidos por la actividad.

El panel es un punto de entrada para cuentas autenticadas, no una función exclusiva de la cuenta raíz. Para el caso "usuario normal → administración" del informe, usar una ruta restringida como `/admin/roles` y demostrar su respuesta 403.

## Tests automatizados de esta fase

```powershell
php artisan test --filter=DashboardAnalyticsTest
```

Estos tests utilizan SQLite en memoria según `phpunit.xml`, sin modificar la base MySQL de trabajo. Cubren:

- Acceso sin autenticación.
- Renderizado de inventario y estado vacío.
- Aislamiento de inventario entre cuentas.
- Ocultamiento de métricas comerciales y rechazo de rutas restringidas sin permisos.
- Visualización del módulo concedido al rol.
- Métricas comerciales propias de la cuenta raíz y exclusión de operaciones cerradas del valor abierto.

No prueban CSRF efectivo en navegador, SMTP real, responsive visual ni Postman. Esas evidencias deben ejecutarse manualmente. Guardar la salida del comando como evidencia adicional y no sustituir con ella las evidencias obligatorias de la actividad.

Resultado ejecutado: `php vendor/phpunit/phpunit/phpunit --filter=DashboardAnalyticsTest --testdox`: **6 tests, 33 assertions, todos correctos**, PHP 8.2.12 y PHPUnit 11.5.56, con SQLite en memoria. El primer intento mostró una aserción que confundía un atributo de accesibilidad con texto visible; se corrigió para comprobar texto visible y se volvió a ejecutar. No se ejecutó la suite completa.

Comprobaciones adicionales: `php -l` en los dos archivos PHP modificados y `git diff --check`, correctos. `/dashboard` mantiene `web` y `auth`; `/logout` mantiene POST, `web` y `auth`. `.env` no está versionado y está excluido por `.gitignore`.

## Auditoría inicial de dependencias

Las versiones de los archivos lock se conservaron. Consultas realizadas el 1 de octubre de 2026:

| Comando | Resultado | Interpretación |
| --- | --- | --- |
| `composer audit --locked --format=json` | Código 1; 3 avisos en 2 paquetes | Avisos encontrados; no equivale a un fallo del panel |
| `npm.cmd audit --package-lock-only --json` | Código 1; 1 paquete afectado con severidad máxima alta | Axios agrupa varios avisos; no se debe confundir el número de paquetes con el de avisos |

| Paquete / versión fijada | Problema reportado | Severidad | Recomendación |
| --- | --- | --- | --- |
| Laravel 12.68.0 | XSS en información de la página de depuración, CVE-2026-102279 / GHSA-jh5r-qr3c-85q8 | Baja | Revisar parche compatible fuera de esta fase; desactivar debug en producción |
| league/commonmark 2.10.0 | Bypass de DisallowedRawHtml, GHSA-97jj-33gv-5xf9 | Media | Revisar actualización específica y uso real de Markdown |
| league/commonmark 2.10.0 | Denegación de servicio en extensión de tablas, GHSA-3q6v-r5mr-hxv8 | Alta | Revisar actualización específica y prueba del procesamiento de contenido no confiable |
| Axios 1.19.0 | Varios avisos de contaminación de prototipos, inyección de encabezados, redirecciones y denegación de servicio | Alta (máxima del paquete) | Revisar actualización específica y exposición real de los adaptadores usados |

Las auditorías no demuestran por sí solas que los avisos sean explotables en esta aplicación. `composer outdated` y `npm outdated` quedan pendientes de la validación de dependencias; no se realizaron actualizaciones masivas.

Salidas completas de Composer, NPM y rutas guardadas en `docs/evidencias/panel/`, junto con el registro de tests. Estas evidencias de terminal complementan las capturas requeridas. Las capturas manuales todavía deben guardarse por el responsable.

## Hallazgos para conservar en el informe

| ID | Hallazgo | Evidencia de código | Riesgo / impacto | Acción / estado |
| --- | --- | --- | --- | --- |
| H-P01 | El controlador pedía una vista dashboard inexistente | `DashboardController.php`, llamada a `view('dashboard')`; ausencia original de la vista | El acceso al panel podía fallar | Vista creada; pendiente de evidencia manual antes/después |
| H-P02 | El servicio calculaba métricas comerciales sin comprobar permisos | `DashboardAnalyticsService.php` | Exposición de resúmenes de un módulo no autorizado al usuario | Consultas condicionadas por permiso; verificar P04/P06 y tests |
| H-A01 | Registro API no reserva el correo raíz como el registro web | `Api/AuthController.php`, `RegisterUserRequest.php`, `User::isPlatformAdmin()` | Posible privilegio raíz si el correo configurado aún no tiene una cuenta | Pendiente de fase de autenticación; mantener fuera de publicación hasta resolver |
| H-E01 | Base MySQL indicada en `.env` inexistente | `php artisan migrate:status`, error MySQL 1049 inicial | Impedía iniciar la aplicación y enumerar rutas | Base creada y migraciones existentes ejecutadas; comprobación posterior correcta |
| H-D01 | Avisos de dependencias PHP/JavaScript | Auditorías Composer y NPM | Severidades de baja a alta; exposición por verificar | Estado inicial registrado; planificar mantenimiento específico después del cierre de fase |

No afirmar explotación de H-A01 sin una prueba controlada posterior. Conservar la diferencia entre un hallazgo por revisión estática y uno demostrado por ejecución.

## Checklist de cierre del panel

- [x] Vista del dashboard creada.
- [x] Reutilización del tema visual existente.
- [x] Métricas obtenidas de la base de datos y limitadas al propietario.
- [x] Consultas comerciales condicionadas por permisos del servidor.
- [x] Ruta del dashboard conserva autenticación.
- [x] Logout conserva POST y CSRF.
- [x] Datos variables escapados por Blade.
- [x] Estado vacío y diseño adaptable implementados.
- [x] Tests específicos y matriz de validación preparados.
- [x] Resultados automatizados registrados después de ejecución.
- [ ] Validación manual P01-P12 completada o pendientes justificados.
- [ ] Capturas revisadas para evitar secretos.
- [ ] Resultados y hallazgos incorporados al informe.
- [ ] Confirmación del responsable para cerrar la fase.

El informe PDF final, la publicación de GitHub y el video se completan con las evidencias reales de todas las fases; no se marcan como entregados en esta etapa.
