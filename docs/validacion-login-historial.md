# Correcciones de login e historial

## Implementación

El botón de contraseña tenía dos manejadores (Vite e inline) que cambiaban el campo dos veces. Se conserva uno en `resources/js/auth/login.js`, con icono y atributos accesibles actualizados.

Se añade `app/Http/Middleware/PreventBrowserCache.php`, alias en `bootstrap/app.php` y aplicación a los grupos de acceso y rutas autenticadas en `routes/web.php`. Las respuestas usan `no-store`. El parcial nuevo `resources/views/partials/session-history.blade.php`, incluido por el tema compartido, oculta el documento al salir y solicita una recarga al restaurarse por el historial. La autorización definitiva sigue en el servidor mediante `auth`.

Referencias: [middleware Laravel](https://laravel.com/docs/12.x/middleware) y [evento pageshow y restauración del historial](https://developer.mozilla.org/en-US/docs/Web/API/Window/pageshow_event).

Modificados también: `resources/views/auth/login.blade.php` y `resources/views/partials/cowapp-stitch-theme.blade.php`. Creado: `tests/Feature/SessionNavigationTest.php`. Sin migraciones ni rutas nuevas.

## Comandos para Git Bash

Desde la carpeta del proyecto, ejecutar uno por uno:

```bash
npm run build
php artisan view:clear
php artisan route:clear
php artisan test --filter=SessionNavigationTest
```

No ejecutados automáticamente. Los tests verifican cabeceras y rechazo de acceso después de logout; el historial real y el botón requieren navegador.

## Evidencias manuales

| ID | Procedimiento | Esperado | Evidencia / resultado |
| --- | --- | --- | --- |
| L01 | Recargar login tras compilar; escribir contraseña ficticia y pulsar ojo varias veces | Cada clic muestra/oculta; icono y estado accesible coherentes | Captura sin contraseña real; pendiente |
| L02 | Login, visitar panel y Clientes, logout, pulsar Atrás y Adelante repetidamente | Ningún panel privado restaurado permanece visible; al solicitar ruta privada vuelve al login | Video corto o capturas; pendiente |
| L03 | Tras logout escribir `/dashboard` directamente | GET 302 hacia login, luego 200 | Network sin cookies; pendiente |
| L04 | Con sesión activa usar Atrás/Adelante entre páginas internas | Recarga con sesión válida; páginas utilizables | Captura; pendiente |
| L05 | Inspeccionar cabeceras de login y dashboard | Cache-Control contiene no-store; dashboard requiere sesión | Network sin secretos; pendiente |

Guardar salida del test y navegador/versión de L02. Probar sin desactivar la caché de DevTools para que la evidencia del historial sea representativa.

## Checklist

- [x] Un solo manejador del botón y controles de historial preparados.
- [x] Logout POST/CSRF y autorización backend conservados.
- [x] Tests específicos preparados.
- [ ] Compilación y tests ejecutados por el responsable.
- [ ] L01–L05 validados con evidencias.
