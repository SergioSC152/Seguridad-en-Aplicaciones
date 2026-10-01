# Corrección del registro y logo CowApp

Fecha: 1 de octubre de 2026. Corrección de la fase en validación; no se inicia un módulo nuevo.

## Hallazgo y solución

La etiqueta de cierre del título de `auth/register.blade.php` estaba dividida como `</t` y `itle>`. El navegador interpretaba el resto del documento como texto del título y dejaba la página sin el formulario visible. La captura proporcionada por el responsable es evidencia del estado anterior.

Se corrigió el título y se conservaron el controlador, Form Request, CSRF y almacenamiento de contraseñas con hash. Se incorporaron límites de longitud, autocompletado y atributos accesibles del botón de mostrar contraseña.

El logo original se conserva sin transformación en `resources/branding/cowapp-logo.png`. Un seeder valida su MIME real PNG, que sea una imagen legible y el límite de 5 MB; copia el archivo al disco público y crea o actualiza su registro multimedia. La base de datos guarda:

- `path`: `branding/cowapp-logo.png`, ruta física relativa al disco público.
- `url`: `/storage/branding/cowapp-logo.png`, URL pública relativa.
- Nombre, MIME, tamaño y estado.

La URL relativa conserva el dominio/puerto actual. La imagen no se almacena como binario en MySQL. La nueva columna URL es nullable para conservar los registros existentes. Repetir el seeder actualiza el mismo registro y no crea otro logo. El mismo componente Blade consulta el registro activo en inicio, login, registro y panel. Si todavía no existe el logo, muestra el nombre CowApp y mantiene visible el formulario.

Las operaciones existentes de carga y reemplazo en multimedia/noticias también guardan la URL correspondiente al archivo nuevo. No se agregaron rutas, permisos ni un módulo de configuración.

## Archivos

Creado: migración `2026_10_01_000009_add_url_to_media_table.php`, seeder `CowAppLogoSeeder.php`, parcial `cowapp-logo.blade.php`, imagen original en `resources/branding/`, test `RegistrationLogoTest.php` y este registro.

Modificado: vistas `auth/register`, `auth/login`, `welcome`, `dashboard`; modelo `Media`; `AppServiceProvider`; controladores existentes `Admin/MediaController` y `Admin/NewsController` para sincronizar la URL de los archivos.

## Comandos que ejecuta el responsable en Git Bash

Desde la carpeta del proyecto, ejecutar uno por uno y guardar su salida:

```bash
php artisan migrate
php artisan db:seed --class=CowAppLogoSeeder
php artisan storage:link
php artisan view:clear
```

La migración agrega solamente `media.url`. El seeder instala el logo. `storage:link` crea el enlace de `public/storage` a `storage/app/public`; no inserta filas en MySQL. Si informa que el enlace ya existe, verificar la URL pública del logo y no recrearlo a la fuerza. No se requiere ejecutar `npm run build` por estos cambios de Blade.

Si `storage:link` falla por permisos en Windows, guardar el mensaje exacto y detener ese paso. No usar `--force` ni desactivar controles. Todos los comandos de Artisan, escritura en base de datos y tests de esta corrección quedan a cargo del responsable; no se han ejecutado automáticamente.

Referencia oficial: [disco público y storage:link](https://laravel.com/docs/12.x/filesystem#the-public-disk), [ejecución de seeders](https://laravel.com/docs/12.x/seeding#running-seeders), [migraciones](https://laravel.com/docs/12.x/migrations#running-migrations).

## Tests preparados, pendientes de ejecución

```bash
php artisan test --filter=RegistrationLogoTest
php artisan test --filter=DashboardAnalyticsTest
```

El primer archivo cubre el formulario dentro del cuerpo HTML, carga del logo sin duplicados, conservación de la imagen original, URL compartida en las cuatro pantallas, estado inactivo y registro con contraseña hasheada. El segundo comprueba que el panel conserve los controles de la fase anterior. Usan SQLite en memoria y almacenamiento simulado; no sustituyen la evidencia visual de `storage:link`.

## Matriz de evidencias manuales

| ID | Procedimiento | Esperado | Evidencia | Obtenido / HTTP / Estado |
| --- | --- | --- | --- | --- |
| R01 | Abrir `/register` en sesión privada y recargar con Ctrl+F5 | Formulario completo, título correcto y logo | Captura del antes recibido + `R01-registro-corregido.png` | Pendiente; GET esperado 200 |
| R02 | Abrir `/storage/branding/cowapp-logo.png` | Imagen original visible | `R02-logo-url.png` y Network | Pendiente; GET esperado 200 |
| R03 | Consultar `media` en phpMyAdmin | Registro Logo CowApp con ruta, URL, MIME PNG y tamaño | `R03-logo-base-datos.png`; ocultar otros datos sensibles | Pendiente |
| R04 | Visitar inicio, login y panel autenticado | Mismo logo, proporcionado y sin estiramiento | `R04-inicio.png`, `R04-login.png`, `R04-panel.png` | Pendiente; GET esperado 200 |
| R05 | Registrar una cuenta normal con datos válidos | Redirección al login y mensaje de éxito | `R05-registro-valido.png` y Network, sin contraseña | Pendiente; POST esperado 302 |
| R06 | Intentar correo duplicado y confirmación de contraseña diferente | Errores backend y ningún usuario duplicado | `R06-validacion.png` | Pendiente; formulario web esperado 302 y errores al volver |
| R07 | Probar registro a 390 px y botón Mostrar/Ocultar | Formulario usable; botón cambia el campo y su estado accesible | `R07-registro-movil.png`; usar una contraseña ficticia | Pendiente |

No usar el correo reservado de administrador para R05. No publicar contraseñas, cookies, tokens ni el contenido de `.env` en las capturas.

## Checklist

- [x] Etiqueta del título corregida.
- [x] CSRF, validación backend y Hash existentes conservados.
- [x] Logo original incorporado y URL persistente preparada.
- [x] Ruta física y URL diferenciadas sin romper la biblioteca existente.
- [x] Seeder con validación de archivo y operación repetible.
- [x] Tests y matriz preparados.
- [ ] Migración, seeder, enlace y limpieza de vistas ejecutados por el responsable.
- [ ] Tests ejecutados y resultados reales registrados.
- [ ] Evidencias R01-R07 guardadas.
- [ ] Confirmación del responsable para continuar.

Este registro complementa `validacion-fase-panel.md` y el informe obligatorio de auditoría. No se declara la corrección validada hasta recibir los resultados de ejecución.
