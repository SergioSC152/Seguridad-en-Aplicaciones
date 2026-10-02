<?php

namespace App\Console\Commands;

use App\Services\MailSettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ConfigureGmail extends Command
{
    protected $signature = 'cowapp:correo-gmail {--probar : Envía un correo de prueba a la misma cuenta emisora}';

    protected $description = 'Configura Gmail SMTP con contraseña oculta y almacenamiento cifrado';

    public function handle(MailSettingsService $settings): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Ejecuta este comando en una terminal interactiva. No admite contraseñas como argumentos.');

            return self::FAILURE;
        }

        $this->info('Se actualizará la configuración SMTP guardada, que tiene prioridad sobre .env.');
        $email = trim((string) $this->ask('Gmail emisor', config('cowapp.mail_settings_admin_email')));
        if (Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:255']])->fails()) {
            $this->error('Introduce una dirección de correo válida.');

            return self::FAILURE;
        }

        try {
            // No permitir que un terminal sin soporte de entrada oculta muestre el secreto.
            $password = preg_replace('/\s+/', '', (string) $this->secret('Contraseña de aplicación Google (entrada oculta)', false));
        } catch (Throwable) {
            $this->error('La terminal no permite entrada oculta. Usa una terminal interactiva compatible o el campo de contraseña del panel.');

            return self::FAILURE;
        }

        if (! preg_match('/^[a-zA-Z0-9]{16}$/', $password)) {
            $this->error('Se requieren los 16 caracteres de la contraseña de aplicación de Google, no la contraseña normal.');

            return self::FAILURE;
        }

        try {
            $settings->save([
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'username' => $email,
                'password' => $password,
                'encryption' => 'tls',
                'from_address' => $email,
                'from_name' => 'CowApp',
            ]);
        } catch (Throwable $exception) {
            $this->error('No se pudo guardar la configuración. Comprueba las migraciones y la conexión a la base de datos.');
            $this->line('Clase de error: '.$exception::class);

            return self::FAILURE;
        } finally {
            unset($password);
        }

        $this->info('SMTP guardado con contraseña cifrada. No se modificó .env ni se creó una cuenta administradora.');
        if (! $this->option('probar')) {
            $this->comment('Sin envío de prueba; todavía no se ha comprobado la conexión con Gmail.');

            return self::SUCCESS;
        }

        try {
            Mail::mailer('smtp')->raw('Prueba de configuración SMTP de CowApp. Este mensaje no contiene códigos de recuperación.', function ($message) use ($email) {
                $message->to($email)->subject('CowApp: prueba SMTP');
            });
        } catch (Throwable $exception) {
            $this->error('La configuración quedó guardada, pero el envío falló.');
            $this->line('Clase de error: '.$exception::class);
            // Clasificar sin imprimir respuestas SMTP que puedan contener datos sensibles.
            $message = strtolower($exception->getMessage());
            if (str_contains($message, '535') || str_contains($message, '534') || str_contains($message, 'authenticate')) {
                $this->error('Gmail rechazó la autenticación. Revisa usuario, verificación en dos pasos y contraseña de aplicación.');
            } elseif (str_contains($message, 'certificate') || str_contains($message, 'crypto') || str_contains($message, 'ssl')) {
                $this->error('Fallo TLS/certificado. Revisa OpenSSL y certificados CA de PHP; no desactives la verificación TLS.');
            } elseif (str_contains($message, 'connection') || str_contains($message, 'timed out') || str_contains($message, 'getaddrinfo')) {
                $this->error('No se pudo conectar. Revisa Internet, DNS y salida al puerto 587 en firewall/antivirus.');
            } else {
                $this->error('Revisa disponibilidad del proveedor y que permita enviar con esta cuenta.');
            }

            return self::FAILURE;
        }

        $this->info('Gmail aceptó el envío de prueba. Comprueba Bandeja de entrada y Spam.');
        $this->comment('Después solicita recuperación con una cuenta registrada; aceptar el envío no garantiza entrega en la bandeja de entrada.');

        return self::SUCCESS;
    }
}
