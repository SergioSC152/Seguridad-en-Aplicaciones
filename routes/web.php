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
Route::get('/', PublicPortalController::class)->name('home');

// Módulo de Contacto y Envío de Correo SMTP (Pasos 6.5, 6.8)
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contacto', [ContactController::class, 'send'])->name('contact.send');

// Rutas de Autenticación
Route::middleware('guest')->group(function () {
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
        ->middleware('throttle:login')
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
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    // Módulo Biblioteca Multimedia (Reto A / Pasos 5.4, 5.5, 5.7, 5.13)
    Route::prefix('admin')->name('admin.')->group(function () {
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
