# Cierre de sesión al visitar la página pública

Visitar Inicio (`/`), Catálogo (`/catalogo`) o Contacto (`/contacto`) cierra la sesión web, invalida su identificador y regenera CSRF. Se conserva el acceso mientras se navega entre dashboard y sus módulos. La salida registra un evento sin secretos.

Las páginas públicas y privadas indicadas no se almacenan en caché. El componente de historial ahora también verifica las páginas públicas restauradas: atrás/adelante recarga desde el servidor. Al intentar regresar al dashboard con una sesión cerrada, se redirige al login.

Esto se aplica a visitar páginas de CowApp. Cerrar una pestaña o navegar a otro dominio no garantiza una petición al servidor; no se utiliza un evento unload como control de seguridad.

## Validación manual y evidencias

1. Iniciar sesión, abrir Clientes y Lotes y volver al dashboard: conservar sesión.
2. Ir al Inicio público: mostrar página pública con la sesión cerrada.
3. Usar atrás para regresar al dashboard: mostrar login; después usar adelante y volver a intentar abrir dashboard: debe seguir exigiendo login.
4. Repetir la salida hacia Catálogo y Contacto, y con «recordarme» seleccionado: no recuperar automáticamente la sesión anterior.
5. Probar logout explícito y atrás/adelante: no recuperar acceso.

Guardar capturas de los resultados. El historial real se valida en el navegador; los tests HTTP verifican el cierre en servidor, acceso protegido, cabeceras y conservación entre módulos.

Comando para el responsable:

```bash
php artisan route:clear
php artisan view:clear
php artisan test --filter=SessionNavigationTest
```

No hay migraciones ni compilación frontend para esta corrección. Tests preparados, no ejecutados por el agente. Archivos: nuevo middleware CloseSessionOnPublicPage, rutas web, partial session-history y SessionNavigationTest.
