# Fase 2: Clientes y contactos

## Alcance y resumen

Se revisó el CRUD existente y se reutilizan modelo Client, relaciones, migración, Form Requests, ClientController, ClientService, ClientPolicy y permiso `clients.manage`. No se reconstruye el módulo. Es información administrativa privada: no corresponde frontend público ni carga de archivos en esta fase.

Se impide eliminar un cliente con oportunidades asociadas: el Service comprueba la relación dentro de una transacción y el controlador devuelve un mensaje; la vista deshabilita la acción y ofrece inactivarlo. Se conserva la restricción de base de datos. La paginación usa la plantilla Bootstrap existente.

## Archivos

Modificados: `app/Services/ClientService.php`, `app/Http/Controllers/Admin/ClientController.php`, `resources/views/admin/clients/index.blade.php`, `tests/Feature/ClientManagementTest.php`. Creado: este documento. No hay migraciones, modelos, permisos ni rutas nuevos.

Rutas existentes, autenticadas y con `clients.manage`: GET/POST `/admin/clientes`, GET `/admin/clientes/create`, GET `/admin/clientes/{client}/edit`, PUT/PATCH y DELETE `/admin/clientes/{client}`. La Policy conserva la comprobación de propiedad.

## Comandos para el responsable

```bash
php artisan test --filter=ClientManagementTest
php artisan test --filter=DashboardAnalyticsTest
```

No ejecutados automáticamente en esta fase. Tests preparados para CRUD, propiedad y permisos, validación, duplicados, eliminación con relaciones, escape HTML y rechazo de propietario introducido por el usuario. Guardar salida real. No se necesita migrar por estos cambios.

## Matriz manual

Usar datos ficticios y dos cuentas independientes. Registrar resultado, HTTP observado y captura por ID.

| ID | Procedimiento | Esperado | HTTP esperado | Estado |
| --- | --- | --- | --- | --- |
| C01 | Abrir Clientes con permiso | Lista propia y formulario de creación | 200 | Pendiente |
| C02 | Crear cliente válido | Registro y mensaje de éxito | POST 302 | Pendiente |
| C03 | Enviar nombre vacío, correo inválido o documento duplicado en la cuenta | Errores y ausencia de registros inválidos | 302 con errores | Pendiente |
| C04 | Editar y marcar inactivo | Cambios persistidos y mensaje | PUT/PATCH 302 | Pendiente |
| C05 | Buscar, filtrar y paginar cuando haya más de 12 registros | Resultados propios; filtros conservados; paginación Bootstrap | 200 | Pendiente |
| C06 | Eliminar cliente sin oportunidades | Registro eliminado y éxito | DELETE 302 | Pendiente |
| C07 | Abrir lista con cliente asociado a una oportunidad existente | Acción deshabilitada y explicación; inactivación disponible | 200 | Pendiente |
| C08 | Cuenta sin permiso solicita la ruta; otra cuenta intenta editar un cliente ajeno | Servidor rechaza ambas acciones | 403 | Pendiente |
| C09 | Sin sesión abrir la ruta | Redirección al login | 302 | Pendiente |
| C10 | Crear nombre con texto `<script>alert(1)</script>` y visualizarlo | Texto escapado; no ejecución | 302 y 200 | Pendiente |
| C11 | Probar a 390 px y escritorio | Formulario usable; tabla desplazable | 200 | Pendiente |

La petición DELETE directa a cliente asociado y el intento de inyectar `user_id` están incluidos en los tests: deben conservar cliente/oportunidad y asignar siempre la cuenta autenticada, respectivamente. Guardar esas salidas como evidencias. No implementar Oportunidades para preparar C07: utilizar datos existentes o registrar la precondición pendiente.

Para evidencia CSRF del módulo, retirar `_token` de un formulario en DevTools y enviar datos ficticios: esperar 419 y ninguna modificación; recargar para restaurar el formulario. Los tests HTTP de Laravel no sustituyen esta prueba real.

## Checklist funcional y seguridad

- [x] CRUD existente, validaciones y mensajes conservados.
- [x] Reutilización de Form Requests, Policy y permiso del servidor.
- [x] Consultas Eloquent y datos validados; propietario asignado desde sesión.
- [x] Restricción de relaciones y mensaje de eliminación preparados.
- [x] Datos escapados por Blade y CSRF conservado.
- [x] Tests y matriz de evidencias preparados.
- [ ] Tests ejecutados y resultados registrados.
- [ ] Matriz manual y prueba CSRF completadas.
- [ ] Evidencias revisadas e incorporadas al informe.
- [ ] Confirmación del responsable para iniciar Prospectos.

Detenerse aquí hasta recibir confirmación. No se declara esta fase validada por haber preparado código o tests.
