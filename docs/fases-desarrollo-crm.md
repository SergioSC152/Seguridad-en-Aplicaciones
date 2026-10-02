# Fases de desarrollo y entrega de CowApp CRM

Fecha: 1 de octubre de 2026. Alcance: CRM web Laravel; el aplicativo móvil es un proyecto separado. Las guías CMS aportan el procedimiento, no cambian el objetivo del CRM.

## Reglas de trabajo

- Un módulo a la vez. Después de cada fase se entregan pruebas y se espera confirmación del responsable.
- Revisar primero estructura, versión, archivos y componentes reutilizables. Mantener Laravel 12 y la interfaz Bootstrap existente mientras no se acuerde una migración visual.
- Conservar las funciones que trabajan correctamente. No crear capas, paquetes ni funcionalidades futuras sin necesidad.
- El responsable ejecuta comandos de Artisan, compilación, migraciones y tests; guardar sus resultados reales.
- La confirmación general de funcionamiento no sustituye las capturas y salidas exigidas por el informe.

## Orden propuesto

| Fase | Alcance y criterio de cierre | Estado |
| --- | --- | --- |
| 0. Revisión inicial | Documentación, estructura, versión, rutas, configuración sin secretos y hallazgos iniciales | Revisión realizada; evidencias parciales existentes |
| 1. Acceso y panel | Registro, logo con URL en BD, login, logout y panel con datos propios | Responsable confirmó el reto; correcciones de contraseña e historial pendientes de validar |
| 2. Clientes y contactos | Revisar y cerrar el CRUD existente, permisos, propiedad, validación, búsqueda, paginación y eliminación segura | Fase actual; no avanzar sin confirmación |
| 3. Prospectos | Revisar el módulo existente y sus relaciones, estados y acceso por cuenta | Pendiente |
| 4. Oportunidades | Revisar el módulo existente, cliente asociado, etapas y cálculos comerciales | Pendiente |
| 5. Productos y servicios | Definir solamente lo necesario para el flujo comercial y reutilizar inventario/categorías cuando corresponda | Pendiente de definición |
| 6. Cotizaciones | Alcance mínimo acordado, detalle y totales, permisos y estados | Pendiente |
| 7. Ventas | Registro del cierre comercial y relaciones acordadas; sin contabilidad adicional implícita | Pendiente |
| 8. Actividades y agenda | Seguimiento comercial mínimo ligado a clientes o oportunidades | Pendiente |
| 9. Contenido público e imágenes | Revisar contenido y multimedia existentes; carga, reemplazo, eliminación y URL; contacto/correos cuando corresponda | Pendiente |
| 10. Seguridad y API | Revisar roles existentes, autenticación API y pruebas Postman; corregir hallazgos y actualizar dependencias de forma específica | Pendiente; obligatoria antes de publicar |
| 11. Auditoría y entrega | Completar matrices, evidencias, informe PDF, README/GitHub y video | Pendiente |

Fincas, proveedores, notificaciones, reportes adicionales y configuración ampliada quedan como ampliaciones sujetas a necesidad y aprobación. El diagrama general no obliga a construirlos para completar la auditoría. Si la guía o rúbrica exige alguno expresamente, se incorporará como una fase independiente antes de la entrega.

## Procedimiento dentro de cada módulo

1. Análisis y alcance: archivos actuales, componentes reutilizables y explicación de cambios.
2. Datos y backend: migración, modelo, relaciones, Form Requests, controlador y Service solamente cuando corresponda.
3. Acceso: Policy, middleware y permisos del servidor; reutilizar los existentes.
4. Interfaz: CRUD, mensajes, validación visible, responsive y frontend público solamente si aplica.
5. Seguridad: CSRF, consultas seguras, datos validados, propiedad de registros y archivos validados por contenido. Hash para contraseñas/OTP cuando corresponda; secretos fuera del código y de evidencias públicas.
6. Validación: tests relevantes y matriz manual con esperado, obtenido, HTTP, evidencia y estado.
7. Cierre: resumen, comandos, archivos creados/modificados, migraciones/rutas, pruebas y checklists. Detenerse hasta recibir confirmación.

## Entregables obligatorios de la actividad de auditoría

Fuente de requisitos: `Actividad_Auditoria_Pruebas_Seguridad_Laravel_Postman.pdf`. Las otras guías orientan la construcción y ejecución.

- Informe técnico PDF: portada, introducción, objetivos, descripción, metodología, herramientas, dependencias, rutas/middleware, configuración, autenticación/autorización, validaciones/CSRF, imágenes, API/Postman, matrices, resultados, recomendaciones, conclusiones y enlaces.
- Salidas y capturas de Composer/NPM audit y outdated, rutas, autenticación, autorización, validaciones, imágenes y API/Postman.
- Matriz de pruebas y matriz de hallazgos. Distinguir revisión estática de fallos demostrados por ejecución.
- Repositorio GitHub sin secretos y README con instalación y pruebas.
- Video de YouTube de 5 a 8 minutos con aplicación, auditoría, rutas, autenticación, prueba negativa, autorización, Postman y recomendación.

Cada evidencia debe usar un ID, precondiciones, resultado esperado, resultado real, HTTP observado y archivo de captura/salida. Lo no ejecutado permanece pendiente. No capturar `.env`, claves, contraseñas, cookies ni tokens.

## Hallazgos que no se deben perder

El registro API necesita revisar la reserva del correo administrador, igual que el registro web. Las auditorías iniciales reportaron avisos de dependencias: conservar sus salidas y volver a comprobarlos en la fase de seguridad. Estos puntos deben resolverse o justificarse con evidencia antes de publicación; no se consideran corregidos por cerrar Clientes.

## Validaciones disponibles

- `validacion-fase-panel.md`: panel y auditoría inicial.
- `validacion-registro-logo.md`: registro e imagen con URL en base de datos.
- `validacion-login-historial.md`: correcciones de contraseña y navegación tras logout.
- `validacion-fase-clientes.md`: fase actual.
