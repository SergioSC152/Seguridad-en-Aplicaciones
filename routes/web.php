<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LivestockBatchController;
use App\Http\Controllers\Admin\LivestockCategoryController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalesOpportunityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Middleware\EnsureMailSettingsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', PublicPortalController::class)->middleware([\App\Http\Middleware\CloseSessionOnPublicPage::class,'no-cache'])->name('home');
Route::get('/catalogo',[\App\Http\Controllers\CatalogController::class,'index'])->middleware([\App\Http\Middleware\CloseSessionOnPublicPage::class,'no-cache'])->name('catalog.index');
Route::post('/catalogo/solicitud',[\App\Http\Controllers\CatalogController::class,'capture'])->middleware('throttle:6,1')->name('catalog.capture');
Route::middleware(['no-cache','throttle:10,1'])->prefix('contratos/{token}')->name('contracts.')->group(function () {
    Route::get('/',[\App\Http\Controllers\ContractController::class,'show'])->name('show');
    Route::post('/codigo',[\App\Http\Controllers\ContractController::class,'code'])->middleware('throttle:3,15')->name('code');
    Route::post('/aceptar',[\App\Http\Controllers\ContractController::class,'accept'])->name('accept');
});

// Módulo de Contacto y Envío de Correo SMTP (Pasos 6.5, 6.8)
Route::get('/contacto', [ContactController::class, 'show'])->middleware([\App\Http\Middleware\CloseSessionOnPublicPage::class,'no-cache'])->name('contact.show');
Route::post('/contacto', [ContactController::class, 'send'])->name('contact.send');

// Rutas de Autenticación
Route::middleware(['guest', 'no-cache'])->group(function () {
    Route::get('/login/mfa',[\App\Http\Controllers\Auth\MfaController::class,'show'])->name('mfa.show');
    Route::post('/login/mfa',[\App\Http\Controllers\Auth\MfaController::class,'verify'])->middleware('throttle:10,1')->name('mfa.verify');
    Route::get('/forgot-password', [PasswordRecoveryController::class, 'requestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordRecoveryController::class, 'send'])
        ->middleware('throttle:otp-send')
        ->name('password.email');
    Route::get('/forgot-password/verify', [PasswordRecoveryController::class, 'otpForm'])
        ->name('password.otp.form');
    Route::post('/forgot-password/verify', [PasswordRecoveryController::class, 'verify'])
        ->middleware('throttle:otp-verify')
        ->name('password.otp.verify');
    Route::post('/forgot-password/resend', [PasswordRecoveryController::class, 'resend'])
        ->middleware('throttle:otp-send')
        ->name('password.otp.resend');
    Route::get('/reset-password', [PasswordRecoveryController::class, 'resetForm'])
        ->name('password.reset.form');
    Route::post('/reset-password', [PasswordRecoveryController::class, 'reset'])
        ->name('password.reset');

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login-burst')
        ->name('login.store');

    Route::get('/register', [LoginController::class, 'createRegister'])
        ->name('register');

    Route::post('/register', [LoginController::class, 'register'])
        ->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Panel Administrativo CRM / CMS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'no-cache','audit-workspace'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    // Módulo Biblioteca Multimedia (Reto A / Pasos 5.4, 5.5, 5.7, 5.13)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('indicadores',[\App\Http\Controllers\Admin\InsightsController::class,'index'])->name('insights.index');
        Route::get('whatsapp',[\App\Http\Controllers\Admin\WhatsappController::class,'index'])->name('whatsapp.index');
        Route::post('whatsapp',[\App\Http\Controllers\Admin\WhatsappController::class,'send'])->middleware('throttle:10,1')->name('whatsapp.send');
        Route::resource('productos',\App\Http\Controllers\Admin\ProductController::class)->parameters(['productos'=>'product'])->names('products')->only(['index','store','edit','update','destroy']);
        Route::resource('actividades',\App\Http\Controllers\Admin\ActivityController::class)->parameters(['actividades'=>'activity'])->names('activities')->only(['index','store','edit','update','destroy']);
        Route::resource('portal',\App\Http\Controllers\Admin\PortalBlockController::class)->parameters(['portal'=>'block'])->names('portal-blocks')->only(['index','store','edit','update','destroy']);
        Route::resource('cotizaciones',\App\Http\Controllers\Admin\QuoteController::class)->parameters(['cotizaciones'=>'quote'])->names('quotes')->only(['index','store','edit','update','destroy']);
        Route::post('cotizaciones/{quote}/enviar',[\App\Http\Controllers\Admin\QuoteController::class,'send'])->middleware('throttle:5,1')->name('quotes.send');
        Route::post('cotizaciones/{quote}/venta',[\App\Http\Controllers\Admin\QuoteController::class,'sell'])->name('quotes.sell');
        Route::get('ventas',[\App\Http\Controllers\Admin\SaleController::class,'index'])->name('sales.index');
        Route::put('ventas/{sale}',[\App\Http\Controllers\Admin\SaleController::class,'update'])->name('sales.update');
        Route::get('remates',[\App\Http\Controllers\Admin\AuctionController::class,'index'])->name('auctions.index');
        Route::get('remates/resumen',[\App\Http\Controllers\Admin\AuctionController::class,'snapshot'])->name('auctions.snapshot');
        Route::post('remates',[\App\Http\Controllers\Admin\AuctionController::class,'store'])->name('auctions.store');
        foreach(['open'=>'abrir','bid'=>'puja','close'=>'cerrar'] as $action=>$url) Route::post('remates/{auction}/'.$url,[\App\Http\Controllers\Admin\AuctionController::class,$action])->name('auctions.'.$action);
        Route::get('clientes/{client}/ficha',[\App\Http\Controllers\Admin\ClientProfileController::class,'show'])->name('clients.profile');
        Route::post('clientes/{client}/documentos',[\App\Http\Controllers\Admin\ClientProfileController::class,'store'])->name('client-documents.store');
        Route::get('documentos/{document}/descargar',[\App\Http\Controllers\Admin\ClientProfileController::class,'download'])->name('client-documents.download');
        Route::put('documentos/{document}',[\App\Http\Controllers\Admin\ClientProfileController::class,'review'])->name('client-documents.review');
        Route::delete('documentos/{document}',[\App\Http\Controllers\Admin\ClientProfileController::class,'destroy'])->name('client-documents.destroy');
        Route::post('leads/{lead}/convertir',[LeadController::class,'convert'])->name('leads.convert');
        Route::get('seguridad',[\App\Http\Controllers\Admin\SecurityController::class,'index'])->name('security.index');
        Route::get('seguridad/eventos',[\App\Http\Controllers\Admin\SecurityController::class,'snapshot'])->name('security.snapshot');
        Route::get('mi-seguridad',[\App\Http\Controllers\Admin\SecurityController::class,'account'])->name('security.account');
        Route::put('mi-seguridad',[\App\Http\Controllers\Admin\SecurityController::class,'update'])->middleware('throttle:5,1')->name('security.account.update');
        Route::resource('lotes', LivestockBatchController::class)
            ->parameters(['lotes' => 'batch'])
            ->names('livestock-batches')
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::resource('categorias-ganado', LivestockCategoryController::class)
            ->parameters(['categorias-ganado' => 'category'])
            ->names('livestock-categories')
            ->only(['index', 'store', 'edit', 'update', 'destroy']);

        Route::middleware('permission:clients.manage')->resource('clientes', ClientController::class)
            ->parameters(['clientes' => 'client'])
            ->names('clients')
            ->only(['index', 'store', 'edit', 'update', 'destroy']);

        Route::middleware('permission:sales-pipeline.manage')->resource('pipeline', SalesOpportunityController::class)
            ->parameters(['pipeline' => 'opportunity'])
            ->names('sales-pipeline')
            ->only(['index', 'store', 'edit', 'update', 'destroy']);

        Route::middleware('permission:leads.manage')->resource('leads', LeadController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);

        Route::middleware('permission:content.manage')->group(function () {
            Route::get('/media', [MediaController::class, 'index'])->name('media.index');
            Route::post('/media', [MediaController::class, 'store'])->name('media.store');
            Route::put('/media/{media}', [MediaController::class, 'update'])->name('media.update');
            Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

            // Autorización server-side para el módulo de contenido existente.
            Route::get('/news', [NewsController::class, 'index'])->name('news.index');
            Route::post('/news', [NewsController::class, 'store'])->name('news.store');
            Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
            Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
        });

        Route::middleware(EnsureMailSettingsAdmin::class)->prefix('configuracion')->name('settings.')->group(function () {
            Route::get('/correo', [MailSettingsController::class, 'edit'])->name('mail.edit');
            Route::put('/correo', [MailSettingsController::class, 'update'])
                ->middleware('throttle:mail-settings-update')
                ->name('mail.update');
        });

        Route::middleware('platform-admin')->prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::put('/usuarios/{user}', [RoleController::class, 'assign'])->name('assign');
        });
    });
});
