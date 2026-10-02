# Inventario de archivos — fase unificada

Incluye la elaboración actual y las correcciones previas pendientes de commit conservadas. No incluye secretos ni archivos ignorados.

## Archivos creados

- `app/Console/Commands/ConfigureGmail.php`
- `app/Console/Commands/CreateCowAppAdmin.php`
- `app/Http/Controllers/Admin/ActivityController.php`
- `app/Http/Controllers/Admin/AuctionController.php`
- `app/Http/Controllers/Admin/ClientProfileController.php`
- `app/Http/Controllers/Admin/InsightsController.php`
- `app/Http/Controllers/Admin/PortalBlockController.php`
- `app/Http/Controllers/Admin/ProductController.php`
- `app/Http/Controllers/Admin/QuoteController.php`
- `app/Http/Controllers/Admin/SaleController.php`
- `app/Http/Controllers/Admin/SecurityController.php`
- `app/Http/Controllers/Admin/WhatsappController.php`
- `app/Http/Controllers/Api/MeasurementController.php`
- `app/Http/Controllers/Auth/MfaController.php`
- `app/Http/Controllers/CatalogController.php`
- `app/Http/Controllers/ContractController.php`
- `app/Http/Controllers/WhatsappWebhookController.php`
- `app/Http/Middleware/AuditWorkspace.php`
- `app/Http/Requests/CaptureLeadRequest.php`
- `app/Http/Requests/RecordMeasurementRequest.php`
- `app/Http/Requests/SaveActivityRequest.php`
- `app/Http/Requests/SaveAuctionRequest.php`
- `app/Http/Requests/SavePortalBlockRequest.php`
- `app/Http/Requests/SaveProductRequest.php`
- `app/Http/Requests/SaveQuoteRequest.php`
- `app/Http/Requests/StoreClientDocumentRequest.php`
- `app/Http/Requests/UpdateSaleRequest.php`
- `app/Mail/LoginOtp.php`
- `app/Models/Activity.php`
- `app/Models/Auction.php`
- `app/Models/AuctionBid.php`
- `app/Models/ClientDocument.php`
- `app/Models/LivestockMeasurement.php`
- `app/Models/PortalBlock.php`
- `app/Models/Product.php`
- `app/Models/Quote.php`
- `app/Models/Sale.php`
- `app/Models/SecurityEvent.php`
- `app/Models/WhatsappMessage.php`
- `app/Policies/ActivityPolicy.php`
- `app/Policies/AuctionPolicy.php`
- `app/Policies/ClientDocumentPolicy.php`
- `app/Policies/PortalBlockPolicy.php`
- `app/Policies/ProductPolicy.php`
- `app/Policies/QuotePolicy.php`
- `app/Policies/SalePolicy.php`
- `app/Services/ActivityService.php`
- `app/Services/AuctionService.php`
- `app/Services/ClientDocumentService.php`
- `app/Services/LoginMfaService.php`
- `app/Services/LoginProtectionService.php`
- `app/Services/PortalBlockService.php`
- `app/Services/ProductService.php`
- `app/Services/QuoteService.php`
- `app/Services/SaleService.php`
- `app/Services/SecurityAuditService.php`
- `app/Services/WhatsappService.php`
- `config/whatsapp.php`
- `database/migrations/2026_10_01_000011_add_otp_epoch_expiration.php`
- `database/migrations/2026_10_01_000012_create_commercial_workspace.php`
- `database/migrations/2026_10_01_000013_create_whatsapp_messages.php`
- `database/migrations/2026_10_01_000014_add_guide_date_and_measurements.php`
- `docs/correccion-tests-gd-correo-terminal.md`
- `docs/fase-unificada-cowapp.md`
- `resources/views/admin/auctions/index.blade.php`
- `resources/views/admin/clients/profile.blade.php`
- `resources/views/admin/insights/index.blade.php`
- `resources/views/admin/quotes/index.blade.php`
- `resources/views/admin/sales/index.blade.php`
- `resources/views/admin/security/account.blade.php`
- `resources/views/admin/security/index.blade.php`
- `resources/views/admin/whatsapp/index.blade.php`
- `resources/views/admin/workspace/crud.blade.php`
- `resources/views/auth/mfa.blade.php`
- `resources/views/catalog/index.blade.php`
- `resources/views/contracts/show.blade.php`
- `resources/views/emails/auth/login-otp.blade.php`
- `resources/views/layouts/workspace.blade.php`
- `resources/views/partials/otp-fields.blade.php`
- `resources/views/partials/portal-blocks.blade.php`
- `resources/views/partials/workspace-links.blade.php`
- `tests/Feature/ConfigureGmailTest.php`
- `tests/Feature/SecurityTimingTest.php`
- `tests/Feature/UnifiedWorkspaceTest.php`

## Archivos modificados

- `.env.example`
- `README.md`
- `app/Http/Controllers/Admin/ClientController.php`
- `app/Http/Controllers/Admin/LeadController.php`
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Auth/PasswordRecoveryController.php`
- `app/Http/Controllers/PublicPortalController.php`
- `app/Http/Requests/StoreClientRequest.php`
- `app/Http/Requests/StoreLeadRequest.php`
- `app/Http/Requests/StoreLivestockBatchRequest.php`
- `app/Http/Requests/UpdateClientRequest.php`
- `app/Http/Requests/UpdateLeadRequest.php`
- `app/Http/Requests/UpdateLivestockBatchRequest.php`
- `app/Http/Requests/VerifyPasswordOtpRequest.php`
- `app/Models/Client.php`
- `app/Models/Lead.php`
- `app/Models/LivestockBatch.php`
- `app/Models/PasswordResetOtp.php`
- `app/Models/SalesOpportunity.php`
- `app/Models/User.php`
- `app/Providers/AppServiceProvider.php`
- `app/Services/ClientService.php`
- `app/Services/DashboardAnalyticsService.php`
- `app/Services/LeadService.php`
- `app/Services/LivestockBatchService.php`
- `app/Services/PasswordRecoveryService.php`
- `bootstrap/app.php`
- `docs/fases-desarrollo-crm.md`
- `docs/validacion-lotes-imagen-correo.md`
- `resources/views/admin/clients/index.blade.php`
- `resources/views/admin/leads/index.blade.php`
- `resources/views/admin/livestock-batches/index.blade.php`
- `resources/views/auth/password/verify-otp.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/welcome.blade.php`
- `routes/api.php`
- `routes/web.php`
- `tests/Feature/LivestockBatchImageTest.php`

## Comandos utilizados durante la elaboración

Lectura de código y documentación con rg, Get-Content, git status, git diff y lectura del ZIP. Edición de archivos con apply_patch. No se ejecutaron migraciones, envío real de correo ni tests. Los comandos que debe ejecutar el responsable están en fase-unificada-cowapp.md.

## Verificación realizada

Revisión estática de rutas, autorización, servicios, plantillas y migraciones; git diff --check. Los resultados de ejecución están pendientes.
