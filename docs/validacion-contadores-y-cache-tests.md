# Contadores de texto y aislamiento de configuración de tests

Los campos con maxlength muestran una barra de progreso y contador «actual / máximo caracteres». Al alcanzar el máximo, aparece «Límite alcanzado» en español. Los textarea tienen desplazamiento vertical y pueden ampliarse. Los inputs de una línea mantienen su desplazamiento horizontal nativo; no se les agrega una barra vertical artificial. La contraseña permanece oculta: el contador muestra solamente su longitud.

La salida compartida por el responsable registra 42 fallos y 37 pruebas aprobadas. Varios fallos presentan HTTP 419 antes de llegar al controlador. Se detectaron bootstrap/cache/config.php y rutas en caché en el proyecto. phpunit.xml ahora utiliza rutas de caché exclusivas para pruebas; no se desactiva CSRF de la aplicación. Las nuevas ubicaciones no deben contener configuración local o de producción.

La ejecución posterior determinará si quedan otros fallos independientes; no se declara corregida toda la suite con esta revisión.

## Comandos y evidencia

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan test --filter='NamesAndBatchValidationTest|TextLimitsTest|CredentialMessagesTest'
```

Guardar salida real. No repetir la suite completa hasta revisar el resultado de este grupo.

Validación visual: escribir 100 caracteres en nombre y 25 en contraseña para ver el aviso; borrar un carácter y comprobar que desaparece. Pegar texto largo en notas, verificar límite y desplazamiento del textarea. Confirmar contador también en formularios de edición y catálogo público. No capturar contraseñas visibles.

Archivos: partial text-limits nuevo, include en cowapp-stitch-theme, phpunit.xml y AppServiceProvider (corrección previa para comandos de mantenimiento sin consulta SMTP inicial). No hay migraciones ni compilación frontend. Revisión estática realizada; pruebas no ejecutadas por el agente.
