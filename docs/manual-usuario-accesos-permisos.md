# Manual de usuario: acceso, roles y permisos de CowApp

Fecha: 1 de octubre de 2026. Basado en el código actual de CowApp.

## 1. Quién puede administrar el acceso

| Persona o registro | Qué puede hacer actualmente |
| --- | --- |
| Administrador raíz | Crear y editar roles, asignarlos a usuarios y acceder a los módulos administrativos. |
| Usuario con rol | Usar los módulos incluidos en su rol, con los límites de propiedad de datos. |
| Usuario sin rol | Acceder a su panel y funcionalidades básicas disponibles; no recibe permisos administrativos automáticamente. |
| Cliente comercial registrado en Clientes | Es una ficha comercial. No es una cuenta de acceso ni puede asignar permisos. |

La cuenta raíz se identifica por el correo configurado en `COWAPP_ADMIN_EMAIL` del archivo `.env`. El correo utilizado para enviar mensajes, `MAIL_USERNAME`, es independiente.

**Límite importante:** actualmente un cliente o dueño de empresa no puede administrar por sí mismo los permisos de sus empleados. Tampoco existe un equipo de empresa que comparta automáticamente clientes, lotes y ventas entre cuentas. El procedimiento de este manual permite que el administrador raíz asigne permisos a usuarios. No convierte esos usuarios en empleados vinculados a una empresa.

## 2. Entrar como administrador raíz

1. Abre la página de inicio de sesión de CowApp.
2. Escribe el correo que está configurado en `COWAPP_ADMIN_EMAIL`.
3. Introduce la contraseña de esa cuenta CowApp. No uses la contraseña de aplicación de Gmail.
4. Si la cuenta tiene MFA activado, introduce el código recibido por correo.
5. En el panel, abre **Roles y permisos**.

En desarrollo, si utilizas el servidor en el puerto 8000, la dirección es:

```text
http://127.0.0.1:8000/admin/roles
```

Usa el dominio y puerto reales de tu instalación. La pantalla solo admite acceso raíz; abrir la dirección directamente no evita esta restricción.

### Si todavía no existe la cuenta raíz

El responsable técnico debe configurar el correo administrativo correcto en `.env`, en la raíz del proyecto. Si modificó ese valor, ejecutará:

```bash
php artisan config:clear
```

Después, desde la carpeta del proyecto:

```bash
php artisan cowapp:admin
```

El comando pide nombre, contraseña y confirmación de forma oculta. La contraseña debe tener al menos 12 caracteres. Si la cuenta ya existe, no cambia su contraseña; utiliza recuperación de contraseña si no la recuerdas.

Cambiar `COWAPP_ADMIN_EMAIL` transfiere el acceso raíz a otra cuenta. No lo cambies para dar permisos a cada empleado: usa roles.

## 3. Registrar la cuenta de un empleado

1. El empleado abre **Crear cuenta** o `/register`.
2. Completa nombre, correo personal de trabajo, contraseña y confirmación.
3. Crea la cuenta y conserva sus propias credenciales.
4. Comunica al administrador únicamente su correo de acceso para que le asigne un rol.

Registrarse no concede permisos administrativos. No compartas la cuenta raíz entre empleados. El correo reservado para la cuenta raíz no se registra mediante el formulario público.

## 4. Crear un rol

Un rol reúne los permisos de un puesto de trabajo, por ejemplo «Asesor comercial».

1. Inicia sesión como raíz y abre **Roles y permisos**.
2. En **Crear rol**, escribe un nombre único.
3. Añade una descripción opcional.
4. Marca las casillas de los módulos necesarios.
5. Pulsa **Crear rol**.

Ejemplos de selección:

| Rol sugerido | Permisos que puedes seleccionar |
| --- | --- |
| Asesor comercial | Clientes, leads/prospectos y Pipeline comercial. |
| Gestor de ventas | Clientes, cotizaciones/contratos y ventas/despachos. |
| Operador de remates | Remates y clientes; añadir cotizaciones/ventas solo si también realizará el cierre comercial. |
| Editor del portal | Administrar contenido y productos/servicios si también mantiene el catálogo. |
| Revisor de seguridad | Auditoría de seguridad. |

Selecciona los permisos según las tareas reales. El catálogo de permisos es controlado por la aplicación; el formulario no crea códigos de permiso nuevos.

## 5. Asignar el rol al empleado

1. En la misma pantalla, baja hasta **Asignar roles a cuentas**.
2. Busca el nombre y, especialmente, el correo del empleado. Usa la paginación si no aparece en la primera página.
3. En **Rol actual**, selecciona el rol creado.
4. Pulsa **Asignar** en esa fila.
5. Comprueba el mensaje de éxito.
6. Pide al empleado que actualice su panel o vuelva a iniciar sesión.

Cada usuario tiene un rol asignado a la vez. Los módulos autorizados aparecerán en su menú; los demás permanecen ocultos. El servidor también verifica los permisos al abrir una URL o enviar un formulario.

La cuenta raíz muestra **Administrador raíz** y no admite cambio de rol desde esta pantalla.

## 6. Modificar o retirar permisos

### Cambiar los permisos de un rol

1. Busca el rol en **Roles registrados**.
2. Marca o desmarca los permisos.
3. Pulsa **Guardar cambios**.

El cambio afecta a todas las cuentas que usan ese rol. Si una sola persona necesita funciones distintas, crea otro rol y asígnaselo.

### Retirar los permisos administrativos de una cuenta

1. Busca al usuario en **Asignar roles a cuentas**.
2. Selecciona **Sin rol**.
3. Pulsa **Asignar**.

Esto retira los permisos del rol; no elimina la cuenta ni garantiza bloquear todas sus funcionalidades básicas. No equivale a suspender totalmente la cuenta o cerrar sus sesiones activas.

### Eliminar un rol

Primero cambia o retira ese rol de todas sus cuentas. Después pulsa **Eliminar** y confirma. Un rol asignado a usuarios no se puede eliminar.

## 7. Qué permite cada módulo

| Permiso | Función principal |
| --- | --- |
| `clients.manage` | Gestionar clientes y su ficha comercial/documental. |
| `leads.manage` | Gestionar prospectos y bandeja WhatsApp, si está configurada. |
| `sales-pipeline.manage` | Gestionar oportunidades y etapas comerciales. |
| `products.manage` | Gestionar productos y servicios. |
| `quotes.manage` | Gestionar cotizaciones y aceptación de contratos. |
| `sales.manage` | Registrar ventas, pagos y despacho. |
| `activities.manage` | Gestionar actividades y agenda. |
| `auctions.manage` | Gestionar remates y pujas administrativas. |
| `content.manage` | Gestionar contenido del portal, noticias y multimedia. |
| `audit.view` | Consultar eventos de seguridad dentro del alcance autorizado. |
| `mail-settings.manage` | Permiso relacionado con SMTP; la ruta actual exige además cuenta raíz. Asignarlo a un empleado no habilita esa pantalla. |

Un permiso habilita operaciones del módulo, pero **no comparte los datos de otro usuario**. Los módulos comerciales filtran por propietario. Actualmente tampoco hay permisos separados de «solo lectura» y «editar» para cada módulo.

Los lotes y categorías tienen controles de propiedad propios; no existe en este catálogo un permiso específico que permita al administrador retirarlos mediante un rol.

## 8. Acceso de clientes que administren a sus empleados

**Esta función está pendiente de implementación.** Una ficha del módulo Clientes no puede asignar roles. Dar permisos comerciales a una cuenta no le permite administrar usuarios ni roles.

El comportamiento necesario para habilitarla sería:

1. Asociar la cuenta del cliente administrador y sus empleados a una misma empresa.
2. Permitir que ese administrador invite o vincule empleados exclusivamente de su empresa.
3. Permitirle asignar únicamente permisos que esté autorizado a delegar.
4. Compartir los datos operativos de su empresa entre empleados autorizados, manteniendo aislamiento respecto a otras empresas.
5. Registrar altas, cambios y revocaciones de acceso en auditoría.

Hasta contar con ese funcionamiento, las asignaciones deben solicitarlas al administrador raíz. No se debe entregar acceso raíz a cada cliente ni utilizar una cuenta compartida para suplir la función pendiente.

## 9. Sesión y recuperación de contraseña

- La sesión se conserva al navegar entre el dashboard y sus módulos.
- Visitar Inicio, Catálogo o Contacto público cierra la sesión web.
- Después de salir, intentar regresar a un módulo con atrás/adelante exige iniciar sesión nuevamente.
- Cinco contraseñas incorrectas para el mismo correo generan un bloqueo de 15 minutos.
- El OTP de recuperación dura 10 minutos desde el envío exitoso. Usa el último código solicitado; pedir otro reemplaza el anterior.
- Si se activa MFA, el inicio de sesión exige un segundo código recibido por correo.

## 10. Problemas frecuentes

| Problema | Qué revisar |
| --- | --- |
| No aparece Roles y permisos | Estás usando una cuenta distinta de la raíz configurada. |
| No aparecen los módulos comerciales | La cuenta no tiene rol o el rol no incluye esos permisos. |
| El permiso nuevo no aparece entre las casillas | El responsable técnico debe aplicar las migraciones pendientes con `php artisan migrate`. |
| La página devuelve 403 | La cuenta no está autorizada o el registro pertenece a otra cuenta. |
| El empleado no ve los clientes o lotes del dueño | Actualmente los datos son por propietario; los roles no crean equipos ni comparten datos. |
| Cambié `.env` y sigue reconociendo otra cuenta raíz | Ejecuta `php artisan config:clear` y revisa el correo de la cuenta con la que inicias sesión. |
| El menú conserva una vista anterior | Actualiza la página; el responsable puede ejecutar `php artisan view:clear`. |
| No puedo eliminar un rol | Hay usuarios asignados; cambia primero sus roles. |

## 11. Evidencias para validar la administración

1. Captura de un rol creado con sus permisos seleccionados.
2. Captura de la asignación a una cuenta de prueba, sin exponer contraseñas.
3. Captura del menú del empleado mostrando los módulos autorizados.
4. Prueba de un módulo no autorizado: debe responder 403 aunque se escriba su URL.
5. Retirar un permiso y verificar que desaparece el módulo y se rechaza el acceso directo.
6. Verificar que un usuario no puede acceder a registros de otra cuenta.

Este manual documenta el comportamiento actual revisado en código. No certifica pruebas de ejecución ni presenta la delegación empresarial pendiente como disponible.
