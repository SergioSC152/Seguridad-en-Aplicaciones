# Corrección: nombre personal y validación de categorías de lotes

El nombre de cuenta admite letras Unicode (tildes y ñ), espacios, apóstrofos y guiones, con máximo de 100 caracteres. Rechaza números y otros símbolos en registro web, API y creación de administrador. El formulario incluye pattern y un mensaje descriptivo; el backend valida aunque se evite el navegador. No se aplica esta regla al nombre de productos, empresas, razas ni códigos de lote.

El error `Validator::validateProhibitedWith does not exist` procedía de la regla inexistente `prohibited_with`. En StoreLivestockBatchRequest y UpdateLivestockBatchRequest se reemplaza por `Rule::prohibitedIf`, activada cuando se proporciona categoría existente. Sigue siendo obligatorio seleccionar una categoría propia o escribir una nueva; proporcionar ambas produce validación y no error 500.

## Validar y guardar evidencia

1. Registrar «Juan123»: rechazar el nombre con mensaje en español.
2. Registrar «María José Muñoz»: aceptar con datos restantes válidos.
3. Crear un lote con categoría existente: guardar sin error 500.
4. Crear un lote con raza nueva: crear categoría propia y lote.
5. Enviar ambas categorías desde Postman: rechazar con error de validación.
6. Editar un lote y cambiarlo a raza nueva: guardar correctamente.

```bash
php artisan view:clear
php artisan test --filter=NamesAndBatchValidationTest
```

No requiere migración ni actualización de dependencias. Se prepararon tests de regresión; no se ejecutaron por el agente. Cambios en Requests de lote/registro, API AuthController, CreateCowAppAdmin y vista de registro.
